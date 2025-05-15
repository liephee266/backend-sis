<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Hospital;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\Notification;
use App\Services\GenericEntityManager;
use App\Services\NotificationManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des utilisateurs
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/hospitals')]
#[ApiEntity(\App\Entity\Hospital::class)]
class HospitalController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;
    private $notificationManager;

    public function __construct(
        GenericEntityManager $genericEntityManager, 
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer, 
        Toolkit $toolkit, 
        Security $security,
        NotificationManager $notificationManager)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
        $this->notificationManager = $notificationManager;
    }

    /**
     * Liste des utilisateurs
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'hospital_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
             if (
                !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT') &&
                !$this->security->isGranted('ROLE_SUPER_ADMIN')
            )
        
            {
                return new JsonResponse([
                    "message" => "Vous n'avez pas accès à cette ressource",
                    "code" => 403
                ], Response::HTTP_FORBIDDEN);
            }
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Si l'utilisateur n'est pas super admin, on filtre par statut "validated"
            if (!$this->isGranted('ROLE_SUPER_ADMIN') && !$this->isGranted('ROLE_ADMIN_SIS')) {
                // On filtre par statut "validated" pour les utilisateurs normaux
                $filtre = ['status' => 2];
            }
            // Récupération des utilisateurs avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Hospital', 'hospital:read', $filtre);

            // Retour d'une réponse JSON avec les utilisateurs et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code'=>500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Affichage d'un utilisateur par son ID
     *
     * @param Hospital $hospital
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospital_show', methods: ['GET'])]
    public function show(Hospital $hospital): Response
    {
        try {
            // Si l'hôpital n'existe pas, retourner une réponse 404
            if (!$hospital) {
                return new JsonResponse(['message' => 'Hôpital non trouvé'], Response::HTTP_NOT_FOUND);
            }

            // Vérification si l'utilisateur a les droits nécessaires (par exemple, super admin ou validation)
            if (!$this->isGranted('ROLE_SUPER_ADMIN') && $hospital->getStatus()->getName() !== 'Validated') {
                return new JsonResponse(['message' => 'Accès interdit'], Response::HTTP_FORBIDDEN);
            }

            // Sérialisation de l'entité Hospital en JSON avec le groupe de sérialisation 'hospital:read'
            $hospitalData = $this->serializer->serialize($hospital, 'json', ['groups' => 'hospital:read']);

            // Retour de la réponse JSON avec les données de l'hôpital et un code HTTP 200
            return new JsonResponse(["data" => json_decode($hospitalData, true), "code" => 200], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return new JsonResponse(['code'=>500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Création d'un nouvel utilisateur
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'hospital_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            // Récupérer le statut "en_attente"
            $status = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'Pending']);
            if (!$status) {
                return $this->json(['code' => 500, 'message' => "Statut 'Pending' introuvable"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Ajouter le statut à la data avant persistance
            $data['status'] = $status->getId();  // Récupérer le statut "en_attente"

            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Hospital", $data);
            
            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'hospital:read']);
                $response = json_decode($response, true);

                // Lorsqu'un hopital est créé
                $notification = $this->notificationManager->createNotification(
                    'Un nouvel Hopital a été créé',
                    'Un nouvel Hopital a été créé en attente de validation.',
                    null,
                    true // Notification globale
                );

                $this->notificationManager->publishNotification($notification, customChannel: 'notification-adminsis', isGlobal: false);

                return $this->json(['data' => $response,'code' => 200, 'message' => "Hopital crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Hopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code'=>500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un utilisateur par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospital_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Vérification des permissions : si l'utilisateur n'est pas un super admin ou un admin_sis
            if (!$this->isGranted('ROLE_ADMIN_SIS') && !$this->isGranted('ROLE_SUPER_ADMIN')) {
                return new JsonResponse(['message' => 'Accès interdit'], Response::HTTP_FORBIDDEN);
            }

            $user = $this->toolkit->getUser($request);

            // Récupération de l'hôpital existant dans la base de données
            $hospital = $this->entityManager->getRepository(Hospital::class)->find($id);

            if (!$hospital) {
                return new JsonResponse(['message' => 'Hôpital non trouvé'], Response::HTTP_NOT_FOUND);
            }

            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
            
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;

            // Ajout de l'ID du statut dans les données reçues pour identifier l'entité à modifier
            if (isset($data['status'])) {
                $status = $this->entityManager->getRepository(Status::class)->find($data['status']);
                if ($status) {
                    $data['status'] = $status->getId(); // Récupérer l'ID du statut
                } else {
                    return new JsonResponse(['message' => 'Statut introuvable'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                // Si le statut n'est pas fourni, on le garde tel quel
                $data['status'] = $hospital->getStatus()->getId();
            }
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Hospital dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Hospital", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'hospital:read']);
                $response = json_decode($response, true);


                // Lorsqu'un hopital est traité
                $notification = $this->notificationManager->createNotification(
                    "La validation d'un hopital a été traité",
                    "Le statut d'un Hopital nommé:".$hospital->getName(). ' à été modifier par '.$this->$user->getFirstName().'.',
                );

                $this->notificationManager->publishNotification($notification, 'notifications-superadminsis');
                return $this->json(['data' => $response,'code' => 200, 'message' => "Hopital modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'hopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code'=>500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un utilisateur par son ID
     * 
     * @param Hospital $hospital
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospital_delete', methods: ['DELETE'])]
    public function delete(Hospital $hospital, EntityManagerInterface $entityManager): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Hospital passée en paramètre
            $entityManager->remove($hospital);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Hopital supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code'=>500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
