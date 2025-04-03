<?php

namespace App\Controller;

use App\Entity\Urgentist;
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
 * Controleur pour la gestion des Urgentist
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/urgentists')]
class UrgentistController extends AbstractController
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
     * Liste des Urgentist
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'urgentist_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des Urgentists avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Urgentist', 'urgentist:read', $filtre);

        // Retour d'une réponse JSON avec les Urgentists et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un Urgentist par son ID
     *
     * @param Urgentist $Urgentist
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgentist_show', methods: ['GET'])]
    public function show(Urgentist $urgentist): Response
    {
        // Sérialisation de l'entité Urgentist en JSON avec le groupe de sérialisation 'Urgentist:read'
        $urgentist = $this->serializer->serialize($urgentist, 'json', ['groups' => 'urgentist:read']);
    
        // Retour de la réponse JSON avec les données de l'Urgentist et un code HTTP 200
        return new JsonResponse(["data" => json_decode($urgentist, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel Urgentist
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'urgentist_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
        
        // Appel à la méthode persistEntity pour insérer les données dans la base
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Urgentist", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
            return $this->json(['code' => 200, 'message' => "Urgentist crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Urgentist"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un Urgentist par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgentist_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Urgentist dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Urgentist", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Urgentist modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Urgentist"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un Urgentist par son ID
     * 
     * @param Urgentist $Urgentist
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'urgentist_delete', methods: ['DELETE'])]
    public function delete(Urgentist $urgentist, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité Urgentist passée en paramètre
        $entityManager->remove($urgentist);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Urgentist supprimé avec succès"], Response::HTTP_OK);
    }
}
