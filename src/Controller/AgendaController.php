<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\AgentHospital;
use App\Entity\Disponibilite;
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
 * Controleur pour la gestion des Agenda
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/agendas')]
#[ApiEntity(\App\Entity\Agenda::class)]
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
    #[Route('/{id_hospital}/{id_doctor}/{month?}/{year?}', name: 'agenda_index', methods: ['GET'])]
    public function index(Request $request, $id_hospital, $id_doctor, $month = null, $year = null): Response
    {
        // Par défaut, on prend l'année actuelle si non fournie
        $year = $year ?? (int) date('Y');
    
        // Validation simple de l'année (optionnelle : tu peux l'améliorer)
        if (!is_numeric($year) || $year < 2000 || $year > 2100) {
            return new JsonResponse(['code' => 400, 'message' => 'Année invalide.'], Response::HTTP_BAD_REQUEST);
        }
        // Gestion des mois : mois courant ou tous les mois
        if ($month === null) {
            $months = range(1, 12); // Tous les mois
        } else {
            $month = (int) $month;
            if ($month < 1 || $month > 12) {
                return new JsonResponse(['code' => 400, 'message' => 'Mois invalide.'], Response::HTTP_BAD_REQUEST);
            }
            $months = [$month, $month === 12 ? 1 : $month + 1];
        }
    
        $filters = [
            'months' => $months,
            'year' => $year,
        ];
    
        // ROLE_DOCTOR : accès à son propre agenda
        if ($this->security->isGranted('ROLE_DOCTOR')) {
            $a = $this->toolkit->getAgenda($filters, [
                'id_doctor' => $id_doctor, 
                'id_hospital' => $id_hospital]);
            return new JsonResponse(["data" => $a, "code" => 200], Response::HTTP_OK);
    
        // ROLE_PATIENT : accès à son propre agenda avec un médecin et un hôpital
    } elseif ($this->security->isGranted('ROLE_PATIENT')) {
        $doctor = $this->entityManager->getRepository(Doctor::class)->find($id_doctor);
        if (!$doctor) {
            return new JsonResponse(['code' => 404, 'message' => 'Médecin non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $agendaData = $this->toolkit->getAgendaAgentHospital($filters, [
            'id_doctor' => $doctor,
            'id_hospital' => $id_hospital,
            'meeting' => null
        ]);

        return new JsonResponse(["data" => $agendaData, "code" => 200], Response::HTTP_OK);

        // ROLE_RECEPTIONIST (agent d'accueil)
    }elseif ($this->security->isGranted('ROLE_AGENT_HOSPITAL')) {
            $currentUser = $this->toolkit->getUser($request)->getId();
            $idAgenttHospital = $this->entityManager->getRepository(AgentHospital::class)->findOneBy(['user' => $currentUser])->getHospital()->getId();

            // Vérification que le médecin appartient à l'hôpital de l'agent d'accueil
            $doctor = $this->entityManager->getRepository(Doctor::class)->find($id_doctor);
            if (!$doctor->getHospital()->exists(fn($key, $hopital) => $hopital->getId() === $idAgenttHospital)) {
                return new JsonResponse(['code' => 403, 'message' => 'Accès refusé : ce médecin n’appartient pas à votre hôpital.'], Response::HTTP_FORBIDDEN);
            }

            $agendaData = $this->toolkit->getAgendaAgentHospital($filters, [
                'id_doctor' => $id_doctor,
                'id_hospital' => $idAgenttHospital,
                'meeting' => null
            ]);

            return new JsonResponse(["data" => $agendaData, "code" => 200], Response::HTTP_OK);
        }

        // Accès non autorisé
        return new JsonResponse(['code' => 403, 'message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
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
