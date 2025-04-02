<?php

namespace App\Controller;

use App\Services\MinioService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/v1/minio')]
class MinioController extends AbstractController
{
    private MinioService $minioService;

    public function __construct(MinioService $minioService)
    {
        $this->minioService = $minioService;
    }

    /**
     * Vérifier la connexion à MinIO
     * 
     * @param MinioService $minioService
     * 
     * @return Response
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     * 
     */
    #[Route("/check-minio", name:"check_minio", methods: ['GET'])]
    public function checkMinioConnection(MinioService $minioService): Response
    {
        // Appeler la méthode pour vérifier la connexion
        $buckets = $minioService->checkConnection();

        if (is_array($buckets)) {
            // Si la connexion est réussie, afficher les buckets
            return new JsonResponse(['message'=>'Connexion réussie à MinIO ! Buckets: ', 'code'=>200], Response::HTTP_OK);
        }

        // Si une erreur est survenue, afficher l'erreur
        return new JsonResponse(['message'=>'Erreur de connexion', 'code'=>500], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Créer un bucket
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     */
    #[Route("/insert", name:"insert_minio", methods: ['POST'])]
    public function createBucket(Request $request): Response
    {
        // Récupérer le nom du bucket depuis le corps de la requête
        $data = json_decode($request->getContent(), true);
        $bucketName = $data['bucketName'] ?? '';

        if (empty($bucketName)) {
            return new JsonResponse(['message'=>'Le nom du bucket est requis', 'code'=>400], Response::HTTP_BAD_REQUEST);
        }

        // Créer le bucket via le service
        $result = $this->minioService->insertbucket($bucketName);

        return $this->json(['message'=>'Bucket crée avec succès', 'code'=>201], Response::HTTP_CREATED);
    }

    /**
     * Supprimer un bucket
     * 
     * @param Request $request
     * 
     * @return JsonResponse
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     */
    #[Route("/delete", name:"delete_minio", methods: ['DELETE'])]
    public function deleteminio(Request $request): Response
    {
        // Récupérer le nom du bucket depuis le corps de la requête
        $data = json_decode($request->getContent(), true);
        $bucketName = $data['bucketName'] ?? '';

        if (empty($bucketName)) {
            return new JsonResponse(['message'=>"Le nom du bucket est requis", 'code'=>400], Response::HTTP_BAD_REQUEST);
        }

        // Supprimer le bucket via le service
        $result = $this->minioService->deleteBucket($bucketName);

        return new JsonResponse(['message'=>'Bucket supprimé avec succès', 'code'=>200], Response::HTTP_OK);
    }
}
