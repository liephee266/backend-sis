<?php

namespace App\Controller;

use App\Entity\HospitalAdmin;
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
 * Controleur pour la gestion des HospitalAdmin
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/hospitaladmins')]
class HospitalAdminController extends AbstractController
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
     * Liste des HospitalAdmin
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'hospitaladmin_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des HospitalAdmins avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'HospitalAdmin', 'hospitaladmin:read', $filtre);

        // Retour d'une réponse JSON avec les HospitalAdmins et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un HospitalAdmin par son ID
     *
     * @param HospitalAdmin $HospitalAdmin
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospitaladmin_show', methods: ['GET'])]
    public function show(HospitalAdmin $hospitaladmin): Response
    {
        // Sérialisation de l'entité HospitalAdmin en JSON avec le groupe de sérialisation 'HospitalAdmin:read'
        $hospitaladmin = $this->serializer->serialize($hospitaladmin, 'json', ['groups' => 'hospitaladmin:read']);
    
        // Retour de la réponse JSON avec les données de l'HospitalAdmin et un code HTTP 200
        return new JsonResponse(["data" => json_decode($hospitaladmin, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel HospitalAdmin
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'hospitaladmin_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
        
        // Appel à la méthode persistEntity pour insérer les données dans la base
        $errors = $this->genericEntityManager->persistEntity("App\Entity\HospitalAdmin", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
            return $this->json(['code' => 200, 'message' => "HospitalAdmin crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'HospitalAdmin"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un HospitalAdmin par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospitaladmin_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité HospitalAdmin dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\HospitalAdmin", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "HospitalAdmin modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'HospitalAdmin"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un HospitalAdmin par son ID
     * 
     * @param HospitalAdmin $HospitalAdmin
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'hospitaladmin_delete', methods: ['DELETE'])]
    public function delete(HospitalAdmin $hospitaladmin, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité HospitalAdmin passée en paramètre
        $entityManager->remove($hospitaladmin);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "HospitalAdmin supprimé avec succès"], Response::HTTP_OK);
    }
}
