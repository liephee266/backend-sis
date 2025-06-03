<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Services\Toolkit;
use App\Attribute\ApiEntity;
use App\Entity\Disponibilite;
use App\Services\GenericEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\SecurityBundle\Security;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controleur pour la gestion des Disponibilités
 * 
 * @author  Orphée Lié <lieloumloum@gmail.com>
 */
#[Route('/api/v1/disponibilite', name: 'disponibilite')]
#[ApiEntity(\App\Entity\Disponibilite::class)]

class DisponibiliteController extends AbstractController
{
    private $toolkit;
    private $entityManager;
    private $serializer;
    private $genericEntityManager;
    private $security;

    public function __construct(GenericEntityManager $genericEntityManager, Security $security,EntityManagerInterface $entityManager, SerializerInterface $serializer, Toolkit $toolkit)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->genericEntityManager = $genericEntityManager;
        $this->security = $security;
    }

    /**
     * Liste des Disponibilités
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'disponibilite_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        try {
            // Tableau de filtres initialisé vide (peut être utilisé pour filtrer les résultats)
            $filtre = [];

            if ($this->security->isGranted('ROLE_DOCTOR')) {
                // Si l'utilisateur a le rôle de médecin, on récupère son ID
                $user = $this->toolkit->getUser($request)->getId();

                $doctor = $this->entityManager->getRepository('App\Entity\Doctor')->findOneBy(['user' => $user])->getId();
                // On ajoute le filtre pour ne récupérer que les disponibilités du médecin
                $filtre = [
                    'doctor' => $doctor,

                ];
            }else{
                
                $filtre =[
                    'meeting'=> null,
                ];
            }
            // Récupération des Disponibilités avec pagination
            $response = $this->toolkit->getPagitionOption($request, 'Disponibilite', 'disponibilite:read', $filtre);

            // Retour d'une réponse JSON avec les Disponibilités et un statut HTTP 200 (OK)
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Affichage d'une Disponibilité par son ID
     *
     * @param disponibilite $disponibilite
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'disponibilite_show', methods: ['GET'])]
    public function show(Disponibilite $disponibilite): Response
    {
        try {
            // Sérialisation de l'entité disponibilite en JSON avec le groupe de sérialisation 'disponibilite:read'
            $disponibilite = $this->serializer->serialize($disponibilite, 'json', ['groups' => 'disponibilite:read']);
        
            // Retour de la réponse JSON avec les données de la disponibilité et un code HTTP 200
            return new JsonResponse(["data" => json_decode($disponibilite, true), "code" => 200], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }        

    /**
     * Création d'une nouvelle Disponibilité
     *
     * @param Request $request
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/', name: 'disponibilite_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {

            if (!$this->toolkit->hasRoles(['ROLE_DOCTOR'])) {
                // Si l'utilisateur n'a pas le rôle de médecin, retour d'une erreur 403 (Accès interdit)
                return new JsonResponse(["message" => 'Accès interdit', "code" => 403], Response::HTTP_FORBIDDEN);
            }

            $user = $this->toolkit->getUser($request);
            $doctorId = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $user->getId()])->getId();
            // Décodage du contenu JSON envoyé dans la requête
            $data = json_decode($request->getContent(), true);

            $data['doctor'] = $doctorId; 
            
            // Appel à la méthode persistEntity pour insérer les données dans la base
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Disponibilite", $data);

            // Vérification des erreurs après la persistance des données
            if (!empty($errors['entity'])) {
                // Si l'entité a été correctement enregistrée, retour d'une réponse JSON avec succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'disponibilite:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Disponibilité créée avec succès"], Response::HTTP_OK);
            }

            // Si une erreur se produit, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la création de la disponibilité"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Modification d'une Disponibilité par son ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'disponibilite_update', methods: ['PUT'])]
    public function update(Request $request,  $id): Response
    {
        try {
            // Décodage du contenu JSON envoyé dans la requête pour récupérer les données
            $data = json_decode($request->getContent(), true);
        
            // Ajout de l'ID dans les données reçues pour identifier l'entité à modifier
            $data['id'] = $id;
        
            // Appel à la méthode persistEntity pour mettre à jour l'entité disponibilite dans la base de données
            $errors = $this->genericEntityManager->persistEntity("App\Entity\Disponibilite", $data, true);
        
            // Vérification si l'entité a été mise à jour sans erreur
            if (!empty($errors['entity'])) {
                // Si l'entité a été mise à jour, retour d'une réponse JSON avec un message de succès
                $response = $this->serializer->serialize($errors['entity'], 'json', ['groups' => 'disponibilite:read']);
                $response = json_decode($response, true);
                return $this->json(['data' => $response,'code' => 200, 'message' => "Disponibilité modifiée avec succès"], Response::HTTP_OK);
            }
        
            // Si une erreur se produit lors de la mise à jour, retour d'une réponse JSON avec une erreur
            return $this->json(['code' => 500, 'message' => "Erreur lors de la modification de la disponibilité"], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Suppression d'une Disponibilité par son ID
     * 
     * @param Disponibilite $disponibilite
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     * @author  Orphée Lié <lieloumloum@gmail.com>
     */
    #[Route('/{id}', name: 'disponibilite_delete', methods: ['DELETE'])]
    public function delete(Disponibilite $disponibilite, EntityManagerInterface $entityManager): Response
    {
        try {
            // Suppression de l'entité Disponibilité passée en paramètre
            $entityManager->remove($disponibilite);
        
            // Validation de la suppression dans la base de données
            $entityManager->flush();
        
            // Retour d'une réponse JSON avec un message de succès
            return $this->json(['code' => 200, 'message' => "Disponibilité supprimé avec succès"], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(["message" => 'Erreur interne du serveur' . $th->getMessage(), "code" => 500], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}