<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Meeting;
use App\Entity\Patient;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\AgentHospital;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des Meeting
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/meetings')]
#[ApiEntity(\App\Entity\Meeting::class)]

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
     * Liste des Meetings
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'meeting_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR') && !$this->security->isGranted('ROLE_AGENT_HOSPITAL'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            $filtre = [];

            // Récupération de l'utilisateur connectéù&
            $user = $this->toolkit->getUser($request);

            // Vérifier si l'utilisateur est un patient
            $patient = $this->entityManager->getRepository(Patient::class)->findOneBy(['user' => $user]);

            // Si un patient est trouvé, appliquer le filtre sur l'ID du patient
            if ($patient) {
                $filtre['patient_id'] = $patient->getId();
            }

            // Vérifier si l'utilisateur est un médecin
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);

            // Si un médecin est trouvé, appliquer le filtre sur l'ID du médecin
            if ($doctor) {
                $filtre['doctor'] = $doctor->getId();
            }

            // Si aucun des deux n'est trouvé (pas de patient et pas de médecin), vous pouvez retourner une erreur
            if (!$patient && !$doctor) {
                return new JsonResponse(['code' => 404, 'message' => "Aucun patient ou médecin trouvé pour cet utilisateur"], Response::HTTP_NOT_FOUND);
            }
            //si c'est un agent hospitalier
            if ($this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
                $agenthospital = $this->entityManager->getRepository(AgentHospital::class)->findOneBy(['user' => $user])->getHospital()->getId();
                $filtre['hospital'] = $agenthospital;
            }

            // Récupération des Meetings avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Meeting', 'meeting:read', $filtre);

            // Retour d'une réponse JSON avec les Meetings et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>'Erreur interne du serveur' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function show(Meeting $meeting, Request $request): Response
    {
        try {
            $user = $this->toolkit->getUser($request);

            // Vérification des autorisations de base
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR') && !$this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            
            //  Règles de visibilité selon le rôle :
            if ($this->security->isGranted('ROLE_PATIENT')) {
                // Le patient ne peut voir que ses propres rendez-vous
                if ($meeting->getPatientId()?->getUser()?->getId() !== $user->getId()) {
                    return new JsonResponse(['code' => 403, 'message' => "Ce rendez-vous ne vous appartient pas"], Response::HTTP_FORBIDDEN);
                }
            }

            if ($this->security->isGranted('ROLE_DOCTOR')) {
                // Le médecin ne peut voir que ses rendez-vous
                if ($meeting->getPatientId()?->getUser()?->getId() !== $user->getId()) {
                    return new JsonResponse(['code' => 403, 'message' => "Ce rendez-vous n'est pas lié à vous"], Response::HTTP_FORBIDDEN);
                }
            }

            // Sérialisation du rendez-vous
            $serialized = $this->serializer->serialize($meeting, 'json', ['groups' => 'meeting:read']);

            return new JsonResponse([
                "data" => json_decode($serialized, true),
                "code" => 200
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
                return new JsonResponse(['code' => 500, 'message' =>'Erreur interne du serveur' . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
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
        if (!$this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

            $user = $this->toolkit->getUser($request);
            $agenthospital = $this->entityManager->getRepository(AgentHospital::class)->findOneBy(['user' => $user])->getHospital()->getId();

            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data['state_id'] = 1;

            $data['hospital'] = $agenthospital;

            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Meeting", $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            if (key_exists("disponibilites", $data)) {
                // Si l'entité a été correctement enregistrée, on traite les disponibilités
                $this->entityManager->getRepository("App\Entity\Disponibilite")->find($data["disponibilites"])->setMeeting($errors['entity']);
                $this->entityManager->flush();
                # code...
            }
            // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'meeting:read']);
                $response = json_decode($response, true);
            return $this->json(['data' => $response,'code' => 200, 'message' => "Meeting crée avec succès"], Response::HTTP_OK);
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
        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
        // Conversion de la date en objet DateTime
        $data["date"] = new \DateTime($data["date"]);
        if ($data["state_id"] == 3) {
            $data_update = [
                "state_id" => $data["state_id"],
                "heure" => $data["heure"],
                "date" => $data["date"],
                "id" => $data["id"],
            ];
            // Appel à la méthode persistEntity pour mettre à jour l'entité Meeting dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Meeting", $data_update, true);
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                    $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'meeting:read']);
                    $response = json_decode($response, true);
                return $this->json(['data' => $response,'data' => $errors['entity'],'code' => 200, 'message' => "Meeting modifié avec succès"], Response::HTTP_OK);
            }
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Meeting"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }else {
            $data_update = [
                "state_id" => $data["state_id"],
                "id" => $data["id"],
            ];
            // Appel à la méthode persistEntity pour mettre à jour l'entité Meeting dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Meeting", $data_update, true);
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'meeting:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'data' => $errors['entity'],'code' => 200, 'message' => "Meeting modifié avec succès"], Response::HTTP_OK);
            }
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Meeting"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN_SIS')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Meeting passée en paramètre
            $entityManager->remove($meeting);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Meeting supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' => "Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
