<?php

namespace App\Controller;

use App\Entity\Meeting;
use App\Services\Toolkit;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Controleur pour la gestion des Meeting
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/meetings')]
class MeetingController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;

    /**
     * Constructeur de la classe MeetingController
     * 
     * @param GenericEntityManager $genericEntityManager Gestionnaire d'entité générique
     * @param EntityManagerInterface $entityManager Gestionnaire d'entité de Doctrine
     * @param SerializerInterface $serializer Srialiseur de données
     * @param Toolkit $toolkit Boite à outils de l'application
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    public function __construct(GenericEntityManager $genericEntityManager, EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit, Security $security)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des Meetin
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'meeting_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des Meetings avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Meeting', 'meeting:read', $filtre);

        // Retour d'une réponse JSON avec les Meetings et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un Meeting par son ID
     *
     * @param Meeting $Meeting
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'meeting_show', methods: ['GET'])]
    public function show(Meeting $meeting): Response
    {


        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL')   && !$this->security->isGranted('ROLE_DOCTOR')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL' ) && !$this->security->isGranted('ROLE_DOCTOR') ) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Sérialisation de l'entité Meeting en JSON avec le groupe de sérialisation 'Meeting:read'
        $meeting = $this->serializer->serialize($meeting, 'json', ['groups' => 'meeting:read']);
    
        // Retour de la réponse JSON avec les données de l'Meeting et un code HTTP 200
        return new JsonResponse(["data" => json_decode($meeting, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel Meeting
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'meeting_create', methods: ['POST'])]
    public function create(Request $request): Response
    {

        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL') && !$this->security->isGranted('ROLE_PATIENT')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);

        // Conversion de la date en objet DateTime
        $data["date"] = new \DateTime($data["date"]);

        // Appel à la méthode persistEntity pour insérer les données dans la base
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Meeting", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
            return $this->json(['code' => 200, 'message' => "Meeting crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création du Meeting"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un Meeting par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'meeting_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {

        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_AGENT_HOPITAL') && !$this->security->isGranted('ROLE_DOCTOR')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);

        // Conversion de la date en objet DateTime
        $data["date"] = new \DateTime($data["date"]);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Meeting dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Meeting", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Meeting modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Meeting"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un Meeting par son ID
     * 
     * @param Meeting $Meeting
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'meeting_delete', methods: ['DELETE'])]
    public function delete(Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        // Suppression de l'entité Meeting passée en paramètre
        $entityManager->remove($meeting);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Meeting supprimé avec succès"], Response::HTTP_OK);
    }
}
