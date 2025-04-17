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
        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        // 1. Récupérer tous les utilisateurs avec le rôle ROLE_ADMIN_SIS
        $users = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN_SIS%')
            ->getQuery()
            ->getResult();

        // 2. Si aucun utilisateur correspondant
        if (empty($users)) {
            return new JsonResponse([
                'data' => [],
                'total' => 0,
                'currentPage' => 1,
                'maxPerPage' => 10
            ], Response::HTTP_OK);
        }

        // 3. Récupérer les identifiants des utilisateurs
        $userIds = array_map(function($user) {
            return $user->getId();
        }, $users);

        // 4. Appliquer le filtre sur la relation "user" dans SisAdmin
        // Ici, on crée le filtre basé sur les IDs récupérés
        $filtre = ['id' => $userIds];

        // 5. Appeler ta méthode de pagination
        $response = $this->toolkit->getPagitionOption($request, 'User', 'user:read', $filtre);

        // 6. Retour de la réponse JSON
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
    public function show(Request $request, int $id): Response
    {
        try {
             //  Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // 1. Récupérer l'utilisateur avec l'id passé en paramètre
            $user = $this->entityManager->getRepository(User::class)->find($id);

            // 2. Vérifier si l'utilisateur existe
            if (!$user) {
                return new JsonResponse([
                    'message' => 'Utilisateur non trouvé.'
                ], Response::HTTP_NOT_FOUND);
            }

            // 3. Vérifier si l'utilisateur a le rôle ROLE_ADMIN_SIS
            if (!in_array('ROLE_ADMIN_SIS', $user->getRoles())) {
                return new JsonResponse([
                    'message' => 'Accès non autorisé, l\'utilisateur n\'a pas le rôle ROLE_ADMIN_SIS.'
                ], Response::HTTP_FORBIDDEN);
            }

            // 4. Sérialisation de l'entité User en JSON avec le groupe de sérialisation 'user:read'
            $userData = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
            
            // 5. Retourner la réponse JSON avec les données de l'utilisateur et un code HTTP 200
            return new JsonResponse(["data" => json_decode($userData, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" =>"Erreur interne du serveur" . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
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
                'image' => $data['image'],
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
        } catch (\Throwable $th) {
            return new JsonResponse(["message" =>"Erreur interne du serveur" . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            $data['birth'] = new \DateTime($data['birth']);

            // Appel à la méthode persistEntity pour mettre à jour l'entité SisAdmin dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\User", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                return $this->json(['code' => 200, 'message' => "SisAdmin modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'SisAdmin"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" =>"Erreur interne du serveur" . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Suppression de l'entité SisAdmin passée en paramètre
            $entityManager->remove($sisadmin);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "SisAdmin supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" =>"Erreur interne du serveur" . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
