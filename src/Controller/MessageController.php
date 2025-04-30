<?php

namespace App\Controller;

use App\Entity\Message;
use App\Services\Toolkit;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des Message
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/messages')]
class MessageController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
    }

    /**
     * Liste des Message
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'message_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            //recupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);
             
            // $messages = $this->entityManager->getRepository(Message::class)
            //     ->createQueryBuilder('m')
            //     ->where('m.sender = :user')
            //     ->orWhere('m.receiver = :user')
            //     ->setParameter('user', $user)
            //     ->orderBy('m.date', 'DESC')
            //     ->getQuery()
            //     ->getResult();
            $filtre = [
                'sender' => $user->getId(),
                'receiver' => $user->getId()
            ];
            
        // Récupération des Messages avec pagination et filtre personnalisé
            $response = $this->toolkit->getPagitionOption($request, 'Message', 'message:read', $filtre,'orWhere');

            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->json([
                'code' => 500,
                'message' => 'Erreur interne serveur : ' . $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Affichage d'un Message par son ID
     *
     * @param Message $Message
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'message_show', methods: ['GET'])]
    public function show(Message $message, Request $request): Response
    {
        try {
           
            $user = $this->toolkit->getUser($request);

            // Vérifie que l'utilisateur connecté est bien le destinataire
            if ($message->getReceiver() !== $user && $message->getSender() !== $user) {
                return $this->json([
                    'code' => 403,
                    'message' => 'Vous n\'avez pas le droit d\'accéder à ce message.'
                ], Response::HTTP_FORBIDDEN);
            }

            $jsonMessage = $this->serializer->serialize($message, 'json', ['groups' => 'message:read']);

            return new JsonResponse([
                "data" => json_decode($jsonMessage, true),
                "code" => 200
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->json([
                'code' => 500,
                'message' => 'Erreur interne serveur : ' . $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Création d'un nouvel Message
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'message_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data["date"] = new \DateTime($data["date"]);

            $receiver = $data["receiver"] ?? null;

            $contentMsg = $data["contentMsg"] ?? null;


        if (!$receiver || !$contentMsg) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $receiver = $this->entityManager->getRepository('App\Entity\User')->find($receiver);

        if (!$receiver) {
            return $this->json(['error' => 'receiver not found'], Response::HTTP_NOT_FOUND);
        }
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Message", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'message:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Message crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Message"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' =>'Erreur interne serveur' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Message par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'message_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);

            $data["date"] = new \DateTime($data["date"]);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Message dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Message", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'message:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Message modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Message"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' =>'Erreur interne serveur' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un Message par son ID
     * 
     * @param Message $Message
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'message_delete', methods: ['DELETE'])]
    public function delete(Message $message, EntityManagerInterface $entityManager): Response
    {
        try {
            // Suppression de l'entité Message passée en paramètre
            $entityManager->remove($message);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Message supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' =>'Erreur interne serveur' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
