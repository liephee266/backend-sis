<?php
// src/Service/MinioService.php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class MinioService
{
    private $s3Client;
    public function __construct()
    {
        // Créez explicitement le client S3 avec les credentials MinIO
        $this->s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => 'minioadmin',  // Remplacez par votre clé d'accès MinIO
                'secret' => 'minioadmin',  // Remplacez par votre mot de passe MinIO
            ],
            'endpoint' => 'http://localhost:9000',  // Remplacez par l'URL de votre instance MinIO
            'use_path_style_endpoint' => true,     // Important pour MinIO
        ]);
    }

    /**
     * Vérifier la connexion à MinIO
     *
     * @return array|mixed
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     */
    public function checkConnection()
    {
        try {
            // Liste les buckets pour tester la connexion
            $result = $this->s3Client->listBuckets();
            return $result['Buckets'];
        } catch (AwsException $e) {
            // Si une erreur se produit, afficher le message d'erreur
            return 'Erreur de connexion à MinIO: ' . $e->getMessage();
        }
    }

    /**
     * Créer un bucket
     * 
     * @param string $bucketname
     * 
     * @return string
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     */
    public function insertbucket(string $bucketname)
    {
        try {
            // Créez un nouveau bucket
            $result = $this->s3Client->createBucket([
                'Bucket' => $bucketname,
            ]);
            return $result;
        } catch (AwsException $e) {
            // Si une erreur se produit, afficher le message d'erreur
            return 'Erreur lors de la création du bucket: ' . $e->getMessage();
        }
    }

    /**
     * Supprimer un bucket
     * 
     * @param string $bucketname
     * 
     * @return string
     * 
     * @author Michel Miyalou <michelmiyalou0@@gmail.com>
     */
    public function deleteBucket(string $bucketname)
    {
        try {
            // Supprimez un bucket existant
            $result = $this->s3Client->deleteBucket([
                'Bucket' => $bucketname,
            ]);
            return $result;
        } catch (AwsException $e) {
            // Si une erreur se produit, afficher le message d'erreur
            return 'Erreur lors de la suppression du bucket: ' . $e->getMessage();
        }
    }
}