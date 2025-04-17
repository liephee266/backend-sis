<?php

namespace App\Controller;

use App\Entity\Urgency;
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
 * Controleur pour la gestion des Urgency
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/urgencys')]
class UrgencyController extends AbstractController
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
     * Liste des Urgency
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'urgency_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_URGENTIST') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Récupération des Urgencys avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Urgency', 'urgency:read', $filtre);

            // Retour d'une réponse JSON avec les Urgencys et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Affichage d'un Urgency par son ID
     *
     * @param Urgency $Urgency
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgency_show', methods: ['GET'])]
    public function show(Urgency $urgency): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_URGENTIST') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Sérialisation de l'entité Urgency en JSON avec le groupe de sérialisation 'Urgency:read'
            $urgency = $this->serializer->serialize($urgency, 'json', ['groups' => 'urgency:read']);
        
            // Retour de la réponse JSON avec les données de l'Urgency et un code HTTP 200
            return new JsonResponse(["data" => json_decode($urgency, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Création d'un nouvel Urgency
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'urgency_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Urgency", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                return $this->json(['code' => 200, 'message' => "Urgency crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Urgency"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Urgency par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgency_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
        
            $user = $this->toolkit->getUser($request);

            /// On récupère l'urgence à modifier
            $urgence = $this->entityManager->getRepository(Urgency::class)->find($id);

            if (!$urgence) {
                return $this->json(['code' => 404, 'message' => "Urgence introuvable"], Response::HTTP_NOT_FOUND);
            }

            // Vérifie que l'utilisateur connecté est bien le propriétaire de l'urgence
            if ($urgence->getPatient()?->getUser()?->getId() !== $user->getId()) {
                return new JsonResponse(['code' => 403, 'message' => "Vous ne pouvez modifier que vos urgences"], Response::HTTP_FORBIDDEN);
            }

            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;

            // Ajout la date de modification
            $data['updated_at'] = new \DateTimeImmutable('now');
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Urgency dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Urgency", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['code' => 200, 'message' => "Urgency modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Urgency"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'un Urgency par son ID
     * 
     * @param Urgency $Urgency
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgency_delete', methods: ['DELETE'])]
    public function delete(Urgency $urgency, EntityManagerInterface $entityManager): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_URGENTIST') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Urgency passée en paramètre
            $entityManager->remove($urgency);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Urgency supprimé avec succès"], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
