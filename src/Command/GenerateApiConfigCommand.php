<?php

namespace App\Command;

use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use App\Attribute\ApiEntity;

#[AsCommand(name: 'app:generate-api-config', description: 'Génère automatiquement un fichier JSON de configuration des endpoints API')]
class GenerateApiConfigCommand extends Command
{
    protected static $defaultName = 'app:generate-api-config';
    protected static $defaultDescription = 'Automatically generates a JSON configuration file for API endpoints';

    private readonly RouterInterface $router;
    private readonly EntityManagerInterface $em;

    public function __construct(RouterInterface $router, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->router = $router;
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'Chemin de sortie du fichier JSON', 'public/api-config.json')
            ->addOption('prefix', 'p', InputOption::VALUE_OPTIONAL, 'Préfixe des routes à inclure (par défaut: /api)', '/api');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $routes = $this->router->getRouteCollection();
        $config = ['title' => 'API Documentation','description' => 'Généré automatiquement via app:generate-api-config','categories' => []];

        $categories = [];
        $prefix = $input->getOption('prefix');
        foreach ($routes as $name => $route) {
            if (!str_starts_with($route->getPath(), $prefix)) continue;

            $controller = $route->getDefault('_controller');
            if (!$controller || !str_contains($controller, '::')) continue;
            [$class, $method] = explode('::', $controller);

            // Catégorie par défaut
            $categoryName = 'General';
            $categoryDesc = 'Endpoints API';
            $parts = explode('\\', $class);
            if (count($parts) > 2) {
                $categoryName = str_replace('Controller', '', end($parts));
                $categoryDesc = 'Endpoints ' . $categoryName;
            }

            // Description et attributs de classe
            try {
                $ref = new ReflectionClass($class);
                if ($doc = $ref->getDocComment()) {
                    if (preg_match('/@Route\("([^"]+)"\)/', $doc, $m)) {
                        $categoryDesc .= ' (' . $m[1] . ')';
                    }
                }
                foreach ($ref->getAttributes() as $attr) {
                    if (str_contains($attr->getName(), 'Route')) {
                        $args = $attr->getArguments();
                        if (isset($args[0])) {
                            $categoryDesc .= ' (' . $args[0] . ')';
                            break;
                        }
                    }
                }
            } catch (\Exception) {}

            $routeInfo = ['id' => $name,'path' => $route->getPath(),'method' => implode(', ', $route->getMethods()),'description' => $this->extractDescription($class, $method),'params' => []];

            if (array_intersect(['POST', 'PUT'], $route->getMethods())) {
                if ($entityClass = $this->guessEntityForRoute($class, $method)) {
                    if (class_exists($entityClass)) {
                        $routeInfo['params'] = $this->getEntityFields($entityClass);
                    }
                }
            }

            $categories[$categoryName] = ['name' => $categoryName,'description' => $categoryDesc,'routes' => array_merge($categories[$categoryName]['routes'] ?? [], [$routeInfo])];
        }

        $config['categories'] = array_values($categories);
        $outputFile = $input->getOption('output');
        if (!str_starts_with($outputFile, '/') && !(strlen($outputFile) > 1 && ctype_alpha($outputFile[0]) && $outputFile[1] === ':')) {
            $outputFile = realpath(__DIR__ . '/../../') . '/' . ltrim($outputFile, '/');
        }
        if (!is_dir(dirname($outputFile))) mkdir(dirname($outputFile), 0755, true);

        if (!file_put_contents($outputFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
            $io->error(sprintf('Impossible d\'écrire le fichier %s', $outputFile));
            return Command::FAILURE;
        }

        $io->success(sprintf('Fichier api-config.json généré dans %s', $outputFile));
        return Command::SUCCESS;
    }

    private function extractDescription(string $class, string $method): string
    {
        try {
            $ref = new ReflectionClass($class);
            if (!$ref->hasMethod($method)) return '';
            $doc = $ref->getMethod($method)->getDocComment() ?: '';
            if (preg_match('/^\s*\*\s*(.+?)\s*$/m', $doc, $m)) return trim($m[1]);
            return '';
        } catch (\Exception) { return ''; }
    }

