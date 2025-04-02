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

    /**
     * Recherche globale sur les entités
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/globalSearch', name: 'app_search', methods: ['POST'])]
    public function searchService(Request $request): JsonResponse
    {
        // Récupérer les données du corps de la requête (JSON)
        $data = json_decode($request->getContent(), true);
        // Extraire les valeurs de l'objet JSON
        $searchTerm = $data['searchTerm'] ?? null;
        $documentType = $data['document_type'] ?? null;
        $dateRange = $data['date_range'] ?? null;
        $startDate = $dateRange['from'] ?? null;
        $endDate = $dateRange['to'] ?? null;

        // Initialiser les résultats pour les permis de conduire et les cartes grises
        $entity1Results = [];
        $entity2Results = [];

        // Vérifier les entités à rechercher
        $entities = match ($documentType) {
            'all' => ['App\Entity\Entity1'::class, 'App\Entity\Entity2'::class],
            default => null,
        };

        if (!$entities) {
            return new JsonResponse(
                ["message" => "Type non valide. Utilisez 'driving-license', 'registration-card' ou 'all'."],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Construire la requête et récupérer les résultats
        foreach ($entities as $entityClass) {
            $qb = $this->defaultEntityManager->getRepository($entityClass)->createQueryBuilder('e');
            // Ajouter la condition de recherche par mot-clé (searchTerm)
            if (!empty($searchTerm)) {
                if ($entityClass === "App\Entity\Entity1"::class) {
                    $qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->like('e.first_name', ':searchTerm'),
                            $qb->expr()->like('e.last_name', ':searchTerm'),
                            $qb->expr()->like('e.cashier_short_code', ':searchTerm')
                        )
                    );
                } elseif ($entityClass === 'App\Entity\Entity2'::class) {
                    $qb->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->like('e.owner_first_name', ':searchTerm'),
                            $qb->expr()->like('e.owner_last_name', ':searchTerm'),
                            $qb->expr()->like('e.cashier_short_code', ':searchTerm')
                        )
                    );
                }
                $qb->setParameter('searchTerm', '%' . $searchTerm . '%');
            }

            // Ajouter la condition de date (si spécifiée)
            if (!empty($startDate) && !empty($endDate)) {
                $qb->andWhere('e.created_at BETWEEN :start_date AND :end_date')
                    ->setParameter('start_date', new \DateTime($startDate))
                    ->setParameter('end_date', new \DateTime($endDate));
            }

            // Exécuter la requête et séparer les résultats en fonction de l'entité
            $results = $qb->getQuery()->getResult();
            if ($entityClass === 'App\Entity\Entity1'::class) {
                $entity1Results = array_merge($entity1Results, $results);
            } elseif ($entityClass === 'App\Entity\Entity2'::class) {
                $entity2Results = array_merge($entity2Results, $results);
            }
        }

        // Si les deux tableaux sont vides, retourner un message
        if (empty($entity1Results) && empty($entity2Results)) {
            return new JsonResponse(
                ["message" => "Aucun résultat trouvé pour les critères spécifiés."],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        // Sérialiser les résultats pour chaque type de document
        $entity1ResultsData = $this->serializer->serialize(
            $entity1Results,
            'json',
            ['groups' => ['driving_license:read']]
        );

        $entity2ResultsData = $this->serializer->serialize(
            $entity2Results,
            'json',
            ['groups' => ['registration_card:read']]
        );

        // Structure de la réponse séparée par type de document
        $responseData = [
            'driving_license' => json_decode($entity1ResultsData), // Résultats pour le permis de conduire
            'registration_card' => json_decode($entity2ResultsData),    // Résultats pour la carte grise
        ];

        return new JsonResponse($responseData, Response::HTTP_OK);
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
            'biometric-datas' => 'BiometricData',
            'categorie-vehicules' => 'CategorieVehicule',
            'client_types' => 'ClientType',
            'departments' => 'Department',
            'documents' => 'Document',
            'driving-licenses' => 'DrivingLicense',
            'locations' => 'Location',
            'operation-type' => 'OperationType',
            'registration-cards' => 'RegistrationCard',
            'users' => 'User',
            'status-history' => 'StatusHistory'
        ];

        if (!array_key_exists($entity_name, $datapowerbi)) {
            return new JsonResponse(['message' => 'Entité non trouvée'], Response::HTTP_NOT_FOUND);
        }
        $response = $this->toolkit->getPagitionOption($request, $datapowerbi[$entity_name],  'powerbi');
        return new JsonResponse($response, Response::HTTP_OK);        
    }
}