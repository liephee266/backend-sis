<?php

namespace App\Controller;

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
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des AgentHopitals avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'User', 'user:read', $filtre);

        // Retour d'une réponse JSON avec les AgentHopitals et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
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
    public function show(User $agenthopital): Response
    {

        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        // Sérialisation de l'entité AgentHopital en JSON avec le groupe de sérialisation 'AgentHopital:read'
        $agenthopital = $this->serializer->serialize($agenthopital, 'json', ['groups' => 'agent_hopital:read']);
    
        // Retour de la réponse JSON avec les données de l'AgentHopital et un code HTTP 200
        return new JsonResponse(["data" => json_decode($agenthopital, true), "code" => 200], Response::HTTP_OK);
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

        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL')) {
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
            'roles' => ["ROLE_AGENT_HOPITAL"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        $errors = $this->genericEntityManager->persistUser($user_data, $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "Agent hopital crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'agent hopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
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

        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        $data['birth'] = new \DateTime($data['birth']);

        // Appel à la méthode persistEntity pour mettre à jour l'entité AgentHopital dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\User", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "AgentHopital modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'AgentHopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        // Suppression de l'entité AgentHopital passée en paramètre
        $entityManager->remove($agenthopital);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "AgentHopital supprimé avec succès"], Response::HTTP_OK);
    }
}
