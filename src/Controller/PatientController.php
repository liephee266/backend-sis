<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\Doctor;
use App\Entity\HospitalAdmin;
use App\Entity\Patient;
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
 * Controleur pour la gestion des Patient
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/patients')]
class PatientController extends AbstractController
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
        $this->security = $security;
    }

    /**
     * Liste des Patient
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'patient_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        // Vérification des autorisations
        if (
            !$this->security->isGranted('ROLE_DOCTOR') &&
            !$this->security->isGranted('ROLE_AGENT_ACCUEIL') &&
            !$this->security->isGranted('ROLE_ADMIN_SIS') &&
            !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
            !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
        ) {
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        $user = $this->toolkit->getUser($request);

        // Si utilisateur est un médecin
        if ($this->security->isGranted('ROLE_DOCTOR')) {
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);

            if (!$doctor) {
                return new JsonResponse(['code' => 403, 'message' => "Médecin non trouvé"], Response::HTTP_FORBIDDEN);
            }

            // Récupérer les patients associés au médecin via consultations
            $consultations = $this->entityManager->getRepository(Consultation::class)
                ->createQueryBuilder('c')
                ->join('c.patient', 'p')
                ->where('c.doctor = :doctor')
                ->setParameter('doctor', $doctor)
                ->getQuery()
                ->getResult();

            $patients = [];
            foreach ($consultations as $consultation) {
                $patients[] = $consultation->getPatient();
            }

            $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read', [
                'patient' => $patients
            ]);
        }

        // Si utilisateur est un admin hospitalier
        elseif ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
            $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                ->findOneBy(['user' => $user]);

            if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                return new JsonResponse([
                    'code' => 403,
                    'message' => "Aucun hôpital trouvé pour cet admin."
                ], Response::HTTP_FORBIDDEN);
            }

            $adminHospital = $hospitalAdmin->getHospital()->getId();

            // Récupérer les consultations liées à cet hôpital
            $consultations = $this->entityManager->getRepository(Consultation::class)
                ->createQueryBuilder('c.patient', 'p')
                ->where('c.hospital = :hospital')
                ->setParameter('hospital', $adminHospital)
                ->getQuery()
                ->getResult();

            // Extraire les patients uniques
            $patients = [];
            $seen = [];

            foreach ($consultations as $consultation) {
                $patient = $consultation->getPatient();
                if (!isset($seen[$patient->getId()])) {
                    $patients[] = $patient;
                    $seen[$patient->getId()] = true;
                }
            }
            dd($patients);
            $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read', [
                'patient' => $patients
            ]);
        }

        // Autres rôles : récupérer tous les patients
        else {
            $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read');
        }

        return new JsonResponse($response, Response::HTTP_OK);
    }
    /**
     * Affichage d'un Patient par son ID
     *
     * @param Patient $Patient
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'patient_show', methods: ['GET'])]
    public function show(Patient $patient, Request $request): Response
    {
        // Vérification des autorisations de l'utilisateur connecté
        if (
            !$this->security->isGranted('ROLE_DOCTOR') &&
            !$this->security->isGranted('ROLE_AGENT_ACCUEIL') &&
            !$this->security->isGranted('ROLE_ADMIN_SIS') &&
            !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
            !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
        ) {
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        $user = $this->toolkit->getUser($request);

        // Vérification si l'utilisateur est un admin de l'hôpital
        if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
            // Récupérer l'HospitalAdmin de l'utilisateur connecté
            $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                ->findOneBy(['user' => $user]);

            if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                return new JsonResponse([
                    'code' => 403,
                    'message' => "Aucun hôpital trouvé pour cet admin."
                ], Response::HTTP_FORBIDDEN);
            }

            $adminHospital = $hospitalAdmin->getHospital();


            // Récupérer les consultations de l'hôpital de l'admin
            $consultations = $this->entityManager->getRepository(Consultation::class)
                ->createQueryBuilder('c')
                ->join('c.patient', 'p')
                ->where('c.hospital = :hospital')
                ->setParameter('hospital', $adminHospital)
                ->getQuery()
                ->getResult();

            // Vérifier si une consultation pour ce patient existe dans cet hôpital
            $patientFound = false;
            foreach ($consultations as $consultation) {
                if ($consultation->getPatient()->getId() === $patient->getId()) {
                    $patientFound = true;
                    break;
                }
            }

            if (!$patientFound) {
                return new JsonResponse([
                    'code' => 403,
                    'message' => "Ce patient n'a pas de consultation dans votre hôpital."
                ], Response::HTTP_FORBIDDEN);
            }
        }

        // Sérialisation du patient avec le groupe de sérialisation 'patient:read'
        $patientJson = $this->serializer->serialize($patient, 'json', ['groups' => 'patient:read']);

        return new JsonResponse([
            'data' => json_decode($patientJson, true),
            'code' => 200
        ], Response::HTTP_OK);
    }
    

    /**
     * Création d'un nouvel Patient
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'patient_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        //  // Vérification des autorisations de l'utilisateur connecté
        // if (!$this->security->isGranted('ROLE_DOCTOR') && !$this->security->isGranted('ROLE_AGENT_ACCEUIL'))  {
        //     // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
        //     return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        // }
        // Décodage du contenu JSON envoyé dans la requête
        $data = json_decode($request->getContent(), true);
        
        // Début de la transaction
        $this->entityManager->beginTransaction();

        // Création du User
        $user_data = [
            'email' => $data['email'],
            'password' => $data['password'],
            'roles' => ["ROLE_PATIENT"],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'nickname' => $data['nickname'],
            'tel' => $data['tel'],
            'birth' => new \DateTime($data['birth']),
            'gender' => $data['gender'],
            'address' => $data['address'],
        ];
        
        // Appel à la méthode persistEntityUser pour insérer les données du User dans la base
        $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Patient", $user_data, $data);

        // Vérification des erreurs après la persistance des données
        if (!empty($errors['entity'])) {
            // Si l'entité a been correctement enregistrée, retour d'une réponse JSON avec успех
            $this->entityManager->commit();
            return $this->json(['code' => 200, 'message' => "Patient crée avec succès"], Response::HTTP_OK);
        }

        // Si une erreur se produit, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la création du Patient"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Modification d'un Patient par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'patient_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_PATIENT')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
        $data = json_decode($request->getContent(), true);
    
        // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
        $data['id'] = $id;
    
        // Appel à la méthode persistEntity pour mettre à jour l'entité Patient dans la base de données
        $errors = $this->genericEntityManager->persistEntity("App\Entity\Patient", $data, true);
    
        // Vérification si l'entité a été mise à jour sans erreur
        if (!empty($errors['entity'])) {
            // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Patient modifié avec succès"], Response::HTTP_OK);
        }
    
        // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
        return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de l'Patient"], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    
    /**
     * Suppression d'un Patient par son ID
     * 
     * @param Patient $Patient
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'patient_delete', methods: ['DELETE'])]
    public function delete(Patient $patient, EntityManagerInterface $entityManager): Response
    {
        // Vérification des autorisations de l'utilisateur connecté
        if (!$this->security->isGranted('ROLE_DOCTOR')) {
            // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
            return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
        }

        // Suppression de l'entité Patient passée en paramètre
        $entityManager->remove($patient);
    
        // Validation de la suppression dans la base de données
        $entityManager->flush();
    
        // Retour d'une réponse JSON avec un message de succès
        return $this->json(['code' => 200, 'message' => "Patient supprimé avec succès"], Response::HTTP_OK);
    }
}
