<?php

namespace App\Controller;

use App\Services\MinioService;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/api/v1/minio')]
class MinioController extends AbstractController
{
    private MinioService $minioService;
    private S3Client $s3Client;

    public function __construct(MinioService $minioService, S3Client $s3Client)
    {
        $this->minioService = $minioService;
        $this->s3Client = $s3Client;
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
        
        // Vérification de l'existence du bucket
        if (!$this->minioService->bucketExists($bucketName)) {
            
            // Vérifier si le nom du bucket est vide
            if (empty($bucketName)) {
                return new JsonResponse(['message'=>'Le nom du bucket est requis','errors'=>'Pas de (/,\) dans le nom du bucket', 'code'=>400], Response::HTTP_BAD_REQUEST);
            }
    
            // Vérifier si le nom du bucket contient des espaces ou des caractere speciaux
            if (!preg_match('/^[a-z0-9-]{3,63}$/', $bucketName)) {
                return new JsonResponse(
                    [
                        'message' => "Format invalide. Respectez ces règles :",
                        'errors' => [
                            'Pas d\'espaces',
                            'Pas de majuscules',
                            'Uniquement : lettres minuscules (a-z), chiffres (0-9) et hypens (-)',
                            'Longueur entre 3 et 63 caractères'
                        ],
                        'code' => 400
                    ],
                    Response::HTTP_BAD_REQUEST
                );
            }
    
            // Créer le bucket via le service
            $result = $this->minioService->insertbucket($bucketName);
    
            return $this->json(['message'=>'Bucket crée avec succès', 'code'=>201],
            Response::HTTP_CREATED);    
        }

        return new JsonResponse(['message'=>"Le bucket: $bucketName existe déja, Veuillez choisir un autre nom", 'code'=>400], Response::HTTP_BAD_REQUEST);
        
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
            return new JsonResponse(['message'=>"Le nom du bucket est requis", 'code'=>400],
            Response::HTTP_BAD_REQUEST);
        }

        // Vérification de l'existence du bucket
        if (!$this->minioService->bucketExists($bucketName)) {
            return new JsonResponse(
                ['message' => "Le bucket: $bucketName n'existe pas, Veuillez revoir son nom",
                'code' => 404],
                Response::HTTP_NOT_FOUND
            );
        }

        // Supprimer le bucket via le service
        $result = $this->minioService->deleteBucket($bucketName);

        return new JsonResponse(['message'=>'Bucket supprimé avec succès', 'code'=>200],
        Response::HTTP_OK);
    }
}
