<?php

namespace App\Controller;

use App\Entity\User;
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
 * Controleur pour la gestion des SisAdmin
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/sisadmins')]
class SisAdminController extends AbstractController
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
     * Liste des SisAdmin
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'sisadmin_index', methods: ['GET'])]
    public function index(Request $request): Response
    {

        if (!$this->security->isGranted('ROLE_SUPER_ADMIN_SIS')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des SisAdmins avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'SisAdmin', 'sisadmin:read', $filtre);

        // Retour d'une réponse JSON avec les SisAdmins et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un SisAdmin par son ID
     *
     * @param SisAdmin $SisAdmin
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'sisadmin_show', methods: ['GET'])]
    public function show(User $sisadmin): Response
    {

        if (!$this->security->isGranted('ROLE_SUPER_ADMIN_SIS')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

        // Sérialisation de l'entité SisAdmin en JSON avec le groupe de sérialisation 'SisAdmin:read'
        $sisadmin = $this->serializer->serialize($sisadmin, 'json', ['groups' => 'sisadmin:read']);
    
        // Retour de la réponse JSON avec les données de l'SisAdmin et un code HTTP 200
        return new JsonResponse(["data" => json_decode($sisadmin, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel SisAdmin
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'sisadmin_create', methods: ['POST'])]
    public function create(Request $request): Response
    {

        // if (!$this->security->isGranted('ROLE_SUPER_ADMIN_SIS')) {
        //     # code...
        //     return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        // }

        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);

        // Début de la transaction
        $this->entityManager->beginTransaction();

        // Création du User
        $user_data = [
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => ["ROLE_ADMIN_SIS"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        $errors = $this->genericEntityManager->persistUser($user_data, $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "SisAdmin crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création du SisAdmin"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un SisAdmin par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'sisadmin_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Début de la transaction
        $this->entityManager->beginTransaction();

        // Création du User
        $user_data = [
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => ["ROLE_ADMIN_SIS"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        $errors = $this->genericEntityManager->modifyUser($user_data, $data, true);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "SisAdmin modifier avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification du SisAdmin"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un SisAdmin par son ID
     * 
     * @param SisAdmin $SisAdmin
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'sisadmin_delete', methods: ['DELETE'])]
    public function delete(User $sisadmin, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité SisAdmin passée en paramètre
        $entityManager->remove($sisadmin);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "SisAdmin supprimé avec succès"], Response::HTTP_OK);
    }
}
