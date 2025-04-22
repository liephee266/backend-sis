<?php

namespace App\Controller;

use App\Entity\HistoriqueMedical;
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
 * Controleur pour la gestion des HistoriqueMedical
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/historiqueMedicals')]
class HistoriqueMedicalController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit,Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des HistoriqueMedical
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'HistoriqueMedical_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
       try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')
                && !$this->security->isGranted('ROLE_SUPER_ADMIN')
                && !$this->security->isGranted('ROLE_DOCTOR')
                && !$this->security->isGranted('ROLE_PATIENT')
                && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Récupération des HistoriqueMedical avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'HistoriqueMedical', 'HistoriqueMedical:read', $filtre);

            // Retour d'une réponse JSON avec les HistoriqueMedical et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des HistoriqueMedical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Affichage d'un HistoriqueMedical par son ID
     *
     * @param HistoriqueMedical $HistoriqueMedical
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
     #[Route('/{id}', name: 'HistoriqueMedical_show', methods: ['GET'])]
    public function show(HistoriqueMedical $historiqueMedical, Request $request): Response
    {
         try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')
                && !$this->security->isGranted('ROLE_SUPER_ADMIN')
                && !$this->security->isGranted('ROLE_DOCTOR')
                && !$this->security->isGranted('ROLE_PATIENT')
                && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Sérialisation de l'entité Availability en JSON avec le groupe de sérialisation 'Availability:read'
            $historiqueMedical = $this->serializer->serialize($historiqueMedical, 'json', ['groups' => 'HistoriqueMedical:read']);
        
            // Retour de la réponse JSON avec les données de l'Availability et un code HTTP 200
            return new JsonResponse(["data" => json_decode($historiqueMedical, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de l'Availability" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Création d'un nouvel HistoriqueMedical
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'HistoriqueMedical_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\HistoriqueMedical", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                return $this->json(['code' => 200, 'message' => "Historique Medical crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Historique Medical"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un HistoriqueMedical par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'HistoriqueMedical_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);

            // Vérification historique est pour ce patient
            $historiqueMedical = $this->entityManager->getRepository('App\Entity\HistoriqueMedical')->find($id);
            if (!$historiqueMedical) {
                return new JsonResponse(['code' => 404, 'message' => "Historique médical introuvable"], Response::HTTP_NOT_FOUND);
            }
            // Vérification si l'utilisateur a le droit de modifier cet historique
            if ($historiqueMedical->getPatient() !== $this->toolkit->getUser($request)) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé à cet historique"], Response::HTTP_FORBIDDEN);
            }
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité HistoriqueMedical dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\HistoriqueMedical", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['code' => 200, 'message' => "Historique Medical modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Historique Medical"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un HistoriqueMedical par son ID
     * 
     * @param HistoriqueMedical $HistoriqueMedical
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'HistoriqueMedical_delete', methods: ['DELETE'])]
    public function delete(HistoriqueMedical $historiqueMedical, EntityManagerInterface $entityManager, Request $request,$id): Response
    {
        try {
             // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
             // Vérification historique est pour ce patient
            $historiqueMedical = $this->entityManager->getRepository('App\Entity\HistoriqueMedical')->find($id);
            if (!$historiqueMedical) {
                return new JsonResponse(['code' => 404, 'message' => "Historique médical introuvable"], Response::HTTP_NOT_FOUND);
            }
            // Vérification si l'utilisateur a le droit de modifier cet historique
            if ($historiqueMedical->getPatient() !== $this->toolkit->getUser($request)) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé à cet historique"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité HistoriqueMedical passée en paramètre
            $entityManager->remove($historiqueMedical);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Historique Medical supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
