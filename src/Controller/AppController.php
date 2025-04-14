<?php
namespace App\Controller;

use App\Services\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur principal de l'application
 * 
 * @author Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/main')]
class AppController extends AbstractController
{

    private $toolkit;
    private $defaultEntityManager;
    private $serializer;

    public function __construct(Toolkit $toolKit, EntityManagerInterface $defaultEntityManager,  SerializerInterface $serializer)
    {
        $this->toolkit = $toolKit;
        $this->defaultEntityManager = $defaultEntityManager;
        $this->serializer = $serializer;
    }

    /**
     * Recherche des data select pour les entite
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/data-select', name: 'app_app_data_select', methods: ['POST'])]
    public function dataSelect(Request $request): JsonResponse
    {
        // Récupérer les données de la requête JSON
        $data = json_decode($request->getContent(), true);
        // Récupérer les filtres, s'ils existent dans la requête (par exemple id_administration)
        // Formatter les entités sélectionnées avec les filtres passés
        $dataSelectEntity = $this->toolkit->formatArrayEntity($data['data_select']);
        $portail = $data['portail'] ?? null;
        // Appliquer la méthode formatArrayEntityLabel en passant également les filtres
        $filters=[];
        $allSelectEntity = $this->toolkit->formatArrayEntityLabel($dataSelectEntity, $filters, $portail);
        // Retourner la réponse avec les données filtrées
        return new JsonResponse($allSelectEntity, Response::HTTP_OK);        
    }

    #[Route('/globalSearch', name: 'app_search', methods: ['POST'])]
        /**
     * Recherche un service en fonction des critères fournis dans la requête.
     * 
     * @param Request $request Requête contenant les paramètres de recherche.
     * @return JsonResponse Réponse JSON contenant les résultats de la recherche ou un message d'erreur.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>

     */
    public function searchService(Request $request): JsonResponse
    {
        // Récupération de la configuration de recherche
        $searchConfig = $this->getSearchConfiguration();
        $searchParams = $this->extractSearchParameters($request);
        
        // Détermination des entités à rechercher en fonction du type de document
        $entities = $this->getSearchableEntities($searchParams['documentType']);
        if (!$entities) {
            return new JsonResponse(
                ["message" => "Type spécifié invalide."],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $results = [];
        foreach ($entities as $entityKey => $entityConfig) {
            // Exécution de la recherche pour chaque entité correspondante
            $searchResults = $this->performEntitySearch(
                $entityConfig['class'],
                $searchParams,
                $entityConfig['searchFields'],
                $entityConfig['serializationGroup']
            );
            $results[$entityKey] = $searchResults;
        }

        // Vérification si aucun résultat n'a été trouvé
        if ($this->areAllResultsEmpty($results)) {
            return new JsonResponse(
                ["message" => "Aucun résultat trouvé pour les critères spécifiés."],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse($results, Response::HTTP_OK);
    }

    /**
     * Retourne la configuration des recherches disponibles.
     * 
     * @return array Configuration des entités et leurs critères de recherche.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    private function getSearchConfiguration(): array
    {
        return [
            'driving_license' => [
                'class' => 'App\Entity\Entity1',
                'searchFields' => ['first_name', 'last_name', 'cashier_short_code'],
                'serializationGroup' => 'driving_license:read'
            ],
            'registration_card' => [
                'class' => 'App\Entity\Entity2',
                'searchFields' => ['owner_first_name', 'owner_last_name', 'cashier_short_code'],
                'serializationGroup' => 'registration_card:read'
            ]
        ];
    }

    /**
     * Extrait les paramètres de recherche à partir de la requête HTTP.
     * 
     * @param Request $request Requête contenant les données JSON.
     * @return array Paramètres extraits de la requête.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    private function extractSearchParameters(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $dateRange = $data['date_range'] ?? [];
        
        return [
            'searchTerm' => $data['searchTerm'] ?? null,
            'documentType' => $data['document_type'] ?? null,
            'startDate' => $dateRange['from'] ?? null,
            'endDate' => $dateRange['to'] ?? null
        ];
    }

    /**
     * Récupère les entités à rechercher en fonction du type de document spécifié.
     * 
     * @param string|null $documentType Type de document à rechercher.
     * @return array|null Tableau des entités correspondantes ou null si aucune correspondance.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    private function getSearchableEntities(?string $documentType): ?array
    {
        $config = $this->getSearchConfiguration();
        
        if ($documentType === 'all') {
            return $config;
        }
        
        return isset($config[$documentType]) ? [$documentType => $config[$documentType]] : null;
    }

    /**
     * Exécute la recherche d'entités en fonction des paramètres spécifiés.
     * 
     * @param string $entityClass Classe de l'entité à rechercher.
     * @param array $searchParams Paramètres de recherche.
     * @param array $searchFields Champs dans lesquels effectuer la recherche.
     * @param string $serializationGroup Groupe de sérialisation pour le formatage des résultats.
     * @return array Résultats de la recherche sous forme de tableau JSON.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    private function performEntitySearch(
        string $entityClass,
        array $searchParams,
        array $searchFields,
        string $serializationGroup
    ): array {
        $qb = $this->defaultEntityManager->getRepository($entityClass)->createQueryBuilder('e');
        
        // Ajout des conditions de recherche basées sur le terme de recherche
        if (!empty($searchParams['searchTerm'])) {
            $conditions = [];
            foreach ($searchFields as $field) {
                $conditions[] = $qb->expr()->like("e.$field", ':searchTerm');
            }
            $qb->andWhere($qb->expr()->orX(...$conditions))
               ->setParameter('searchTerm', '%' . $searchParams['searchTerm'] . '%');
        }

        // Ajout des conditions basées sur la plage de dates
        if (!empty($searchParams['startDate']) && !empty($searchParams['endDate'])) {
            $qb->andWhere('e.created_at BETWEEN :start_date AND :end_date')
               ->setParameter('start_date', new \DateTime($searchParams['startDate']))
               ->setParameter('end_date', new \DateTime($searchParams['endDate']));
        }

        $results = $qb->getQuery()->getResult();
        
        return json_decode($this->serializer->serialize(
            $results,
            'json',
            ['groups' => [$serializationGroup]]
        ));
    }

    /**
     * Vérifie si tous les résultats de recherche sont vides.
     * 
     * @param array $results Tableau contenant les résultats des différentes recherches.
     * @return bool Retourne true si aucun résultat n'est trouvé, sinon false.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    private function areAllResultsEmpty(array $results): bool
    {
        foreach ($results as $result) {
            if (!empty($result)) {
                return false;
            }
        }
        return true;
    }


    function nettoyer_json($json) {
        // Supprimer les retours à la ligne et les espaces
        $json_nettoye = str_replace(array("\n", "\r", " ", "\t"), '', $json);
        return $json_nettoye;
    }


    /**
     * Cette méthode permet de récupérer les données pour PowerBi
     * 
     * 
     * @param Request $request
     * @param string $entity_name
     * @return JsonResponse
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     * */
    #[Route('/powerbi/{entity_name}', name: 'app_powerbi_route', methods: ['GET'])]
    function dataPowerBi(Request $request, $entity_name): JsonResponse
    {
        if ($_ENV['POWER_BI_CLIENT_ID'] !== $request->headers->get('Authorization')) {
            return new JsonResponse(['message' => 'Accès non autorisé'], Response::HTTP_BAD_REQUEST);
        }
        $datapowerbi = [
            // 'users' => 'User',
        ];

        if (!array_key_exists($entity_name, $datapowerbi)) {
            return new JsonResponse(['message' => 'Entité non trouvée'], Response::HTTP_NOT_FOUND);
        }
        $response = $this->toolkit->getPagitionOption($request, $datapowerbi[$entity_name],  'powerbi');
        return new JsonResponse($response, Response::HTTP_OK);        
    }
     
}