<?php

namespace App\Controller;

use App\Entity\Affiliation;
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
 * Controleur pour la gestion des Affiliation
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/affiliations')]
class AffiliationController extends AbstractController
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
     * Liste des Affiliation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'affiliation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {

        try {
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Récupération des Affiliations avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Affiliation', 'affiliation:read', $filtre);

            // Retour d'une réponse JSON avec les Affiliations et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des Affiliations" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Affichage d'un Affiliation par son ID
     *
     * @param Affiliation $Affiliation
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'affiliation_show', methods: ['GET'])]
    public function show(Affiliation $affiliation): Response
    {
        try {
            // Sérialisation de l'entité Affiliation en JSON avec le groupe de sérialisation 'Affiliation:read'
            $affiliation = $this->serializer->serialize($affiliation, 'json', ['groups' => 'affiliation:read']);
        
            // Retour de la réponse JSON avec les données de l'Affiliation et un code HTTP 200
            return new JsonResponse(["data" => json_decode($affiliation, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de l'Affiliation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Création d'un nouvel Affiliation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'affiliation_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
        
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Affiliation", $data);
    
            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                return $this->json(['code' => 200, 'message' => "Affiliation crée avec succès"], Response::HTTP_OK);
            }
    
            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Affiliation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Affiliation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un Affiliation par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'affiliation_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Affiliation dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Affiliation", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['code' => 200, 'message' => "Affiliation modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Affiliation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Affiliation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un Affiliation par son ID
     * 
     * @param Affiliation $Affiliation
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'affiliation_delete', methods: ['DELETE'])]
    public function delete(Affiliation $affiliation, EntityManagerInterface $entityManager): Response
    {
        try {
             // Suppression de l'entité Affiliation passée en paramètre
            $entityManager->remove($affiliation);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Affiliation supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'Affiliation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}