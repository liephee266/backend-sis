<?php

namespace App\Controller;

use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\DossierMedicale;
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
 * Controleur pour la gestion des DossierMedicale
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/dossier_medicale')]
#[ApiEntity(\App\Entity\DossierMedicale::class)]
class DossierMedicaleController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit,Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des DossierMedicale
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'DossierMedicale_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')
                && !$this->security->isGranted('ROLE_SUPER_ADMIN')
                && !$this->security->isGranted('ROLE_DOCTOR')
                && !$this->security->isGranted('ROLE_PATIENT')
                && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Initialisation du filtre
            $filtre = [];
            $dossiersAccessibles = [];

            // Récupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);
        
            // Si c'est un patient, on filtre uniquement ses historiques
            if ($this->security->isGranted('ROLE_PATIENT')) {
                if (!$user) {
                    return new JsonResponse(['code' => 401, 'message' => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
                }
    
               // Récupérer le patient lié à cet utilisateur
                $patient = $this->entityManager
                    ->getRepository('App\Entity\Patient')
                    ->findOneBy(['user' => $user]);

                if (!$patient) {
                    return new JsonResponse(['code' => 404, 'message' => "Profil patient introuvable"], Response::HTTP_NOT_FOUND);
                }

                // // Récupérer le dossier médical du patient connecté
                // $dossierMedical = $this->entityManager->getRepository('App\Entity\DossierMedicale')->findOneBy(['patient_id' => $patient]);
                // if (!$dossierMedical) {
                //     return new JsonResponse(['code' => 404, 'message' => "Dossier médical introuvable"], Response::HTTP_NOT_FOUND);
                // } 
                // Limiter les dossiers accessibles uniquement à celui du patient
                $filtre = ['patient_id' => $patient->getId()];

               
            }
            else {
            // si c'est pas un ptient on vérifie si id dans champ access est le meme que l'id de l'utilisateur connecté
                $dossiers = $this->entityManager
                ->getRepository(DossierMedicale::class)
                ->findAll();

                $dossiersAccessibles = array_filter($dossiers, function ($dossier) use ($user) {
                    return in_array($user->getId(), $dossier->getAccess() ?? []);
                });

                }

            // TODO : Ajouter une gestion spécifique via une table "autorisation" si nécessaire pour les autres rôles

            // Récupération des DossierMedicales avec pagination et filtre
            $response = $this->toolkit->getPagitionOption($request, 'DossierMedicale', 'dossier_medicale:read', $filtre,["access" => $dossiersAccessibles]);

            return new JsonResponse($response, Response::HTTP_OK);
        // } catch (\Throwable $th) {
        //         return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des Historique Medicals : " . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        //     }
    }


    /**
     * Affichage d'un DossierMedicale par son ID
     *
     * @param DossierMedicale $DossierMedicale
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'DossierMedicale_show', methods: ['GET'])]
    public function show(DossierMedicale $DossierMedicale, Request $request): Response
    {
        try {
             // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')
                && !$this->security->isGranted('ROLE_SUPER_ADMIN')
                && !$this->security->isGranted('ROLE_DOCTOR')
                && !$this->security->isGranted('ROLE_PATIENT')
                && !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            $user = $this->toolkit->getUser($request);

            if (!$user) {
                return new JsonResponse(['code' => 401, 'message' => "Utilisateur non connecté"], Response::HTTP_UNAUTHORIZED);
            }

            $isPatient = $this->security->isGranted('ROLE_PATIENT');

            if ($isPatient) {
                $patient = $this->entityManager
                    ->getRepository('App\Entity\Patient')
                    ->findOneBy(['user' => $user]);

                if (!$patient) {
                    return new JsonResponse(['code' => 404, 'message' => "Profil patient introuvable"], Response::HTTP_NOT_FOUND);
                }

                if ($DossierMedicale->getPatientId() !== $patient) {
                    return new JsonResponse(['code' => 403, 'message' => "Accès refusé à ce dossier médical"], Response::HTTP_FORBIDDEN);
                }
            } else {
                // Vérifie que l'utilisateur connecté a bien accès à ce dossier
                $accessList = $DossierMedicale->getAccess() ?? [];

            if (!in_array($user->getId(), $accessList)) {
                return new JsonResponse([
                    'code' => 403,
                    'message' => "Vous n'avez pas accès à ce dossier médical",
                    'access' => $accessList,
                    'user_id' => $user->getId()
                ],Response::HTTP_FORBIDDEN);
            }
            }
            $historiqueSerialized = $this->serializer->serialize($DossierMedicale,'json',['groups' => 'dossier_medicale:read']);

            return new JsonResponse(["data" => json_decode($historiqueSerialized, true),"code" => 200], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return $this->json(['code' => 500,'message' => "Erreur lors de la consultation du dossier médical : " . $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Création d'un nouvel DossierMedicale
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'DossierMedicale_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\DossierMedicale", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'dossier_medicale:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "DossierMedicale crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Historique Medical"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un DossierMedicale par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'DossierMedicale_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);

            // Vérification historique est pour ce patient
            $DossierMedicale = $this->entityManager->getRepository('App\Entity\DossierMedicale')->find($id);
            if (!$DossierMedicale) {
                return new JsonResponse(['code' => 404, 'message' => "Historique médical introuvable"], Response::HTTP_NOT_FOUND);
            }
            // Vérification si l'utilisateur a le droit de modifier cet historique
            if ($DossierMedicale->getPatient() !== $this->toolkit->getUser($request)) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé à cet historique"], Response::HTTP_FORBIDDEN);
            }
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité DossierMedicale dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\DossierMedicale", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un do$dossier_medicale de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'dossier_medicale:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "DossierMedicale modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Historique Medical"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un DossierMedicale par son ID
     * 
     * @param DossierMedicale $DossierMedicale
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'DossierMedicale_delete', methods: ['DELETE'])]
    public function delete(DossierMedicale $DossierMedicale, EntityManagerInterface $entityManager, Request $request,$id): Response
    {
        try {
             // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
             // Vérification historique est pour ce patient
            $DossierMedicale = $this->entityManager->getRepository('App\Entity\DossierMedicale')->find($id);
            if (!$DossierMedicale) {
                return new JsonResponse(['code' => 404, 'message' => "Historique médical introuvable"], Response::HTTP_NOT_FOUND);
            }
            // Vérification si l'utilisateur a le droit de modifier cet historique
            if ($DossierMedicale->getPatient() !== $this->toolkit->getUser($request)) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé à cet historique"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité DossierMedicale passée en paramètre
            $entityManager->remove($DossierMedicale);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Historique Medical supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'Historique Medical" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
