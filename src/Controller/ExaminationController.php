<?php

namespace App\Controller;

use App\Entity\Examination;
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
 * Controleur pour la gestion des Examination
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/examinations')]
class ExaminationController extends AbstractController
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
     * Liste des Examination
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'examination_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Récupération des Examinations avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Examination', 'examination:read', $filtre);

            // Retour d'une réponse JSON avec les Examinations et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des Examination : " . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Affichage d'un Examination par son ID
     *
     * @param Examination $Examination
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'examination_show', methods: ['GET'])]
    public function show(Examination $examination): Response
    {
        try {
            // Sérialisation de l'entité Examination en JSON avec le groupe de sérialisation 'Examination:read'
            $examination = $this->serializer->serialize($examination, 'json', ['groups' => 'examination:read']);
        
            // Retour de la réponse JSON avec les données de l'Examination et un code HTTP 200
            return new JsonResponse(["data" => json_decode($examination, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de l'Examination : " . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Création d'un nouvel Examination
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'examination_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Examination", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                return $this->json(['data' => $errors['entity'],'code' => 200, 'message' => "Examination crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Examination"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Examination" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Examination par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'examination_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
    
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Examination dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Examination", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['data' => $errors['entity'],'code' => 200, 'message' => "Examination modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Examination"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Examination" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'un Examination par son ID
     * 
     * @param Examination $Examination
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'examination_delete', methods: ['DELETE'])]
    public function delete(Examination $examination, EntityManagerInterface $entityManager): Response
    {
        try {
            // Suppression de l'entité Examination passée en paramètre
            $entityManager->remove($examination);
    
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Examination supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
