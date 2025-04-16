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
use Symfony\Bundle\SecurityBundle\Security;

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
    private $security;

    public function __construct(GenericEntityManager $genericEntityManager, 
                                EntityManagerInterface $entityManager, 
                                SerializerInterface $serializer, 
                                Toolkit $toolkit,
                                Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
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
        if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
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
        if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
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
        if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);

        // Début de la transaction
        $this->entityManager->beginTransaction();

        // Création du User
        $user_data = [
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => ["ROLE_ADMIN_HOSPITAL"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        // Appel à la méthode persistEntityUser pour insérer les données du User dans la base
        $errors = $this->genericEntityManager->persistEntityUser("App\Entity\HospitalAdmin", $user_data, $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "Admin hopital crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'admin hopital"], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
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
        if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
        // Suppression de l'entité HospitalAdmin passée en paramètre
        $entityManager->remove($hospitaladmin);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "HospitalAdmin supprimé avec succès"], Response::HTTP_OK);
    }
}
