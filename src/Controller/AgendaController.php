<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Doctor;
use App\Entity\Patient;
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
 * Controleur pour la gestion des Agenda
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/agendas')]
class AgendaController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private Security $security;

    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit, Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des Agenda
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'agenda_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }
        // Vérification des autorisations de l'utilisateur connecté
        $filtre = [];

        // Récupération des Agendas avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Agenda', 'agenda:read', $filtre);

        // Retour d'une réponse JSON avec les Agendas et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche des agendas" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Affichage d'un Agenda par son ID
     *
     * @param Agenda $Agenda
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenda_show', methods: ['GET'])]
    public function show(Agenda $agenda): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Sérialisation de l'entité Agenda en JSON avec le groupe de sérialisation 'Agenda:read'
            $agenda = $this->serializer->serialize($agenda, 'json', ['groups' => 'agenda:read']);
        
            // Retour de la réponse JSON avec les données de l'Agenda et un code HTTP 200
            return new JsonResponse(["data" => json_decode($agenda, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la recherche de l'agenda" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Création d'un nouvel Agenda
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'agenda_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data['timeInterval'] = new \DateTimeImmutable($data['timeInterval']);
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Agenda", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'agenda:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response, 'code' => 200, 'message' => "Agenda crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Agenda"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Agenda" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }

    /**
     * Modification d'un Agenda par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenda_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);

        $data['timeInterval'] = new \DateTimeImmutable($data['timeInterval']);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Agenda dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Agenda", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'agenda:read']);
            $response = json_decode($response, true);
            return $this->json(['data' => $response,'code' => 200, 'message' => "Agenda modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Agenda"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Agenda" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
    
    /**
     * Suppression d'un Agenda par son ID
     * 
     * @param Agenda $Agenda
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'agenda_delete', methods: ['DELETE'])]
    public function delete(Agenda $agenda, EntityManagerInterface $entityManager): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Suppression de l'entité Agenda passée en paramètre
            $entityManager->remove($agenda);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Agenda supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->json(['code' => 500, 'message' => "Erreur lors de la suppression de l'Agenda" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
