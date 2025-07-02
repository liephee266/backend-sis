<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Urgency;
use App\Entity\Urgentist;
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
 * Controleur pour la gestion des Urgentist
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/urgentists')]
#[ApiEntity(\App\Entity\User::class)]
class UrgentistController extends AbstractController
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN') && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            // Si l'utilisateur connecté est un administrateur d'hôpital, on récupère l'hôpital associé
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $user])->getHospital()->getId();
                // Ajout du filtre pour l'hôpital
                $filtre['hospital_id'] = $hospitalAdmin;
            }
            // Récupération des Urgentists avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Urgentist', 'urgentist:read', $filtre);

            // Retour d'une réponse JSON avec les Urgentists et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function show(Urgentist $urgentist, Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN') && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Si l'utilisateur connecté est un administrateur d'hôpital, on vérifie si l'urgentist appartient à cet hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $user])->getHospital()->getId();
                
                // Si l'urgentist n'appartient pas à l'hôpital de l'administrateur, on retourne une erreur 403
                if ($urgentist->getHospitalId()->getId() !== $hospitalAdmin) {
                    return new JsonResponse(['code' => 403, 'message' => "Accès refusé pour cet urgentist"], Response::HTTP_FORBIDDEN);
                }
            }

            // Sérialisation de l'entité Urgentist en JSON avec le groupe de sérialisation 'Urgentist:read'
            $urgentist = $this->serializer->serialize($urgentist, 'json', ['groups' => 'urgentist:read']);
        
            // Retour de la réponse JSON avec les données de l'Urgentist et un code HTTP 200
            return new JsonResponse(["data" => json_decode($urgentist, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations
            if (
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS')
            ) {
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }

            // Récupération et décodage des données
            $data = json_decode($request->getContent(), true);

            $data["password"] = $data["password"] ?? 123456789;

            // Création du User
            $user_data = [
                'email' => $data['email'],
                'password' => $data['password'],
                'roles' => ["ROLE_URGENTIST"],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'nickname' => $data['nickname']?? null,
                'tel' => $data['tel'],
                'birth' => new \DateTime($data['birth']),
                'gender' => $data['gender'],
                'address' => $data['address']?? null,
                'image' => $data['image']?? null,
            ];

            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $user])->getHospital()->getId();

                $data["hospital_id"] = $hospitalAdmin;

                if (!$data) {
                    return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
                }

                // Démarrer la transaction
                $this->entityManager->beginTransaction();

                    $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Urgentist", $user_data, $data);

                    // Vérification si l'entité a été créée sans erreur
                    if (!empty($errors['entity'])) {
                        $this->entityManager->commit();
                        $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'urgentist:read']);
                        $response = json_decode($response, true);
                        return new JsonResponse(['data' => $response, 'code' => 200,'message' => "Urgentist créé avec succès"], Response::HTTP_OK);
                    }

                    // Erreur dans la persistance
                    return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'urgentist"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }else {

                if (!$data) {
                    return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
                }

                // Démarrer la transaction
                $this->entityManager->beginTransaction();

                    $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Urgentit", $user_data, $data);

                    // Vérification si l'entité a été créée sans erreur
                    if (!empty($errors['entity'])) {
                        $this->entityManager->commit();
                        $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'urgentist:read']);
                        $response = json_decode($response, true);
                        return new JsonResponse(['data' => $response,'code' => 200,'message' => "Urgentist créé avec succès"], Response::HTTP_OK);
                    }

                    // Erreur dans la persistance
                    return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'urgentist"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            return $this->json(['code' => 500, 'message' => "Erreur serveur: " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);

            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Urgentist dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Urgentist", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'urgentist:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Urgentist modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Urgentist"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS') && !$this->security->isGranted('ROLE_SUPER_ADMIN'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Urgentist passée en paramètre
            $entityManager->remove($urgentist);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Urgentist supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
