<?php
namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\HistoriqueMedical;
use App\Entity\Hospital;
use App\Entity\Meeting;
use App\Entity\Patient;
use App\Entity\User;
use App\Services\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use App\Services\MailerService;

/**
 * Controleur principal de l'application
 * 
 * @author Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/main')]
class AppController extends AbstractController
{

    private $toolkit;
    private $entityManager;
    private $serializer;
    private $security;

    public function __construct(Toolkit $toolKit, EntityManagerInterface $entityManager,  SerializerInterface $serializer, Security $security)
    {
        $this->toolkit = $toolKit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->security = $security;
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
            'Entity1' => [
                'class' => 'App\Entity\Entity1',
                'searchFields' => ['first_name', 'last_name', 'cashier_short_code'],
                'serializationGroup' => 'driving_license:read'
            ],
            'Entity2' => [
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
        $qb = $this->entityManager->getRepository($entityClass)->createQueryBuilder('e');
        
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

    /**
     * Archivage d'une entité
     * 
     * 
     * @param string $entity_name
     * @param int $id
     * @return JsonResponse
     * 
     * @author Michel MIYALOU <michelmiyalou0@gmail.com>
     * */
    #[Route('/archivage/{entity_name}/{id}', name: 'app_archivage', methods: ['GET'])]
    function archiver($entity_name, int $id): JsonResponse
    {

        if (!$this->security->isGranted('ROLE_SUPER_ADMIN_SIS') && !$this->security->isGranted('ROLE_ADMIN_SIS')
            && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        $data = [
            "doctor" => "Doctor",
            "adminsis" => "SisAdmin",
            "hopital" => "Hospital",
        ];

        if (!array_key_exists($entity_name, $data)) {
            return new JsonResponse(['message' => 'Entité non trouvée', 'code' => 404], Response::HTTP_NOT_FOUND);
        }

        // Récupération de l'ID  par la methode ExistRepository
        $entity = $this->toolkit->ExistRepository($data, $entity_name, $id);

        if (!$entity) {
            return new JsonResponse(
                ['message' => $entity_name.' non trouvé(e)', 'code' => 404], 
                Response::HTTP_NOT_FOUND
            );
        }

        // Archivage de l'entité
        $entity->setIsArchived(true);
        
        $this->entityManager->flush();

        return new JsonResponse(
            ['message' => $entity_name.' archivé(e) avec succès'], 
            Response::HTTP_OK
        );
    }

    /**
     * Suspension d'une entité
     * 
     * 
     * @param string $entity_name
     * @param int $id
     * @return JsonResponse
     * 
     * @author Michel MIYALOU <michelmiyalou0@gmail.com>
     * */
    #[Route('/suspendre/{entity_name}/{id}', name: 'app_suspension', methods: ['GET'])]
    function suspendu($entity_name, int $id): JsonResponse
    {

        if (!$this->security->isGranted('ROLE_SUPER_ADMIN_SIS') && !$this->security->isGranted('ROLE_ADMIN_SIS')
            && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        $data = [
            "doctor" => "Doctor",
            "adminsis" => "SisAdmin",
        ];

        if (!array_key_exists($entity_name, $data)) {
            return new JsonResponse(['message' => 'Entité non trouvée', "code" => 404], Response::HTTP_NOT_FOUND);
        }

        // Récupération de l'ID  par la methode ExistRepository
        $entity = $this->toolkit->ExistRepository($data, $entity_name, $id);

        if (!$entity) {
            return new JsonResponse(
                ['message' => $entity_name.' non trouvé(e)', 'code' => 404], 
                Response::HTTP_NOT_FOUND
            );
        }

        // Archivage de l'entité
        $entity->setIsSuspended(true);
        
        $this->entityManager->flush();

        return new JsonResponse(
            ['message' => $entity_name.' suspendu(e) avec succès'], 
            Response::HTTP_OK
        );
    }

    #[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
    public function send(MailerService $mailer, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $mailer->sendWelcomeEmail(
            'lieloumloum@gmail.com',
            'Orphée Lié',
        );
        return $this->json(['status' => 'email_sent']);
    }
        /**
     * Crée un patient et un utilisateur (User) associé, ou lie un patient à un utilisateur existant.
     * 
     * Cette route permet à un agent hospitalier de créer un nouveau patient dans le système.
     * Si l'utilisateur (User) n'existe pas, il est créé automatiquement avec les informations fournies.
     * Si l'utilisateur existe déjà, il est simplement lié au patient.
     * 
     * @Route("/agentCreate", name="agent_create", methods={"POST"})
     * 
     * @param Request $request La requête HTTP contenant les informations du patient et de l'utilisateur.
     * 
     * @author Daryon Rocknes <daryonrocknes@icloud.com>
     */
    #[Route('/agentCreate', name: 'agent_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            $data["password"] = $data["password"] ?? '123456789';

            // Début de la transaction
            $this->entityManager->beginTransaction();

            // Vérification si le User existe déjà (par email ou téléphone)
            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'email' => $data['email']
            ]);

            // Si le User n'existe pas, on le crée
            if (!$user) {
                $user = new User();
                $user->setEmail($data['email']);
                $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
                $user->setRoles(["ROLE_PATIENT"]);
                $user->setFirstName($data['first_name']);
                $user->setLastName($data['last_name']);
                $user->setNickname($data['nickname'] ?? null);
                $user->setTel($data['tel']);
                $user->setBirth($data['birth'] ? new \DateTime($data['birth']) : null);
                $user->setGender($data['gender']);
                $user->setAddress($data['address'] ?? null);
                $user->setImage($data['image'] ?? null);

                // Persist et flush pour le User
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            // Création de l'entité Patient lié à ce User (sans les champs spécifiques)
            $patient = new Patient();
            $patient->setUser($user); 

            // Définir des valeurs par défaut pour tous les champs non-nullables
            $patient->setPoids(0);
            $patient->setTaille(0);
            $patient->setGroupeSanguins('NULL');
            $patient->setSignalerCommeDecedé(0);
            $patient->setNomUrgence('NULL');
            $patient->setAdresseUrgence('NULL');
            $patient->setNumeroUrgence('NULL');

            // Persist et flush pour le Patient
            $this->entityManager->persist($patient);
            $this->entityManager->flush();

            // Commit de la transaction
            $this->entityManager->commit();

            // Sérialisation et retour de la réponse
            $response = $this->serializer->serialize($patient, 'json', ['groups' => 'patient:read']);
            $response = json_decode($response, true);

            return $this->json(['data' => $response, 'code' => 200, 'message' => "Patient et User créés ou liés avec succès"], Response::HTTP_OK);

        } catch (\Throwable $th) {
            // Annulation de la transaction en cas d'erreur
            $this->entityManager->rollback();
            return new JsonResponse(['code' => 500, 'message' => "Erreur interne du serveur : " . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
        /**
     * Récupère les rendez-vous (meetings) d’un médecin pour un hôpital donné.
     *
     * Accessible uniquement aux utilisateurs ayant le rôle ROLE_DOCTOR.
     * Vérifie que le médecin est bien rattaché à l’hôpital passé dans l’URL.
     *
     * @Route("/api/doctor/meetings/hospital/{hospitalId}", name="doctor_meetings_by_hospital", methods={"GET"})
     *
     * @param int $hospitalId L’identifiant de l’hôpital à filtrer
     * @param Request $request La requête HTTP (utile pour la pagination, les filtres éventuels, etc.)
     *
     * @return JsonResponse
     * - 200 : Liste paginée des rendez-vous
     * - 403 : Accès refusé (rôle ou hôpital non autorisé)
     * - 404 : Médecin ou hôpital introuvable
     * - 500 : Erreur interne
     * @author Daryon Rocknes <daryonrocknes@icloud.com>
     */
    #[Route('/doctor/meetings/{hospitalId}', name: 'doctor_meetings_by_hospital', methods: ['GET'])]
    public function getMeetingsByHospital(int $hospitalId, Request $request): JsonResponse
    {
        try {
            // Vérification que l'utilisateur a bien le rôle DOCTOR
            if (!$this->security->isGranted('ROLE_DOCTOR')) {
                return new JsonResponse([
                    'code' => 403,
                    'message' => "Accès réservé aux médecins"
                ], JsonResponse::HTTP_FORBIDDEN);
            }
    
            // Récupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);
    
            // Récupération de l'entité Doctor
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);
            if (!$doctor) {
                return new JsonResponse([
                    'code' => 404,
                    'message' => "Médecin introuvable"
                ], JsonResponse::HTTP_NOT_FOUND);
            }
    
            // Récupération de l'hôpital demandé
            $hospital = $this->entityManager->getRepository(Hospital::class)->find($hospitalId);
            if (!$hospital) {
                return new JsonResponse([
                    'code' => 404,
                    'message' => "Hôpital introuvable"
                ], JsonResponse::HTTP_NOT_FOUND);
            }
    
            // Vérification de l'appartenance du meeting à ce médecin et cet hôpital
            $meetings = $this->entityManager->getRepository(Meeting::class)->findBy([
                'doctor' => $doctor,
                'hospital' => $hospital
            ]);
    
            // Si aucun meeting trouvé
            if (!$meetings) {
                return new JsonResponse([
                    'code' => 404,
                    'message' => "Aucun meeting trouvé pour ce médecin dans cet hôpital"
                ], JsonResponse::HTTP_NOT_FOUND);
            }
    
            // Optionnel : filtre par date si présent dans les query params
            $date = $request->query->get('date');
            if ($date) {
                $meetings = array_filter($meetings, function($meeting) use ($date) {
                    return $meeting->getDate()->format('Y-m-d') === $date;
                });
            }
    
            // Construction de la réponse
            $response = array_map(function($meeting) {
                return [
                    'id' => $meeting->getId(),
                    'doctor' => $meeting->getDoctor()->getUser()->getFirstName() . ' ' . $meeting->getDoctor()->getUser()->getLastName(),
                    'hospital' => $meeting->getHospital()->getName(),
                    'patient' => $meeting->getPatientId()->getUser()->getFirstName() . ' ' . $meeting->getPatientId()->getUser()->getLastName(),
                    'date' => $meeting->getDate()->format('Y-m-d H:i'),
                    // Autres champs à retourner
                ];
            }, $meetings);
    
            return new JsonResponse($response, JsonResponse::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse([
                'code' => 500,
                'message' => 'Erreur interne : ' . $th->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/mon-historique', name: 'HistoriqueMedical_patient', methods: ['GET'])]
    public function monHistorique(Request $request): Response
    {
        try {
            // Vérifie que l'utilisateur est bien un patient
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé."], Response::HTTP_FORBIDDEN);
            }

            // Récupère l'utilisateur connecté
            $user = $this->toolkit->getUser($request);

            // Trouve le patient associé à cet utilisateur
            $patient = $this->entityManager->getRepository(Patient::class)->findOneBy(['user' => $user]);

            if (!$patient) {
                return new JsonResponse(['code' => 404, 'message' => "Aucun patient associé à cet utilisateur."], Response::HTTP_NOT_FOUND);
            }

            // Récupère l'historique médical du patient
            $historiqueMedical = $this->entityManager->getRepository(HistoriqueMedical::class)
                ->findOneBy(['patient' => $patient]);

            if (!$historiqueMedical) {
                return new JsonResponse(['code' => 404, 'message' => "Aucun historique médical trouvé pour ce patient."], Response::HTTP_NOT_FOUND);
            }

            // Sérialise les données avec les consultations associées
            $data = $this->serializer->serialize($historiqueMedical, 'json', ['groups' => 'HistoriqueMedical:read']);

            return new JsonResponse(["data" => json_decode($data, true), "code" => 200], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return new JsonResponse([
                'code' => 500,
                'message' => "Erreur interne : " . $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}