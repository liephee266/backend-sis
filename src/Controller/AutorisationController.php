<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Doctor;
use App\Entity\Status;
use App\Entity\Hospital;
use App\Services\Toolkit;
use App\Entity\Autorisation;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {

            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

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
        if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {

            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }
        // Sérialisation de l'entité Autorisation en JSON avec le groupe de sérialisation 'Autorisation:read'
        $autorisation = $this->serializer->serialize($autorisation, 'json', ['groups' => 'autorisation:read']);
    
        // Retour de la réponse JSON avec les données de l'Autorisation et un code HTTP 200
        return new JsonResponse(["data" => json_decode($autorisation, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Demande d'une autorisation à un dossier medicale 
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU<michelmiyalou0@gmail.com>
     */
    #[Route('/{entity_name}/{id}', name: 'autorisation_create', methods: ['POST'])]
    public function create(Request $request, $entity_name, $id): Response
    {
        try {
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')
                && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
                && !$this->security->isGranted('ROLE_DOCTOR')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

            $dataEntity = [
                "dossier_medicale" => "DossierMedicale",
                "hopital" => "Hospital",
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
            $data['status_id'] = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'Pending'])->getId(); 

            $data['validator_role'] = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['validator_id']])->getRoles()[0];

            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Autorisation", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'autorisation:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Autorisation soumis avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la soumission du Autorisation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la soumission du Autorisation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Validation d'une Autorisation au dossier medicale par son ID
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
        try {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')
                && !$this->security->isGranted('ROLE_PATIENT')) {
            # code...
            return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
        }

            // Récupération de l'ID de l'autorisation
            $autorisation_type = $this->entityManager->getRepository(Autorisation::class)->findOneBy(['id' => $id])->getTypeDemande();

        if ($autorisation_type === "AUTORISATION") {
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
                    $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'autorisation:read']);
                    $response = json_decode($response, true);
                    return $this->json(['data' => $response,'code' => 200, 'message' => "Autorisation modifié avec succès"], Response::HTTP_OK);
                }
            
                // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
                return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Autorisation"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }else{
                $data = json_decode($request->getContent(), true);

                if ($data['status_id'] === 2) {
                    // Mise à jour de l'autorisation
                    $autorisation_affiliation = [
                        'updated_at' => new \DateTime(),
                        'id' => $id,
                        'status_id' => $data['status_id'],
                    ];
                
                    $errors = $this->genericEntityManager->persistEntity("App\Entity\Autorisation", $autorisation_affiliation, true);
                
                    // Récupération des entités
                    $autorisation = $this->entityManager->getRepository(Autorisation::class);
                    if (!$autorisation) {
                        return $this->json(['code' => 404, 'message' => "Autorisation non trouvée"], Response::HTTP_NOT_FOUND);
                    }
                
                    $demander_id = $autorisation->find($id)->getDemanderId();
                    $user = $this->entityManager->getRepository(User::class)->find($demander_id);
                    if (!$user) {
                        return $this->json(['code' => 404, 'message' => "Utilisateur non trouvé"], Response::HTTP_NOT_FOUND);
                    }
                
                    $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);
                    if (!$doctor) {
                        return $this->json(['code' => 404, 'message' => "Docteur non trouvé"], Response::HTTP_NOT_FOUND);
                    }
                
                    $hospital = $this->entityManager->getRepository(Hospital::class)->find($autorisation->find($id)->getEntityId());

                    if (!$hospital) {
                        return $this->json(['code' => 404, 'message' => "Hôpital non trouvé"], Response::HTTP_NOT_FOUND);
                    }
                
                    // Établir la relation
                    $hospital->addDoctor($doctor);

                    $this->entityManager->persist($hospital); // ou $doctor selon la relation
                    $this->entityManager->flush();

                    return $this->json(['data' => $errors['entity'],'code' => 200, 'message' => "Autorisation modifiée avec succès"], Response::HTTP_OK);
                }else {
                    // Mise à jour de l'autorisation
                    $autorisation_affiliation = [
                        'updated_at' => new \DateTime(),
                        'id' => $id,
                        'status_id' => $data['status_id'],
                    ];
                
                    $errors = $this->genericEntityManager->persistEntity("App\Entity\Autorisation", $autorisation_affiliation, true);
                    
                    // Vérification si l'entité a été mise à jour sans erreur
                    if (!empty($errors['entity'])) {
                        // Si l'entité a été mise à jour, retour d'une réponse JSON avec un do$autorisation de succès
                        return $this->json(['data' => $errors['entity'],'code' => 200, 'message' => "Autorisation modifié avec succès"], Response::HTTP_OK);
                    }
                
                    // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
                    return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Autorisation"], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Autorisation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Suppression de l'entité Autorisation passée en paramètre
            $entityManager->remove($autorisation);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Autorisation supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'Autorisation" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

