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
            ->addOption(
                'output',
                'o',
                \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                'Chemin de sortie du fichier JSON',
                'public/api-config.json'
            )
            ->addOption(
                'prefix',
                'p',
                \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                'Préfixe des routes à inclure (par défaut: /api)',
                '/api'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $routes = $this->router->getRouteCollection();
        $config = [
            'title' => 'API Documentation',
            'description' => 'Généré automatiquement via app:generate-api-config',
            'categories' => [],
        ];

        // Regrouper routes par contrôleur ou par tag
        $categories = [];
        foreach ($routes as $name => $route) {
            // Filtrer les routes selon le préfixe spécifié
            $prefix = $input->getOption('prefix');
            if (!str_starts_with($route->getPath(), $prefix)) {
                continue;
            }

            $controller = $route->getDefault('_controller');
            if (!$controller || !str_contains($controller, '::')) {
                continue;
            }
            [$class, $method] = explode('::', $controller);

            // Extraire la catégorie à partir du contrôleur ou des annotations
            $categoryName = 'General';
            $categoryDesc = 'Endpoints API';
            
            // Essayer d'extraire la catégorie à partir du namespace du contrôleur
            $parts = explode('\\', $class);
            if (count($parts) > 2 && isset($parts[2])) {
                $categoryName = str_replace('Controller', '', $parts[2]);
                $categoryDesc = 'Endpoints ' . $categoryName;
            }
            
            // Vérifier si une annotation ou un attribut Route est présent sur la classe
            try {
                $ref = new ReflectionClass($class);
                
                // Vérifier les annotations (Symfony < 6)
                $classDoc = $ref->getDocComment();
                if ($classDoc && preg_match('/@Route\("([^"]+)"\)/', $classDoc, $matches)) {
                    $categoryDesc .= ' (' . $matches[1] . ')';
                }
                
                // Vérifier les attributs (Symfony 6+)
                $attributes = $ref->getAttributes();
                foreach ($attributes as $attribute) {
                    $name = $attribute->getName();
                    if (str_contains($name, 'Route')) {
                        $args = $attribute->getArguments();
                        if (!empty($args) && isset($args[0])) {
                            $categoryDesc .= ' (' . $args[0] . ')';
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de réflexion
            }
            
            $routeInfo = [
                'id' => $name,
                'path' => $route->getPath(),
                'method' => implode(', ', $route->getMethods()),
                'description' => $this->extractDescription($class, $method),
                'params' => [],
            ];

            // Si POST ou PUT, introspecter l'entité liée
            if (in_array('POST', $route->getMethods()) || in_array('PUT', $route->getMethods())) {
                $entityClass = $this->guessEntityForRoute($class, $method);
                if (class_exists($entityClass)) {
                    $routeInfo['params'] = $this->getEntityFields($entityClass);
                }
            }

            $categories[$categoryName]['name'] = $categoryName;
            $categories[$categoryName]['description'] = $categoryDesc;
            $categories[$categoryName]['routes'][] = $routeInfo;
        }

        $config['categories'] = array_values($categories);
        // Récupérer le chemin de sortie depuis l'option ou utiliser la valeur par défaut
        $outputFile = $input->getOption('output');
        
        // Convertir en chemin absolu si nécessaire
        if (!str_starts_with($outputFile, '/') && !preg_match('#^[A-Z]:\\#i', $outputFile)) {
            $outputPath = realpath(__DIR__ . '/../../') . '/' . ltrim($outputFile, '/');
        } else {
            $outputPath = $outputFile;
        }
        
        // Créer le répertoire parent si nécessaire
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Écrire le fichier avec gestion d'erreur
        if (file_put_contents(
            $outputPath,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        )) {
            $io->success(sprintf('Fichier api-config.json généré avec succès dans %s', $outputPath));
        } else {
            $io->error(sprintf('Impossible d\'écrire dans le fichier %s', $outputPath));
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    private function extractDescription(string $class, string $method): string
    {
        try {
            $ref = new ReflectionClass($class);
            if (!$ref->hasMethod($method)) {
                return '';
            }
            
            $methodRef = $ref->getMethod($method);
            $doc = $methodRef->getDocComment() ?: '';
            
            // Extraire la première ligne utile du docblock
            if (preg_match('/\*\s+(.*)\r?\n/', $doc, $m)) {
                return trim($m[1]);
            }
            
            // Vérifier les attributs Route (Symfony 6+)
            $attributes = $methodRef->getAttributes();
            foreach ($attributes as $attribute) {
                $name = $attribute->getName();
                if (str_contains($name, 'Route')) {
                    $args = $attribute->getArguments();
                    if (!empty($args) && isset($args[0])) {
                        return 'Route: ' . $args[0];
                    }
                }
            }
            
            return '';
        } catch (\Exception $e) {
            return '';
        }
    }

    private function guessEntityForRoute(string $class, string $method): ?string
    {
        try {
            // 1. Hypothèse : nom du contrôleur nommé XController indique l'entité X
            if (preg_match('/([^\\\\]+)Controller$/', $class, $m)) {
                $entityName = $m[1];
                $entityClass = 'App\\Entity\\' . $entityName;
                if (class_exists($entityClass)) {
                    return $entityClass;
                }
            }
            
            // 2. Essayer de déduire l'entité à partir du nom de la méthode (ex: createUser -> User)
            $methodPatterns = [
                '/^create([A-Z][a-zA-Z0-9]*)/',
                '/^update([A-Z][a-zA-Z0-9]*)/',
                '/^delete([A-Z][a-zA-Z0-9]*)/',
                '/^get([A-Z][a-zA-Z0-9]*)/',
                '/^post([A-Z][a-zA-Z0-9]*)/',
                '/^put([A-Z][a-zA-Z0-9]*)/',
            ];
            
            foreach ($methodPatterns as $pattern) {
                if (preg_match($pattern, $method, $matches)) {
                    $entityClass = 'App\\Entity\\' . $matches[1];
                    if (class_exists($entityClass)) {
                        return $entityClass;
                    }
                }
            }
            
            // 3. Analyser les paramètres de la méthode pour trouver des entités
            $ref = new \ReflectionClass($class);
            if ($ref->hasMethod($method)) {
                $methodRef = $ref->getMethod($method);
                foreach ($methodRef->getParameters() as $param) {
                    $paramType = $param->getType();
                    if ($paramType && !$paramType->isBuiltin()) {
                        $typeName = $paramType->getName();
                        if (str_starts_with($typeName, 'App\\Entity\\')) {
                            return $typeName;
                        }
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEntityFields(string $entityClass): array
    {
        try {
            $meta = $this->em->getClassMetadata($entityClass);
            $fields = [];
            
            // Ajouter les champs simples
            foreach ($meta->fieldMappings as $name => $info) {
                // Ignorer les clés primaires
                if ($meta->isIdentifier($name)) {
                    continue;
                }
                $fields[] = [
                    'name' => $name,
                    'type' => $info['type'],
                    'required' => !($info['nullable'] ?? false),
                    'description' => $this->getFieldDescription($entityClass, $name),
                ];
            }
            
            // Ajouter les relations
            foreach ($meta->associationMappings as $name => $mapping) {
                $fields[] = [
                    'name' => $name,
                    'type' => 'relation',
                    'relation_type' => $mapping['type'],
                    'target_entity' => $mapping['targetEntity'],
                    'required' => !($mapping['joinColumns'][0]['nullable'] ?? true),
                    'description' => $this->getFieldDescription($entityClass, $name),
                ];
            }
            
            return $fields;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getFieldDescription(string $entityClass, string $fieldName): string
    {
        try {
            $ref = new ReflectionClass($entityClass);
            
            // Chercher dans les propriétés
            if ($ref->hasProperty($fieldName)) {
                $prop = $ref->getProperty($fieldName);
                $doc = $prop->getDocComment();
                if ($doc && preg_match('/@var\s+[^\s]+\s+(.+)/', $doc, $matches)) {
                    return trim($matches[1]);
                }
            }
            
            // Chercher dans les méthodes getter
            $getterName = 'get' . ucfirst($fieldName);
            if ($ref->hasMethod($getterName)) {
                $method = $ref->getMethod($getterName);
                $doc = $method->getDocComment();
                if ($doc && preg_match('/@return\s+[^\s]+\s+(.+)/', $doc, $matches)) {
                    return trim($matches[1]);
                }
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
        
        return '';
    }
}
