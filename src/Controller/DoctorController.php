<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Hospital;
use App\Services\Toolkit;
use App\Entity\HospitalAdmin;
use App\Entity\DoctorHospital;
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

        try {
            //code...
            if (
                !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT') &&
                !$this->security->isGranted('ROLE_SUPER_ADMIN')
               )
        
             {
                return new JsonResponse([
                    "message" => "Vous n'avez pas accès à cette ressource",
                    "code" => 403
                ], Response::HTTP_FORBIDDEN);
            }
    
            $filtre = [];
    
            // Si c'est un admin hospitalier, on filtre les docteurs liés à son hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
    
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                    ->findOneBy(['user' => $user]);

    
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        "message" => "Aucun hôpital associé à cet admin",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
                
                $hospital = $hospitalAdmin->getHospital();

                $doctors = $this->entityManager->createQueryBuilder()
                    ->select('d')
                    ->from(Doctor::class, 'd')
                    ->join('d.hospital', 'h') // 'hospital' étant la propriété ManyToMany dans Doctor
                    ->where('h = :hospital')
                    ->setParameter('hospital', $hospital)
                    ->getQuery()
                    ->getResult();

                   
                $doctorIds = array_map(function ($doctor) {
                    return $doctor->getId();
                }, $doctors);

                // Ajouter ce filtre pour n'afficher que les médecins de cet hôpital
                $filtre['id'] = $doctorIds;
            }
    
            $response = $this->toolkit->getPagitionOption($request, 'Doctor', 'doctor:read', $filtre);
    
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->json(['code' => 500, 'message' => "Une erreur s'est produite" . $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function show(Doctor $doctor, Request $request): Response
    {
        try {
            //code...
            if (
                !$this->security->isGranted('ROLE_SUPER_ADMIN') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_DOCTOR') &&
                !$this->security->isGranted('ROLE_PATIENT')
            ) {
                return new JsonResponse([
                    "message" => "Vous n'avez pas accès à cette ressource",
                    "code" => 403
                ], Response::HTTP_FORBIDDEN);
            }
             // Vérification si l'utilisateur est un admin hospitalier
        if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
            $user = $this->toolkit->getUser($request);

            $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)
                ->findOneBy(['user' => $user]);

            if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                return new JsonResponse([
                    "message" => "Aucun hôpital associé à cet administrateur.",
                    "code" => 403
                ], Response::HTTP_FORBIDDEN);
            }

            $hospital = $hospitalAdmin->getHospital();

            // Vérifier que le docteur appartient bien à cet hôpital (relation ManyToMany)
            if (!$doctor->getHospital()->contains($hospital)) {
                return new JsonResponse([
                    "message" => "Ce médecin n'est pas rattaché à votre hôpital.",
                    "code" => 403
                ], Response::HTTP_FORBIDDEN);
            }
        }

        // Sérialisation et réponse
        $doctorData = $this->serializer->serialize($doctor, 'json', ['groups' => 'doctor:read']);

        return new JsonResponse([
            "data" => json_decode($doctorData, true),
            "code" => 200
        ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            //throw $th;
            return $this->json(['code' => 500, 'message' => "Une erreur est survenue" . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Création d'un nouvel Doctor
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Michel MIYALOU <michelmiyalou0@gmail.com>
     */
    #[Route('/', name: 'doctor_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            // Vérification des autorisations
            if (
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL') &&
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_SUPER_ADMIN')
            ) {
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }

            // Récupération et décodage des données
            $data = json_decode($request->getContent(), true);

            $data["password"] = $data["password"] ?? 123456789;

            $data["serviceStartingDate"] = new \DateTime($data['serviceStartingDate']);

            // Création du User
            $user_data = [
                'email' => $data['email'],
                'password' => $data['password'],
                'roles' => ["ROLE_DOCTOR"],
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

                $data["hospital"] = $hospitalAdmin;

                if (!$data) {
                    return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
                }

                // Démarrer la transaction
                $this->entityManager->beginTransaction();

                    $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Doctor", $user_data, $data);

                    // Vérification si l'entité a été créée sans erreur
                    if (!empty($errors['entity'])) {
                        $this->entityManager->commit();
                        $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'doctor:read']);
                        $response = json_decode($response, true);
                        return new JsonResponse(['data' => $response, 'code' => 201,'message' => "Médecin créé avec succès"], Response::HTTP_CREATED);
                    }

                    // Erreur dans la persistance
                    return $this->json(['code' => 500, 'message' => "Erreur lors de la création du médecin"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }else {

                if (!$data) {
                    return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
                }

                // Démarrer la transaction
                $this->entityManager->beginTransaction();

                    $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Doctor", $user_data, $data);

                    // Vérification si l'entité a été créée sans erreur
                    if (!empty($errors['entity'])) {
                        $this->entityManager->commit();
                        $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'doctor:read']);
                        $response = json_decode($response, true);
                        return new JsonResponse(['data' => $response,'code' => 201,'message' => "Médecin créé avec succès"], Response::HTTP_CREATED);
                    }

                    // Erreur dans la persistance
                    return $this->json(['code' => 500, 'message' => "Erreur lors de la création du médecin"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            return $this->json(['code' => 500, 'message' => "Erreur serveur: " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'un Doctor par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Michel MIYALOU, Daryon Rockness <michelmiyalou0@gmail.com>
     */
    #[Route('/{id}', name: 'doctor_update', methods: ['PUT'])]
    public function update(Request $request, $id): Response
    {
        try {
            //code...
            if (
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
            ) {
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
    
            // Décodage du JSON
            $data = json_decode($request->getContent(), true);
    
            if (!$data) {
                return $this->json(['code' => 400, 'message' => "Données invalides ou manquantes"], Response::HTTP_BAD_REQUEST);
            }
    
            // Récupération du médecin à modifier
            $doctor = $this->entityManager->getRepository(Doctor::class)->find($id);
            if (!$doctor) {
                return $this->json(['code' => 404, 'message' => "Médecin introuvable"], Response::HTTP_NOT_FOUND);
            }
    
            // Vérification que l'admin hospitalier modifie un médecin de son hôpital
            if ($this->security->isGranted('ROLE_ADMIN_HOSPITAL')) {
                $user = $this->toolkit->getUser($request);
                $hospitalAdmin = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $user]);
    
                if (!$hospitalAdmin || !$hospitalAdmin->getHospital()) {
                    return new JsonResponse([
                        "message" => "Aucun hôpital trouvé pour cet admin.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
    
                $adminHospital = $hospitalAdmin->getHospital();
    
                // Vérification via DoctorHospital
                $doctorHospital = $this->entityManager->getRepository(Hospital::class)->findOneBy([
                    'doctor' => $doctor,
                ]);
    
                if (!$doctorHospital) {
                    return new JsonResponse([
                        "message" => "Ce médecin n'appartient pas à votre hôpital.",
                        "code" => 403
                    ], Response::HTTP_FORBIDDEN);
                }
            }
    
            // Préparer les données de mise à jour
            $data['id'] = $id;
    
            $data['serviceStartingDate'] = new \DateTime($data['serviceStartingDate']);
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité Doctor dans la base de données
            $errors = $this->genericEntityManager->persistEntityUser("App\Entity\Doctor", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'doctor:read']);
                $response = json_decode($response, true);
                return new JsonResponse(['data' => $response,'code' => 200, 'message' => "Médecin modifié avec succès"], Response::HTTP_OK);
            }
    
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification du médecin"], Response::HTTP_INTERNAL_SERVER_ERROR);
    
        } catch (\Throwable $e) {
            //throw $th;
            return $this->json(['code' => 500, 'message' => "Erreur serveur: " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function delete(Doctor $doctor, EntityManagerInterface $entityManager): Response
    {

        try {
            //code...
            if (
                !$this->security->isGranted('ROLE_ADMIN_SIS') &&
                !$this->security->isGranted('ROLE_ADMIN_HOSPITAL')
            ) {
                return new JsonResponse(["message" => "Vous n'avez pas accès à cette ressource", "code" => 403], Response::HTTP_FORBIDDEN);
            }
            // Suppression de l'entité Doctor passée en paramètre
            $entityManager->remove($doctor);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Doctor supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $e) {
            //throw $e;
            return $this->json(['code' => 500, 'message' => "Erreur serveur: " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
