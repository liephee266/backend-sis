<?php

namespace App\Controller;

use App\Entity\Online;
use App\Services\Toolkit;
use App\Entity\HospitalAdmin;
use App\Attribute\ApiEntity;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des Online
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/onlines')]
#[ApiEntity(\App\Entity\User::class)]
class OnlineController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private Security $security;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit,Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des Online
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel Speedy <michelmiyalou0@gmail.com>
     */
    #[Route('/all', name: 'online_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // On recupere les paramètres de l'URL
        $hospital_id = $request->query->get('hospital_id'); // Récupère 'hospital_id' dans l'URL
        $user_id = $request->query->get('user_id'); // Récupère 'user_id' dans l'URL

        // Vérifier si les paramètres sont présents
        if (!$hospital_id || !$user_id) {
            return $this->json([
                'code' => 400,
                'message' => 'Les paramètres hospital_id et user_id sont requis'
            ], Response::HTTP_BAD_REQUEST);
        }

        // On cherche une ligne en fonction de ces paramètres
        $online = $this->entityManager->getRepository(Online::class)
            ->findOneBy(['hospital_id' => $hospital_id, 'user_id' => $user_id]);

        if ($online) {
            // Si la ligne est trouvée, retourner les données
            $serialized = $this->serializer->serialize($online, 'json', ['groups' => 'online:read']);
            return $this->json([
                'data' => json_decode($serialized, true),
                'code' => 200,
                'message' => 'Données trouvées avec succès'
            ], Response::HTTP_OK);
        }

        // Si aucune donnée n'est trouvée
        return $this->json([
            'code' => 404,
            'message' => 'Aucune donnée trouvée pour ces paramètres'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Récuperation ou création d'un Online si il n'existe pas déjà
     * par rapport à l'hôpital et l'utilisateur connecté.
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel Speedy <michelmiyalou0@gmail.com>
     */
    #[Route('/', name: 'online_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_DOCTOR'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            $user = $this->toolkit->getUser($request)->getId();

            // Récupération et décodage des données
            $data = json_decode($request->getContent(), true);

            $data_online =[
                "hospital_id" => $data['hospital_id'] ?? null,
                "user_id" => $user,
            ];

            $online = $this->entityManager->getRepository(Online::class)->findOneBy($data_online);
            // Vérification si des Online ont été trouvés
            if (!$online) {
                $data['value'] = false;
                $data['user_id'] = $user;

                $result = $this->genericEntityManager->persistEntity(Online::class, $data);
                if (!empty($result['entity'])) {
                    $serialized = $this->serializer->serialize($result['entity'], 'json', ['groups' => 'online:read']);
                    return $this->json(['data' => json_decode($serialized, true), 'code' => 200, 'message' => "Online créé avec succès"], Response::HTTP_CREATED);
                }

                return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Online"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $serialized = $this->serializer->serialize($online, 'json', ['groups' => 'online:read']);
            return $this->json(['data' => json_decode($serialized, true), 'code' => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Modification d'un Online par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Michel Speedy <michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'online_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité disponibilite dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Online", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'online:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Online modifiée avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'online"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Suppression d'un Online par son ID
     * 
     * @param Online $Online
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'online_delete', methods: ['DELETE'])]
    public function delete(Online $online, EntityManagerInterface $entityManager): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Online passée en paramètre
            $entityManager->remove($online);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Online supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
