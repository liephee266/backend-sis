<?php
namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class GenericEntityManager
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private PropertyAccessorInterface $propertyAccessor;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PropertyAccessorInterface $propertyAccessor,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->propertyAccessor = $propertyAccessor;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Insère une entité basée sur des données dynamiques.
     *
     * @param string $entityClass Nom complet de l'entité (e.g., App\Entity\User)
     * @param array $data Données à mapper sur l'entité
     * @return array Liste des erreurs ou un tableau vide si succès
     * 
     * @author  Orphée Lié et Michel MIYALOU <lieloumloum@gmail.com>
     */
    public function persistEntity(string $entityClass, array $data, bool $update = false): array
    {
        // Normalise les clés du JSON en camelCase
        // Crée une nouvelle instance de l'entité
        $entity = "";
        if ($update==false) {
            $entity = new $entityClass();
        } else {
            $entity = $this->entityManager->getRepository($entityClass)->find($data['id']);
        }
        unset($data['id']);
        
        // Récupère les métadonnées de l'entité
        $metadata = $this->entityManager->getClassMetadata($entityClass);
        
        foreach ($data as $field => $value) {
            
            // Vérifie si le champ est mappé dans l'entité
            if ($metadata->hasField($field)) {
                if ($field == "password" and $entity instanceof User) {
                    $hashedPassword = $this->passwordHasher->hashPassword($entity, $data['password']);
                    $value = $hashedPassword;
                }
                // Affecte la valeur du champ
                $this->propertyAccessor->setValue($entity, $field, $value);
            }
            // Gestion des associations (relations Doctrine)
            elseif ($metadata->hasAssociation($field)) {
                $associationMetadata = $metadata->getAssociationMapping($field);
                // dump($associationMetadata['type']);
                // Si l'association est une relation "to-one"
                if ($associationMetadata['type'] & ClassMetadata::TO_ONE) {
                    $relatedEntity = $this->entityManager
                        ->getRepository($associationMetadata['targetEntity'])
                        ->find($value['id'] ?? $value);
                    if ($relatedEntity) {
                        $this->propertyAccessor->setValue($entity, $field, $relatedEntity);
                    }
                }
                // Si l'association est une relation "many-to-many"
                elseif ($associationMetadata['type'] & ClassMetadata::MANY_TO_MANY) {
                    // On s'assure que la valeur est un tableau
                    $ids = is_array($value) ? $value : [$value];
                    
                    // Récupère la collection existante (pour les updates)
                    $collection = $this->propertyAccessor->getValue($entity, $field);
                    $collection = new ArrayCollection();
                    // Ajoute chaque entité associée
                    foreach ($ids as $id) {
                        $relatedEntity = $this->entityManager
                            ->getRepository($associationMetadata['targetEntity'])
                            ->find(is_array($id) ? $id['id'] : $id);
                        if ($relatedEntity) {
                            $collection->add($relatedEntity);
                        }
                    }
                    $this->propertyAccessor->setValue($entity, $field, $collection);
                }
                // Si l'association est une relation "one-to-many"
                elseif ($associationMetadata['type'] & ClassMetadata::ONE_TO_MANY) {
                    // On s'assure que la valeur est un tableau
                    $ids = is_array($value) ? $value : [$value];
                    // Récupère la collection existante (pour les updates)
                    $collection = $this->propertyAccessor->getValue($entity, $field);
                    // Ajoute chaque entité associée
                    foreach ($ids as $id) {
                        $relatedEntity = $this->entityManager
                            ->getRepository($associationMetadata['targetEntity'])
                            ->find(is_array($id) ? $id['id'] : $id);
                        if ($relatedEntity) {
                            $collection->add($relatedEntity);
                        }
                    }
                    $this->propertyAccessor->setValue($entity, $field, $collection);
                }
            }
        }
        
        // Valide l'entité
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $errorMessages; // Retourne les erreurs
        }
        
        // Persiste l'entité
        // dd($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        
        return ["entity" => $entity]; // Aucune erreur
    }
    
    /**
     * Normalise les clés du JSON en camelCase.
     *
     * @param array $data //Données à normaliser
     * @return array //Données normalisées
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    function normalizeKeysToCamelCase(array $data): array
    {
        $normalized = [];
        foreach ($data as $key => $value) {
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
            $normalized[$camelCaseKey] = is_array($value) ? $this->normalizeKeysToCamelCase($value) : $value;
        }
        return $normalized;
    }

    /**
     * Insère ou modifie une entité basée sur la création du User.
     *Pratique pour assigner ou modifier un utilisateur a une autre entité au même moment quand crée ou modifie cette entité     
     * 
     * @param string $entityClass Nom complet de l'entité
     * @param array $user_data tableaux de données de l'utilisateur
     * @param array $data données à mapper sur l'entité
     * @return array Liste des erreurs ou un tableau 
     * 
     * @author Michel MIYALOU <michelmiyalou@gmail.com>
     */
    public function persistEntityUser(string $entityClass ,array $user_data, $data)
    {

        // Validation des données requises
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Données manquantes"], 
                Response::HTTP_BAD_REQUEST
            );
        }

        // Début de la transaction
        $this->entityManager->beginTransaction();

        $errors_user = $this->persistEntity("App\Entity\User", $user_data); 
            
        if (!empty($errors_user['errors'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Erreur lors de la création de l'utilisateur: "], Response::HTTP_BAD_REQUEST);
        }
        
        // Préparation des données de l'entité
        $entite_data = $data;
        $entite_data['user'] = $errors_user['entity']->getId();
        
        // Création de l'entite
        $errors_entite = $this->persistEntity($entityClass, $entite_data);
        
        if (!empty($errors_entite['errors'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Erreur lors de la création"], Response::HTTP_BAD_REQUEST);
        }

        // Validation de la transaction
        $this->entityManager->commit();
        return ["entity" => $errors_entite['entity']];
    }

    /**
     * Insère uniquement un user a partir de la methode persistEntity.
     *Pratique pour inserer un user depuis le controlleur d'une autre entité
     * 
     * @param array $user_data tableaux de données de l'utilisateur
     * @param array $data données à mapper sur l'entité
     * @return array Liste des erreurs ou un tableau 
     * 
     * @author Michel MIYALOU <michelmiyalou@gmail.com>
     */
    public function persistUser(array $user_data, $data)
    {
        // Validation des données requises
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Données manquantes"], 
                Response::HTTP_BAD_REQUEST
            );
        }
         // Début de la transaction
        $this->entityManager->beginTransaction();

        $errors_user = $this->persistEntity("App\Entity\User", $user_data); 
            
        if (!empty($errors_user['errors'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Erreur lors de la création de l'utilisateur: "], Response::HTTP_BAD_REQUEST);
        }

        if (!empty($errors_user['errors'])) {
            return new JsonResponse(
                ['code' => 400, 'message' => "Erreur lors de la création"], Response::HTTP_BAD_REQUEST);
        }

        // Validation de la transaction
        $this->entityManager->commit();
        return ["entity" => $errors_user['entity']];
    }
}