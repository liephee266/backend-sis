<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Status;
use App\Services\Toolkit;
use App\Entity\Autorisation;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des Autorisations
 * 
 * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
 */
#[Route('/api/v1/autorisation', name: 'autorisation_')]
class AutorisationController extends AbstractController
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
     * Liste des Autorisations
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/', name: 'autorisation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des autorisations avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Autorisation', 'autorisation:read', $filtre);

        // Retour d'une réponse JSON avec les autorisations et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'une Autorisation par son ID
     *
     * @param Autorisation $autorisation
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'autorisation_show', methods: ['GET'])]
    public function show(Autorisation $autorisation): Response
    {
        // Sérialisation de l'entité Autorisation en JSON avec le groupe de sérialisation 'Autorisation:read'
        $autorisation = $this->serializer->serialize($autorisation, 'json', ['groups' => 'autorisation:read']);
    
        // Retour de la réponse JSON avec les données de l'Autorisation et un code HTTP 200
        return new JsonResponse(["data" => json_decode($autorisation, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'une nouvelle Autorisation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{entity_name}/{id}', name: 'autorisation_create', methods: ['POST'])]
    public function create(Request $request, $entity_name, $id): Response
    {
        $dataEntity = [
            "dossier_medicale" => "DossierMedicale",
        ];

        if (!array_key_exists($entity_name, $dataEntity)) {
            return new JsonResponse(['message' => 'Entité non trouvée', "code" => 404], Response::HTTP_NOT_FOUND);
        }

        // Récupération de l'ID  par la methode ExistRepository
        $entity = $this->toolkit->ExistRepository($dataEntity, $entity_name, $id);

        if (!$entity) {
            return new JsonResponse(
                ['message' => $entity_name.' non trouvé(e)', 'code' => 404], 
                Response::HTTP_NOT_FOUND
            );
        }

        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
    
        $user_connect = $this->toolkit->getUser($request);
        // Ajout de l'ID de l'utilisateur connecté dans les données
        $data['demander_id'] = $user_connect->getId();

        // Ajout du role de l'utilisateur connecté dans les données
        $data['demander_role'] = $user_connect->getRoles()[0];

        // Ajout de l'ID de l'entité dans les données
        $data['entity_id'] = $entity->getId();

        // Ajout de l'entité dans les données
        $data['entity'] = $entity_name;

        // Ajouter le statut à la data avant persistance
        $data['status_id'] = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'pending'])->getId(); 

        $data['validator_role'] = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['validator_id']])->getRoles()[0];

        // Appel à la méthode persistEntity pour insérer les données dans la base
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Autorisation", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
            return $this->json(['code' => 200, 'message' => "Autorisation soumis avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la soumission du Autorisation"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'une Autorisation par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'autorisation_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;

        $autorisation_data = [
            'date_limit' =>$data['date_limit'],
            'status_id' => $data['status_id'],
            'updated_at' => new \DateTime(),
            'id' => $data['id'],
        ];

        // Appel à la méthode persistEntity pour mettre à jour l'entité Autorisation dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Autorisation", $autorisation_data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un do$autorisation de succès
            return $this->json(['code' => 200, 'message' => "Autorisation modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Autorisation"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'une Autorisation par son ID
     * 
     * @param Autorisation $Autorisation
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'autorisation_delete', methods: ['DELETE'])]
    public function delete(Autorisation $autorisation, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité Autorisation passée en paramètre
        $entityManager->remove($autorisation);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Autorisation supprimé avec succès"], Response::HTTP_OK);
    }
}