    private function guessEntityForRoute(string $class, string $method): ?string
    {
        try {
            $ref = new ReflectionClass($class);
            // 0. Attribut de classe
            $classAttrs = $ref->getAttributes(ApiEntity::class);
            if (!empty($classAttrs)) {
                $entity = $classAttrs[0]->newInstance()->entity;
                if ($entity) return $entity;
            }
            // 1. Attribut de méthode
            if ($ref->hasMethod($method)) {
                $methodRef = $ref->getMethod($method);
                $methodAttrs = $methodRef->getAttributes(ApiEntity::class);
                if (!empty($methodAttrs)) return $methodAttrs[0]->newInstance()->entity;
                // 2. ParamConverter
                foreach ($methodRef->getParameters() as $param) {
                    $paramType = $param->getType();
                    if ($paramType && !$paramType->isBuiltin()) {
                        $typeName = $paramType->getName();
                        if (class_exists($typeName) && !$this->em->getMetadataFactory()->isTransient($typeName)) {
                            return $typeName;
                        }
                    }
                }
            }
            // 3. Convention Controller→Entity
            if (preg_match('/([^\\]+)Controller$/', $class, $m)) {
                $entityClass = 'App\\Entity\\' . $m[1];
                if (class_exists($entityClass)) return $entityClass;
            }
            // 4. createX → Entity
            if (preg_match('/^create([A-Z][a-zA-Z0-9]*)/', $method, $m)) {
                $entityClass = 'App\\Entity\\' . $m[1];
                if (class_exists($entityClass)) return $entityClass;
            }
        } catch (\Exception) {}
        return null;
    }
    private function getEntityFields(string $entityClass): array
    {
        try {
            $meta = $this->em->getClassMetadata($entityClass);
            $fields = [];

            $refClass = new \ReflectionClass($entityClass);

            foreach ($meta->fieldMappings as $name => $info) {
                if ($meta->isIdentifier($name)) continue;

                $property = $refClass->hasProperty($name) ? $refClass->getProperty($name) : null;
                $attr = $property?->getAttributes(\App\Attribute\ApiField::class)[0] ?? null;
                $auto = $attr ? $attr->newInstance()->auto : false;

                if ($auto) continue; // ← On exclut les champs automatiques

                $fields[] = [
                    'name' => $name,
                    'type' => $info['type'],
                    'required' => !($info['nullable'] ?? false),
                    'description' => $this->getFieldDescription($entityClass, $name)
                ];
            }

            foreach ($meta->associationMappings as $name => $mapping) {
                $property = $refClass->hasProperty($name) ? $refClass->getProperty($name) : null;
                $attr = $property?->getAttributes(\App\Attribute\ApiField::class)[0] ?? null;
                $auto = $attr ? $attr->newInstance()->auto : false;

                if ($auto) continue; // ← Pareil ici

                $fields[] = [
                    'name' => $name,
                    'type' => 'relation',
                    'relation_type' => $mapping['type'],
                    'target_entity' => $mapping['targetEntity'],
                    'required' => !($mapping['joinColumns'][0]['nullable'] ?? true),
                    'description' => $this->getFieldDescription($entityClass, $name)
                ];
            }

            return $fields;
        } catch (\Exception) {
            return [];
        }
    }
    private function getFieldDescription(string $entityClass, string $fieldName): string
    {
        try {
            $ref = new ReflectionClass($entityClass);
            if ($ref->hasProperty($fieldName)) {
                $doc = $ref->getProperty($fieldName)->getDocComment() ?: '';
                if (preg_match('/@var\s+[^\s]+\s+(.+)/', $doc, $m)) return trim($m[1]);
            }
            $getter = 'get' . ucfirst($fieldName);
            if ($ref->hasMethod($getter)) {
                $doc = $ref->getMethod($getter)->getDocComment() ?: '';
                if (preg_match('/@return\s+[^\s]+\s+(.+)/', $doc, $m)) return trim($m[1]);
            }
        } catch (\Exception) {}
        return '';
    }
}

// src/Attribute/ApiEntity.php
// <?php
// namespace App\Attribute;
// use Attribute;
//
// #[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
// class ApiEntity
// {
//     public function __construct(public string $entity) {}
// }
