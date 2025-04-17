<?php

namespace App\Controller;

use App\Entity\DossierMedicale;
use App\Services\Toolkit;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des DossierMedicale
 * 
 * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
 */
#[Route('/api/v1/dossier_medicale', name: 'dossier_medicale_')]
class DossierMedicaleController extends AbstractController
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
     * Liste des DossierMedicale
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/', name: 'dossier_medicale_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Récupération des dossier_medicales avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'DossierMedicale', 'dossier_medicale:read', $filtre);

            // Retour d'une réponse JSON avec les dossier_medicales et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des DossierMedicales" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Affichage d'un DossierMedicale par son ID
     *
     * @param DossierMedicale $dossier_medicale
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'dossier_medicale_show', methods: ['GET'])]
    public function show(DossierMedicale $dossier_medicale): Response
    {
        try {
            // Sérialisation de l'entité DossierMedicale en JSON avec le groupe de sérialisation 'DossierMedicale:read'
            $dossier_medicale = $this->serializer->serialize($dossier_medicale, 'json', ['groups' => 'dossier_medicale:read']);
        
            // Retour de la réponse JSON avec les données de l'DossierMedicale et un code HTTP 200
            return new JsonResponse(["data" => json_decode($dossier_medicale, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche du DossierMedicale" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Création d'un nouvel DossierMedicale
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/', name: 'dossier_medicale_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\DossierMedicale", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                return $this->json(['code' => 200, 'message' => "DossierMedicale crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création du DossierMedicale"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création du DossierMedicale" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un DossierMedicale par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'dossier_medicale_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité DossierMedicale dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\DossierMedicale", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un do$dossier_medicale de succès
                return $this->json(['code' => 200, 'message' => "DossierMedicale modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification du DossierMedicale"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur interne serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'un DossierMedicale par son ID
     * 
     * @param DossierMedicale $DossierMedicale
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'dossier_medicale_delete', methods: ['DELETE'])]
    public function delete(DossierMedicale $dossier_medicale, EntityManagerInterface $entityManager): Response
    {
        try {
            // Suppression de l'entité DossierMedicale passée en paramètre
            $entityManager->remove($dossier_medicale);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "DossierMedicale supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression du DossierMedicale" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

