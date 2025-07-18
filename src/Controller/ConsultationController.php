<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\Consultation;
use App\Entity\AgentHospital;
use App\Entity\HospitalAdmin;
use App\Entity\HistoriqueMedical;
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
 * Controleur pour la gestion des Consultation
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/consultations')]
#[ApiEntity(\App\Entity\Consultation::class)]
class ConsultationController extends AbstractController
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
     * Liste des Consultation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'consultation_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des autorisations
            if (
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT')
            ) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            $user = $this->toolkit->getUser($request);
            $filtre = [];

            // Si utilisateur est un médecin
            if ($this->security->isGranted('ROLE_DOCTOR')) {
                $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);

                if (!$doctor) {
                    return new JsonResponse(['code' => 403, 'message' => "Médecin non trouvé"], Response::HTTP_FORBIDDEN);
                }

                // Pour un médecin, filtrer par ses consultations
                $consultations = $this->entityManager->getRepository(Consultation::class)
                    ->createQueryBuilder('c')
                    ->select('c.id')
                    ->where('c.doctor = :doctor')
                    ->setParameter('doctor', $doctor)
                    ->getQuery()
                    ->getScalarResult();

                $consultationIds = array_column($consultations, 'id');
                // Filtrer par ID
                $filtre['id'] = $consultationIds;

            }
            // Si utilisateur est un patient
            if ($this->security->isGranted('ROLE_PATIENT')) {
                $patient = $this->entityManager->getRepository(Patient::class)->findOneBy(['user' => $user]);

                if (!$patient) {
                    return new JsonResponse(['code' => 403, 'message' => "Patient non trouvé"], Response::HTTP_FORBIDDEN);
                }

       
            // Récupérer toutes les consultations du patient
            $consultations = $patient->getConsultations();  // Accéder à la relation OneToMany

            // Si le patient n'a pas de consultations
            if (empty($consultations)) {
                return new JsonResponse(['code' => 404, 'message' => "Aucune consultation trouvée"], Response::HTTP_NOT_FOUND);
            }
                 // Extraire les IDs des consultations
                $consultationIds = array_map(function($consultation) {
                    return $consultation->getId();
                }, $consultations->toArray());
                // Filtrer par ID
                $filtre['id'] = $consultationIds;
            }

            // Si autres rôles, pas de filtre
            $response = $this->toolkit->getPagitionOption($request, 'Consultation', 'consultation:read', $filtre);

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Affichage d'un Consultation par son ID
     *
     * @param Consultation $Consultation
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation, Request  $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT') && !$this->security->isGranted('ROLE_DOCTOR')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Récupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);

            // Récupérer le patient lié à l'utilisateur connecté
            $patient = $this->entityManager->getRepository(Patient::class)->findOneBy(['user' => $user]);

            // Si l'utilisateur est un patient, on vérifie qu'il est associé à cette consultation
            if ($this->security->isGranted('ROLE_PATIENT')) {
                // On vérifie si le patient est bien associé à cette consultation
                if ($consultation->getPatient()->getId() !== $patient->getId()) {
                    // Si ce n'est pas le cas, retour d'une réponse JSON avec une erreur 403 (Accès refusé)
                    return new JsonResponse(['code' => 403, 'message' => "Accès refusé. Vous ne pouvez pas accéder à cette consultation."], Response::HTTP_FORBIDDEN);
                }
            }

            // Récupérer le patient lié à l'utilisateur connecté
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);

            // Si l'utilisateur est un médecin, il peut voir toutes les consultations qui lui sont attribuées
            if ($this->security->isGranted('ROLE_DOCTOR')) {
                // Vérifie si la consultation est liée à ce médecin
                if ($consultation->getDoctor()->getId() !== $doctor->getId()) {
                    // Si ce n'est pas le cas, retour d'une réponse JSON avec une erreur 403 (Accès refusé)
                    return new JsonResponse(['code' => 403, 'message' => "Accès refusé. Vous ne pouvez pas accéder à cette consultation."], Response::HTTP_FORBIDDEN);
                }
            }

            // Sérialisation de l'entité Consultation en JSON avec le groupe de sérialisation 'Consultation:read'
            $consultation = $this->serializer->serialize($consultation, 'json', ['groups' => 'consultation:read']);

            // Retour de la réponse JSON avec les données de l'Consultation et un code HTTP 200
            return new JsonResponse(["data" => json_decode($consultation, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Création d'un nouvel Consultation
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'consultation_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_DOCTOR'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data["dateSymptoms"] = new \DateTime($data["dateSymptoms"]);

            $data["prochaine_consultation"] = new \DateTime($data["prochaine_consultation"]);

             // Vérifie que l’ID du patient est fourni
            if (!isset($data['patient'])) {
                return new JsonResponse(['code' => 400, 'message' => "ID du patient manquant"], Response::HTTP_BAD_REQUEST);
            }

            // Récupération de l’entité Patient
            $patient = $this->entityManager->getRepository(Patient::class)->find($data['patient']);

            if (!$patient) {
                return new JsonResponse(['code' => 404, 'message' => "Patient introuvable"], Response::HTTP_NOT_FOUND);
            }

            //on Vérifie si un historique médical existe déjà pour ce patient
            $historiqueMedical = $this->entityManager->getRepository(HistoriqueMedical::class)
                ->findOneBy(['patient' => $patient]);

            // Si non, on le crée
            if (!$historiqueMedical) {
                $historiqueMedical = new HistoriqueMedical();
                $historiqueMedical->setPatient($patient);
                $this->entityManager->persist($historiqueMedical);
                $this->entityManager->flush(); // Nécessaire pour générer l’ID
            }

            // Injecte l’historique médical dans les données de la consultation
            $data['historiqueMedical'] = $historiqueMedical->getId();

                        // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Consultation", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctemenm:ù enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'consultation:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Consultation crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de l'Consultation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Consultation par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'consultation_update', methods: ['PUT'])]

    public function update(Request $request,  $id): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_DOCTOR') && !$this->security->isGranted('ROLE_AGENT_HOSPITAL'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Récupération de l'utilisateur connecté
            $user = $this->toolkit->getUser($request);
            
            // Vérifier si l'utilisateur est un médecin
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);

            // Si un médecin est trouvé, appliquer le filtre sur l'ID du médecin
            if ($doctor) {
                $filtre['doctor'] = $doctor->getId();
            }

            // Si aucun des deux n'est trouvé (pas de patient et pas de médecin), vous pouvez retourner une erreur
            if (!$doctor) {
                return new JsonResponse(['code' => 404, 'message' => "Aucun médecin trouvé pour cet utilisateur"], Response::HTTP_NOT_FOUND);
            }

            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Consultation dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Consultation", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'consultation:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Consultation modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Consultation"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'un Consultation par son ID
     * 
     * @param Consultation $Consultation
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'consultation_delete', methods: ['DELETE'])]
    public function delete(Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_ADMIN') && !$this->security->isGranted('ROLE_AGENT_ACCEUIL'))  {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Consultation passée en paramètre
            $entityManager->remove($consultation);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Consultation supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}