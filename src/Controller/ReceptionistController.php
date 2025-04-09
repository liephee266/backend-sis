<?php

namespace App\Controller;

use App\Entity\Receptionist;
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
 * Controleur pour la gestion des Receptionist
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/receptionists')]
class ReceptionistController extends AbstractController
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
     * Liste des Receptionist
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'receptionist_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des Receptionists avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Receptionist', 'receptionist:read', $filtre);

        // Retour d'une réponse JSON avec les Receptionists et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un Receptionist par son ID
     *
     * @param Receptionist $Receptionist
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'receptionist_show', methods: ['GET'])]
    public function show(Receptionist $receptionist): Response
    {
        // Sérialisation de l'entité Receptionist en JSON avec le groupe de sérialisation 'Receptionist:read'
        $receptionist = $this->serializer->serialize($receptionist, 'json', ['groups' => 'receptionist:read']);
    
        // Retour de la réponse JSON avec les données de l'Receptionist et un code HTTP 200
        return new JsonResponse(["data" => json_decode($receptionist, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel Receptionist
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'receptionist_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
        
        // Début de la transaction
        $this->entityManager->beginTransaction();

        // Création du User
        $user_data = [
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => ["ROLE_RECEPTIONIST"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        // Appel à la méthode persistEntityUser pour insérer les données du User dans la base
        $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Receptionist", $user_data, $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "Receptionist crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création du Receptionist"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un Receptionist par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'receptionist_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Receptionist dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Receptionist", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Receptionist modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Receptionist"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un Receptionist par son ID
     * 
     * @param Receptionist $Receptionist
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'receptionist_delete', methods: ['DELETE'])]
    public function delete(Receptionist $receptionist, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité Receptionist passée en paramètre
        $entityManager->remove($receptionist);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Receptionist supprimé avec succès"], Response::HTTP_OK);
    }
}
