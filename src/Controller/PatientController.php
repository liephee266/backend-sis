<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Patient;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\Consultation;
use App\Entity\HospitalAdmin;
use App\Entity\DossierMedicale;
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
 * Controleur pour la gestion des Patients
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/patients')]
#[ApiEntity(\App\Entity\Patient::class)]

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
     * Liste des Patients
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'patient_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Vérification des autorisations
            if(!$this->toolkit->hasRoles(['ROLE_PATIENT', 'ROLE_DOCTOR', 'ROLE_AGENT_HOSPITAL', 'ROLE_ADMIN_SIS', 'ROLE_SUPER_ADMIN', 'ROLE_ADMIN_HOSPITAL'])) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            $user = $this->toolkit->getUser($request);
            // Si utilisateur est un médecin
            if ($this->security->isGranted('ROLE_DOCTOR')) {
                $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);
                if (!$doctor) {
                    return new JsonResponse(['code' => 403, 'message' => "Médecin non trouvé"], Response::HTTP_FORBIDDEN);
                }
                // Patients via consultations
                $consultationPatients = $this->entityManager->getRepository(Consultation::class)
                    ->createQueryBuilder('c')
                    ->select('DISTINCT p.id')
                    ->innerJoin('c.patient', 'p')
                    ->where('c.doctor = :doctor')
                    ->setParameter('doctor', $doctor)
                    ->getQuery()
                    ->getScalarResult();
                $consultationPatientIds = array_column($consultationPatients, 'id');

                // Patients créés par le docteur
                $createdPatients = $this->entityManager->getRepository(Patient::class)
                    ->createQueryBuilder('p')
                    ->select('p.id')
                    ->where('p.created_by = :doctor')
                    ->setParameter('doctor', $user->getId())
                    ->getQuery()
                    ->getScalarResult();
                $createdPatientIds = array_column($createdPatients, 'id');

                // Fusionner les deux ensembles d'IDs
                $allPatientIds = array_unique(array_merge($consultationPatientIds, $createdPatientIds));

                if (empty($allPatientIds)) {
                    return new JsonResponse([
                        'code' => 204,
                        'message' => 'Aucun patient trouvé.'
                    ], Response::HTTP_NO_CONTENT);
                }

                // Appliquer le filtre dans la pagination
                $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read', [
                    'id' => $allPatientIds
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
                    ->createQueryBuilder('c')
                    ->where('c.hospital = :hospital')
                    ->setParameter('hospital', $adminHospital)
                    ->getQuery()
                    ->getResult();

                // Extraire les patients uniques
                $ids = array_map(function($obj) {
                    return $obj->getPatient()->getId();
                }, $consultations);

                $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read', [
                    'id' => $ids
                ]);
            }
            // Autres rôles : récupérer tous les patients
            else {
                $response = $this->toolkit->getPagitionOption($request, 'Patient', 'patient:read');
            }

            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->toolkit->hasRoles(['ROLE_DOCTOR', 'ROLE_AGENT_HOSPITAL', 'ROLE_ADMIN_SIS', 'ROLE_SUPER_ADMIN', 'ROLE_ADMIN_HOSPITAL'])) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            $user = $this->toolkit->getUser($request);

            // Vérification si l'utilisateur est un médecin
            if ($this->toolkit->hasRoles(['ROLE_DOCTOR'])) {
                // Récupérer le médecin de l'utilisateur connecté
                $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user]);
                if (!$doctor) {
                    return new JsonResponse(['code' => 403, 'message' => "Médecin non trouvé"], Response::HTTP_FORBIDDEN);
                }
                // Récupérer les consultations du médecin
                $consultations = $this->entityManager->getRepository(Consultation::class)
                    ->createQueryBuilder('c')
                    ->where('c.doctor = :doctor')
                    ->setParameter('doctor', $doctor)
                    ->getQuery()
                    ->getResult();
                // Vérifier si une consultation pour ce patient existe
                $patientFound = false;
                foreach ($consultations as $consultation) {
                    if ($consultation->getPatient()->getId() === $patient->getId()) {
                        $patientFound = true;
                        break;
                    }
                }
                
                // Vérifie si le médecin a créé ce patient
                $isCreator = $patient->getCreatedBy() === $user;
                if (!$patientFound && !$isCreator) {
                    return new JsonResponse([
                        'code' => 403,
                        'message' => "Ce patient n'a pas de consultation avec vous."
                    ], Response::HTTP_FORBIDDEN);
                }

                return new JsonResponse([
                    'data' => json_decode($this->serializer->serialize($patient, 'json', ['groups' => 'patient:read']), true),
                    'code' => 200,
                ], Response::HTTP_OK);
                    
            } elseif ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                // Récupérer l'admin hospitalier de l'utilisateur connecté
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                    ->findOneBy(['user' => $user]);
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        'code' => 403,
                        'message' => "Aucun hôpital trouvé pour cet admin."
                    ], Response::HTTP_FORBIDDEN);
                }
                // Récupérer les consultations liées à cet hôpital
                $consultations = $this->entityManager->getRepository(Consultation::class)
                    ->createQueryBuilder('c')
                    ->where('c.hospital = :hospital')
                    ->setParameter('hospital', $hospitalAdmin->getHospital()->getId())
                    ->getQuery()
                    ->getResult();
                // Vérifier si une consultation pour ce patient existe
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
            
            // $patient = $dossierMedicale->getPatientId(); // ✅ Récupération du patient à partir du dossier
            // $serializationGroup = $this->toolkit->getPatientSerializationGroup($user, $dossierMedicale);
            // dd($serializationGroup);
        
            $patientJson = $this->serializer->serialize($patient, 'json', ['groups' => 'patient:read']);

            return new JsonResponse([
                'data' => json_decode($patientJson, true),
                'code' => 200,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Création d'un Patient par le patient lui même
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/me', name: 'patient_create_me', methods: ['POST'])]
    public function create_me(Request $request): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data["password"] = $data["password"] ?? 123456789;
            
            // Début de la transaction
            $this->entityManager->beginTransaction();

            // Création du User
            $user_data = [
                'email' => $data['email'],
                'password' => $data['password'],
                'roles' => ["ROLE_PATIENT"],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'nickname' => $data['nickname']?? null,
                'tel' => $data['tel'],
                'birth' => new \DateTime($data['birth']),
                'gender' => $data['gender'],
                'address' => $data['address']?? null,
                'image' => $data['image']?? null,
            ];
            $data['signaler_comme_decedé'] = false;
            $dat['poids'] = 0;
            $data['taille'] = 0;
            $data['signaler_comme_decedé'] = false;
            $data['nom_urgence'] = "NULL";
            $data['numero_urgence'] = "NULL";
            $data['adresse_urgence'] = "NULL";
            $data['groupe_sanguins'] = "NULL";
            
            // Appel à la méthode persistEntityUser pour insérer les données du User dans la base
            $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Patient", $user_data, $data);
            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $this->entityManager->commit();
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'patient:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Patient crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création du Patient"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Création d'un nouveau Patient par un utilisateur autorisé
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'patient_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->toolkit->hasRoles(['ROLE_DOCTOR', 'ROLE_AGENT_HOSPITAL', 'ROLE_ADMIN_SIS', 'ROLE_SUPER_ADMIN'])) {
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data["password"] = $data["password"] ?? 123456789;
            
            // Début de la transaction
            $this->entityManager->beginTransaction();

            // Création du User
            $user_data = [
                'email' => $data['email'],
                'password' => $data['password'],
                'roles' => ["ROLE_PATIENT"],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'nickname' => $data['nickname']?? null,
                'tel' => $data['tel'],
                'birth' => new \DateTime($data['birth']),
                'gender' => $data['gender'],
                'address' => $data['address']?? null,
                'image' => $data['image']?? null,
            ];
            $data['signaler_comme_decedé'] = false;
            $data['created_by'] = $this->toolkit->getUser($request)->getId();
            // Appel à la méthode persistEntityUser pour insérer les données du User dans la base
            $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Patient", $user_data, $data);
            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $this->entityManager->commit();
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'patient:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Patient crée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création du Patient"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            // Vérification des autorisations de l'utilisateur connecté
            if (!$this->security->isGranted('ROLE_PATIENT')) {
                // Si l'utilisateur n'a pas les autorisations, retour d'une réponse JSON avec une erreur 403 (Interdit)
                return new JsonResponse(['code' => 403, 'message' => "Accès refusé"], Response::HTTP_FORBIDDEN);
            }

            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Doctor dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Patient", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'patient:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Patient modifié avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification du patient"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
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
        } catch (\Throwable $th) {
            return new JsonResponse(['code' => 500, 'message' =>"Erreur interne du serveur" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}