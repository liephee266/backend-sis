<?php

namespace App\Controller;

use App\Entity\AgentHospital;
use App\Entity\HospitalAdmin;
use App\Entity\User;
use App\Services\Toolkit;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des AgentHopital
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/agenthopitals')]
class AgentHopitalController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit, Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des AgentHopital
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'agenthopital_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN_HOSPITAL') && !$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                # code...
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
    
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];
    
            $user = $this->toolkit->getUser($request);
             // Si c'est un admin hospitalier, on filtre les agent_d'acceulls liés à son hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
    
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                    ->findOneBy(['user' => $user]);
    
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        "message" => "Aucun hôpital associé à cet admin",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
    
                $hospital = $hospitalAdmin->getHospital();
    
                // Récupérer les IDs des agents hopital liés à cet hôpital via la table DoctorHospital
                $agentHospitalRepository = $this->entityManager->getRepository(AgentHospital::class);
                $doctorHops = $agentHospitalRepository->findBy(['hospital' => $hospital]);
    
                $doctorIds = array_map(function ($dh) {
                    return $dh->getId();
                }, $doctorHops);
    
                // Ajouter ce filtre pour n'afficher que les agents hospital de cet hôpital
                $filtre['id'] = $doctorIds;
            }
    
            // Récupération des AgentHopitals avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'User', 'user:read', $filtre);
    
            // Retour d'une réponse JSON avec les AgentHopitals et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des AgentHopitals" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Affichage d'un AgentHopital par son ID
     *
     * @param AgentHopital $AgentHopital
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenthopital_show', methods: ['GET'])]
    public function show(User $agenthopital, Request $request): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                # code...
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
    
            // Si c’est un admin hospitalier, vérifier que le docteur appartient bien à son hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
    
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                    ->findOneBy(['user' => $user]);
    
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        "message" => "Aucun hôpital trouvé pour cet admin.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
    
                $hospital = $hospitalAdmin->getHospital();
    
                // Vérifier que le docteur appartient bien à cet hôpital
                $agentHospital = $this->entityManager->getRepository(AgentHospital::class)
                    ->findOneBy([
                        'user' => $agenthopital,
                        'hospital' => $hospital
                    ]);
    
                if (!$agentHospital) {
                    return new JsonResponse([
                        "message" => "Cet agent hopital n'est pas rattaché à votre hôpital.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
            }
    
            // Sérialisation de l'entité AgentHopital en JSON avec le groupe de sérialisation 'AgentHopital:read'
            $agenthopital = $this->serializer->serialize($agenthopital, 'json', ['groups' => 'user:read']);
        
            // Retour de la réponse JSON avec les données de l'AgentHopital et un code HTTP 200
            return new JsonResponse(["data" => json_decode($agenthopital, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de l'AgentHopital" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Création d'un nouvel AgentHopital
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'agenthopital_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                # code...
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
    
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Début de la transaction
            $this->entityManager->beginTransaction();
    
            // Création du User
            $user_data = [
                'email' => $data['email'],
                'password' => $data['password'],
                'roles' => ["ROLE_AGENT_HOSPITAL"],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'tel' => $data['tel'],
                'birth' => new \DateTime($data['birth']),
                'gender' => $data['gender'],
                'address' => $data['address'],
            ];
            
            $errors = $this->genericEntityManager->persistEntityUser("App\Entity\AgentHospital", $user_data, $data);
    
            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
                $this->entityManager->commit();
                return $this->json(['code' => 200, 'message' => "Agent hopital crée avec succès"], Response::HTTP_OK);
            }
    
            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'agent hopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'agent hopital" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un AgentHopital par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenthopital_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
                # code...
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
    
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
            }
    
            // Récupération de l'agent hospital à modifier
            $agenthopital = $this->entityManager->getRepository(AgentHospital::class)->find($id);
            if (!$agenthopital) {
                return $this->json(['code' => 404, 'message' => "Agent Hospital introuvable"], Response::HTTP_NOT_FOUND);
            }
    
            // Vérification que l'admin hospitalier modifie un agent hospital de son hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $user]);
    
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        "message" => "Aucun hôpital trouvé pour cet admin.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
    
                $hospital = $hospitalAdmin->getHospital();
    
                // Vérification via AgentHospitalHospital
                $doctorHospital = $this->entityManager->getRepository(AgentHospital::class)->findOneBy([
                    'agentHospital' => $agenthopital,
                    'hospital' => $hospital
                ]);
    
                if (!$doctorHospital) {
                    return new JsonResponse([
                        "message" => "Cet agent hospital n'appartient pas à votre hôpital.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
            }
    
            // Préparer les données de mise à jour
            $data['id'] = $id;
    
            // Appel à la méthode persistEntity pour mettre à jour l'entité AgentHopital dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\AgentHospital", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['code' => 200, 'message' => "AgentHopital modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'AgentHopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'AgentHopital" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un AgentHopital par son ID
     * 
     * @param AgentHopital $AgentHopital
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenthopital_delete', methods: ['DELETE'])]
    public function delete(User $agenthopital, EntityManagerInterface $entityManager): Response
    {
        try {
            if (
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
            ) {
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité AgentHopital passée en paramètre
            $entityManager->remove($agenthopital);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "AgentHopital supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'AgentHopital" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
