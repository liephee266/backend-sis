<?php

namespace App\Controller;

use App\Entity\Doctor;
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
 * Controleur pour la gestion des Doctor
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/doctors')]
class DoctorController extends AbstractController
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
     * Liste des Doctor
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'doctor_index', methods: ['GET'])]
    public function index(Request $request): Response
    {

        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_ADMIN_HOPITAL') && !$this->security->isGranted('ROLE_ADMIN_SIS')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
        $filtre = [];

        // Récupération des Doctors avec pagination
        $response = $this->toolkit->getPagitionOption($request, 'Doctor', 'doctor:read', $filtre);

        // Retour d'une réponse JSON avec les Doctors et un statut HTTP 200 (OK)
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * Affichage d'un Doctor par son ID
     *
     * @param Doctor $Doctor
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'doctor_show', methods: ['GET'])]
    public function show(Doctor $doctor): Response
    {

        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_ADMIN_HOPITAL') && !$this->security->isGranted('ROLE_ADMIN_SIS')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Sérialisation de l'entité Doctor en JSON avec le groupe de sérialisation 'Doctor:read'
        $doctor = $this->serializer->serialize($doctor, 'json', ['groups' => 'doctor:read']);
    
        // Retour de la réponse JSON avec les données de l'Doctor et un code HTTP 200
        return new JsonResponse(["data" => json_decode($doctor, true), "code" => 200], Response::HTTP_OK);
    }

    /**
     * Création d'un nouvel Doctor
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'doctor_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        
        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_ADMIN_HOPITAL') && !$this->security->isGranted('ROLE_ADMIN_SIS')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès non autorisé"], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);

        // Conversion de la date de service en objet DateTime
        $data['serviceStartingDate'] = new \DateTime($data['serviceStartingDate']);
        
        // Appel à la méthode persistEntity pour insérer les données dans la base
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Doctor", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
            return $this->json(['code' => 200, 'message' => "Doctor crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Doctor"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un Doctor par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'doctor_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        //Verification de l'existence de l'entite
        $doctor = $this->entityManager->getRepository(Doctor::class)->find($id);
        
        if (!$doctor) {
            return $this->json(['code' => 404, 'message' => "Ce doctor specifier n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;

        // Conversion de la date de service en objet DateTime
        $data['serviceStartingDate'] = new \DateTime($data['serviceStartingDate']);
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Doctor dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Doctor", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Doctor modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Doctor"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un Doctor par son ID
     * 
     * @param Doctor $Doctor
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'doctor_delete', methods: ['DELETE'])]
    public function delete(Doctor $doctor = null, EntityManagerInterface $entityManager): Response
    {
        if (!$doctor) {
            return $this->json(
                ['code' => 404, 'message' => "Ce doctor n'existe pas ou a deja été supprimé"],
                Response::HTTP_NOT_FOUND
            );
        }

        // Suppression de l'entité Doctor passée en paramètre
        $entityManager->remove($doctor);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Doctor supprimé avec succès"], Response::HTTP_OK);
    }
}
