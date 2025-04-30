<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Services\Toolkit;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Controleur pour la gestion des Conversation
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/conversations')]
class ConversationController extends AbstractController
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
     * Liste des Conversation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'Conversation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des rôles de l'utilisateur connecté
            if (
                !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT')
            ) {
                return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
            }
            // Récupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);

            if (!$user) {
                return $this->json([
                    'code' => 401,
                    'message' => 'Utilisateur non authentifié'
                ], Response::HTTP_UNAUTHORIZED);
            }
             $filtre = [
                'participants' => $user->getId(),
            ];
        
            $response = $this->toolkit->getPagitionOption($request, 'Conversation', 'conversation:read', $filtre);

            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->json([
                'code' => 500,
                'message' => "Erreur lors de la recherche des conversations : " . $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
   }

    /**
     * Affichage d'un Conversation par son ID
     *
     * @param Conversation $Conversation
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'Conversation_show', methods: ['GET'])]
    public function show(Conversation $conversation,Request $request): Response
    {
        // try {
            if (
                !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT')
            ) {
                return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
            }
            // Vérification de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);
            if (!$user) {
                return $this->json(['message' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
            }
            // Vérification si l'id de l'utilisateur connecté se trouvve dans le champ participant
            if (!in_array($user->getId(), $conversation->getParticipants())) {
                return $this->json(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
            }

            $messages = $this->entityManager->getRepository(Message::class)
                ->findBy(['conversation' => $conversation], ['date' => 'ASC']);
        

            $conversation = $this->serializer->serialize($messages, 'json', ['groups' => 'conversation:read']);

            return new JsonResponse(["data" => json_decode($conversation, true), "code" => 200], Response::HTTP_OK);
        // } catch (\Throwable $th) {
        //     return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de la conversation : " . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        // }

    }

    /**
     * Création d'un nouvel Conversation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'Conversation_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // recuperation de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);

            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $otherUserId = $data['receiver'] ?? null;

            if (!$otherUserId) {
                return $this->json(['message' => 'Identifiant du destinataire manquant'], 400);
            }
            // Récupération de l'utilisateur destinataire
            $otherUser = $this->entityManager->getRepository(User::class)->find($otherUserId);
            // Vérification si l'utilisateur destinataire existe
            if (!$otherUser) {
                return $this->json(['message' => 'Utilisateur non trouvé'], 404);
            }

            // Vérifie si une conversation existe déjà
            $existing = $this->entityManager->getRepository(Conversation::class)->findOneBetweenUsers($user, $otherUser);

            if ($existing) {
                return $this->json(['message' => 'Conversation déjà existante', 'id' => $existing->getId()], 200);
            }
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Conversation", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'Conversation:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Conversation crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Conversation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Conversation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Conversation par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'Conversation_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
    
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Conversation dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Conversation", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'Conversation:read']);
                $response = json_decode($response, true);
                return $this->json(["data"=> $response, 'code' => 200, 'message' => "Conversation modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Conversation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Conversation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'un Conversation par son ID
     * 
     * @param Conversation $Conversation
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'Conversation_delete', methods: ['DELETE'])]
    public function delete(Conversation $Conversation, EntityManagerInterface $entityManager): Response
    {
        try {
            // Suppression de l'entité Conversation passée en paramètre
            $entityManager->remove($Conversation);
    
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Conversation supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
