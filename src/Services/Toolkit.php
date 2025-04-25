<?php
namespace App\Services;

use App\Entity\DossierMedicale;
use App\Entity\Patient;
use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use App\Entity\User;
use DateTimeImmutable;
use Pagerfanta\Pagerfanta;
use App\Entity\Disponibilite;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

/**
 * app\Services\Toolkit
*
*  @service Class Toolkit
*  Cette classe contient des fonctions utiles pour travailler avec les données utilisateur et les entités.
*  Elle est utilisée par d'autres classes du projet comme son nom l'indique il s'agit d'une boite a outils, 
*  pour ne pas surcharger les code de l'application et les controllers
*  @author Orphée Lié <lieloumloum@gmail.com>
*/

class Toolkit 
{  
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private JWTEncoderInterface $jwtManager;
    
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, JWTEncoderInterface $jwtManager)
    {
        $this->entityManager = $entityManager;  
        $this->serializer = $serializer;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @param array $dataSelect
     * @return array
     * 
     * Renvoie un tableau de noms d'entité avec la première lettre en majuscule
     * conçu pour intervenir au sein de la fonction qui se charge de retourner les select
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    public function formatArrayEntity(array $dataSelect): array
    {
        return array_map(function ($value) {
            // Mettre la première lettre en majuscule
            $value = ucfirst($value);
            // Retirer le 's' final s'il y en a
            if (str_ends_with($value, 's')) {
                $value = substr($value, 0, -1);
            }
            return $value;
        }, $dataSelect);
    }

    /**
     * @param array $dataSelect
     * @return array
     * 
     * Renvoie un tableau pour peupler les select de l'application avec les ID et les labels ou descriptions de chaque entité
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    public function formatArrayEntityLabel(array $dataSelect, array $filtres=[], string $portail = null): array
{
    $allData = [];
    $entities = [];
    foreach ($dataSelect as $key => $value) {
        if ( !empty($filtres)) {
            // Pour les autres entités, on applique simplement le filtre
            $entities = $this->entityManager->getRepository('App\Entity\\'.$value)->findBy($filtres);
        } else {
            // Si aucune condition de filtre spécifique, on prend toutes les entités
            $entities = $this->entityManager->getRepository('App\Entity\\'.$value)->findAll();
        }
        // Sérialisation des données
        $data = json_decode($this->serializer->serialize($entities, 'json', ['groups' => 'data_select']), true);
        
        if ($value == 'TypeHopital') {
            # code...
            $value = 'typeHopital';
            $allData[$value] = $data;
        }else {
            # code...
            $allData[strtolower($value)] = $data;
        }
        // $allData[$value] = $data;
    }
    // Retourner les données transformées
    return $this->transformArray($allData);
}

    /**
     * Transforme un tableau d'entrées en un format où l'ID devient la clé et la première autre valeur est également ajoutée.
     * 
     * Cette méthode prend un tableau d'entrée de la forme :
     * [
     *   "administration" => [
     *     [
     *       "id" => 1,
     *       "nom" => "Administration Centrale",
     *       // D'autres clés possibles...
     *     ]
     *   ]
     * ]
     * et renvoie un tableau transformé sous la forme :
     * [
     *   "administration" => [
     *     [
     *       "id" => "1",
     *       "value" => "Administration Centrale"
     *     ]
     *   ]
     * ]
     * Si la clé `nom` n'existe pas, elle prend la première autre clé trouvée pour la valeur associée.
     * 
     * @param array $input Le tableau d'entrée à transformer.
     * @return array Le tableau transformé.
     * 
     * *@author Orphée Lié <lieloumloum@gmail.com>
     * 
     */
    public function transformArray(array $input): array
    {
        $result = [];
        foreach ($input as $key => $items) {
            if (is_array($items) && isset($items[0]['id'])) {
                foreach ($items as $item) {
                    // dd($item);
                    if (isset($item['id'])) {
                        // Recherche la première clé différente de 'id' et extrait sa valeur
                        $otherKey = array_key_first(array_diff_key($item, ['id' => '']));
                        $value = $otherKey !== null ? $item[$otherKey] : null;
                        // Ajoute le résultat transformé
                        // if ($key == 'prestation' ) {
                        //     $result[$key][] = [
                        //         'value' => (string)$item['id'],
                        //         'label' => $item['nom'],
                        //         // 'description' => $item['description']
                        //     ];
                        // }else {
                            $result[$key][] = [
                                'value' => (string)$item['id'],
                                'label' => $value
                            ];
                        // }
                    }
                }
            }else{
                $result[$key] = [];
            }
        }
        return $result;
    }

    /**
     * Retourne le role de l'utilisateur connecté
     * 
     * @param Request $request
     * @return string
     * 
     * *@author Orphée Lié <lieloumloum@gmail.com>
     * 
     */

    public function getRoleUser(Request $request ): array
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = substr($authorizationHeader, 7); 
        $payload = $this->jwtManager->decode($token);
        $user =  $this->entityManager->getRepository(User::class)->findOneBy([
            "email" => $payload["username"]
        ]);
        return $user->getRoles();
    }

    /**
     * Gère la pagination d'une collection d'entités et renvoie les résultats paginés avec des métadonnées de pagination.
     * Cette méthode prend en compte les paramètres `page` et `limit` dans la requête pour configurer la pagination.
     * 
     * @param Request $request La requête HTTP contenant les paramètres de pagination (`page`, `limit`).
     * @param string $class_name Le nom de la classe de l'entité à paginer.
     * @param string $groupe_attribute Le groupe de sérialisation pour filtrer les attributs lors de la sérialisation des résultats.
     * @param array|null $filtre Les filtres de recherche pour la pagination.
     * 
     * *@author  Orphée Lié <lieloumloum@gmail.com>
     * 
     * @return array Les données paginées et les informations de pagination.
     */
    public function getPagitionOption(Request $request, string $class_name, string $groupe_attribute, array $filtre = []) : array
    {
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups($groupe_attribute)
            ->toArray();
        // Initialiser les paramètres de pagination par défaut
        $query = [];
        // Vérifie si les paramètres `page` et `limit` sont présents dans la requête, sinon valeurs par défaut
        if ($request->query->has('page') && $request->query->has('limit')) {
            $query['page'] = $request->query->get('page');
            $query['limit'] = $request->query->get('limit');
        } 
        // Définit le numéro de page et la limite d'éléments par page à partir de la requête
        $page = $request->query->getInt('page', $query['page'] ?? 1);
        $maxPerPage = $request->query->getInt('maxPerPage', $query['limit'] ?? 10);
        // Création du QueryBuilder pour la classe d'entité spécifiée
        $queryBuilder = $this->entityManager->getRepository('App\Entity\\'.$class_name)->createQueryBuilder('u');
        // Appliquer les filtres si ils existent
        if ($filtre) {
            foreach ($filtre as $key => $value) {
                if (is_array($value)) {
                    // Si la valeur est un tableau, on vérifie si le champ est aussi un tableau JSON
                    // Supposons ici que 'roles', 'tags', etc. sont des champs JSON en BDD
                    if (in_array($key, ['roles', 'tags', 'permissions'])) {
                        // On utilise JSON_CONTAINS (MySQL uniquement)
                        $queryBuilder->andWhere("JSON_CONTAINS(u.$key, :$key) = 1");
                        // $queryBuilder->andWhere("u.$key @> :$key"); @Pour PostgreSQL
                        // Doctrine attend une chaîne JSON ici
                        $queryBuilder->setParameter($key, json_encode($value));
                    } else {
                        // Cas classique avec IN
                        $queryBuilder->andWhere($queryBuilder->expr()->in("u.$key", ":$key"));
                        $queryBuilder->setParameter($key, $value);
                    }
                } elseif ($key === 'created_at' || $key === 'updated_at') {
                    $queryBuilder->andWhere("u.$key >= :$key");
                    $queryBuilder->setParameter($key, $value);
                } else {
                    $queryBuilder->andWhere("u.$key = :$key");
                    $queryBuilder->setParameter($key, $value);
                }
            }
        }        
        $queryBuilder->orderBy('u.id', 'DESC');
        // Configuration de l'adaptateur pour Pagerfanta pour gérer la pagination
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($maxPerPage);
        $pagerfanta->setCurrentPage($page);

        // Obtenir les résultats de la page actuelle
        $items = $pagerfanta->getCurrentPageResults();
        
        // Sérialiser les résultats paginés avec le groupe de sérialisation spécifié
        $data = $this->serializer->serialize($items, 'json', $context);

        // Vérifie si le nom de la classe se termine par "s", sinon ajoute "s" pour un pluriel de convention
        if (!str_ends_with($class_name, 's')) {
            $class_name = $class_name . 's';
        }
        return $this->p($pagerfanta, json_decode($data), $page, $maxPerPage, $class_name);
    }
    /**
     * Récupère l'utilisateur authentifié depuis la requête HTTP.
     * Et renvoie l'objet User correspondant.
     * @author Orphée Lié <lieloumloum@gmail.com>
     * 
     * @param Request  $request
     * 
     */

    function getUser(Request $request)  {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = substr($authorizationHeader, 7); 
        $payload = $this->jwtManager->decode($token);
        $id =  $this->entityManager->getRepository(User::class)->findOneBy([
            "email" => $payload["username"]
        ]);
        return $id;
    
    }

    /**
     * Convertit une chaîne de caractères séparée par des virgules en tableau.
     * Si la chaîne ne contient pas de virgule, retourne un tableau avec un seul élément.
     *
     * @param string $input Chaîne de caractères à convertir en tableau.
     * @return array Tableau des éléments séparés par des virgules ou un tableau contenant un seul élément.
     * 
     * @author Orphée Lié  
     */
    function stringToArray(string $input): array
    {
        // Utilise la fonction explode pour séparer les éléments par virgule
        // Si aucune virgule n'est présente, explode retourne un tableau avec un seul élément
        return explode(',', $input);
    }

    /**
     * Interprète les permissions d'un module en fonction d'un JSON.
     *
     * @param string $json Le JSON contenant les permissions.
     * @param string $module Le nom du module à rechercher.
     * @param string|null $action (Optionnel) L'action spécifique à vérifier (read, write, delete).
     * @return array|bool Retourne un tableau des actions autorisées si aucune action n'est spécifiée.
     *                    Retourne true/false si une action spécifique est fournie.
     *                    Retourne null si le module n'existe pas.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    public function interpretPermissions(string $json, string $module, ?string $action = null)
    {
        // Décoder le JSON en tableau associatif
        $permissions = json_decode($json, true);

        // Vérifie si le module existe dans les permissions
        if (!array_key_exists($module, $permissions)) {
            return null; // Le module n'existe pas
        }

        // Si une action spécifique est fournie
        if ($action !== null) {
            return isset($permissions[$module][$action]) && $permissions[$module][$action] === true;
        }

        // Retourne toutes les actions autorisées pour le module
        $allowedActions = [];
        foreach ($permissions[$module] as $key => $isAllowed) {
            if ($isAllowed === true) {
                $allowedActions[] = $key;
            }
        }

        return $allowedActions;
    }

    /**
     * Traitement des filtres
     * 
     * @param array $filtre
     * @return array
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     * 
     */
    public function traitementFiltre($filtre): array
    {
        foreach ($filtre as $key => $value) {
            if ($key != 'date_debut' && $key != 'date_fin' && $value != null && $value != '') {
                $filtre[$key] = explode(',', $value);
            }
            if( $value == null or $value == '') {
                unset($filtre[$key]);
            }
        }
        return $filtre;
    }

    /**
     * Génère une réponse paginée structurée pour une API.
     *
     * @param Pagerfanta $pagerfanta L'objet Pagerfanta gérant la pagination.
     * @param array $r Les données à inclure dans la réponse.
     * @param int $page La page actuelle.
     * @param int $maxPerPage Le nombre maximum d'éléments par page.
     * @param string $class_name Le nom de la classe ou du module pour générer les URL.
     * @return array La réponse structurée avec les données et les métadonnées de pagination.
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    public function p($pagerfanta, $r, $page, $maxPerPage, $class_name): array
    {
        // Structure de réponse paginée
        $response = [
            // Les données des résultats
            'data' => $r, // Les résultats formatés, typiquement un tableau ou une collection d'éléments.
            // Métadonnées liées à la pagination
            'pagination' => [
                'current_page' => $page,                      // Numéro de la page actuelle
                'max_per_page' => $maxPerPage,                // Nombre maximum d'éléments par page
                'total_items' => $pagerfanta->getNbResults(), // Nombre total d'éléments dans la collection
                'total_pages' => $pagerfanta->getNbPages(),   // Nombre total de pages nécessaires pour tout afficher
                // URL de la page suivante, si elle existe
                'next_page' => $pagerfanta->hasNextPage() 
                    ? "/" . strtolower($class_name) . "/?page=" . ($page + 1) . "&limit=$maxPerPage" 
                    : null,
                // URL de la page précédente, si elle existe
                'previous_page' => $pagerfanta->hasPreviousPage() 
                    ? "/" . strtolower($class_name) . "/?page=" . ($page - 1) . "&limit=$maxPerPage" 
                    : null,
                // URL de la première page
                'first_page' => "/" . strtolower($class_name) . "/?page=1&limit=$maxPerPage",
                // URL de la dernière page
                'last_page' => "/" . strtolower($class_name) . "/?page=" . $pagerfanta->getNbPages() . "&limit=$maxPerPage",
            ],
            // Code de succès HTTP
            "code" => 200
        ];
        return $response; // Retourne la structure pour une utilisation dans une réponse JSON.
    }

    /**
     * Récupère un objet par son UUID
     *
     * @param string $entityName Le nom de la classe de l'objet.
     * @param string $id L'identifiant de l'objet.
     * @return object L'objet correspondant au UUID fourni.
     * 
     * @author Michel Miyalou <michelmiyalou0@gmail.com>
     */
    public function getbyuuid(string $entityName, $id)
    {
        // Récupère l'objet par son UUID
        $ressource = $this->entityManager->getRepository($entityName)->findOneBy(['uuid' => $id]);
        // Si l'objet n'existe pas, on essaie de le récupérer par son ID
        if (!$ressource && is_numeric($id)) {
            $ressource = $this->entityManager->getRepository($entityName)->find($id);
        }else{
            $ressource = null;
        }
        //si l'objet n'existe pas, on retourne null
        return $ressource;
    }

    /**
     * Calcule le nombre de jours entre une date donnée et la date actuelle.
     *
     * @param string $dateStr   Date au format 'Y-m-d H:i:s', ex. '2025-04-11 15:05:48'
     * @return int              Nombre de jours (positif ou négatif)
     * @throws Exception        Si le format de date est invalide
     */
    function joursDepuisDate(string $dateStr): int
    {
        // Création de l'objet DateTime pour la date fournie
        $dateFournie = DateTime::createFromFormat('Y-m-d H:i:s', $dateStr);
        if (!$dateFournie) {
            throw new Exception("Format de date invalide : attendu 'Y-m-d H:i:s'.");
        }

        // Création de l'objet DateTime pour la date actuelle
        $dateActuelle = new DateTime('now');

        // Calcul de la différence
        $interval = $dateFournie->diff($dateActuelle);

        // Retourne le nombre total de jours (peut être négatif si dateFournie > dateActuelle)
        return $interval->invert ? -$interval->days : $interval->days;
    }



    public function ExistRepository(array $data,string $entity_name,int $id)
    {
        // Récupération du repository
        $repository_entity = $this->entityManager->getRepository('App\Entity\\'.$data[$entity_name])->find($id);

        return $repository_entity;
    }
    /** 
     * Détermine le groupe de sérialisation à utiliser pour afficher les informations du patient,
     * en fonction des droits d'accès de l'utilisateur connecté.
     *
     * Si l'utilisateur a un accès explicite au dossier médical (via le champ "access"),
     * le groupe complet 'patient:read' est retourné.
     * Sinon, un groupe restreint 'patient:read:restricted' est appliqué.
     *
     * @param User $user L'utilisateur connecté
     * @param Patient $patient Le patient concerné
     * @param DossierMedicale $dossierMedicale Le dossier médical du patient
     *
     * @return string Le groupe de sérialisation à utiliser ('patient:read' ou 'patient:read:restricted')
     * @author Daryon Rocknes <daryonrocknes@icloud.com.com>
     */
    public function getPatientSerializationGroup(User $user, DossierMedicale $dossierMedicale): string
    {
        // Liste des utilisateurs ayant un accès au dossier médical
        $accessList = $dossierMedicale->getAccess();

        // ID de l'utilisateur connecté
        $userId = $user->getId();

        // Vérifie si l'utilisateur est présent dans la liste d'accès
        foreach ($accessList as $accessUser) {
            // Cas où la liste contient des objets User
            if ($accessUser instanceof User && $accessUser->getId() === $userId) {
                return 'patient:read';
            }

            // Cas où la liste contient des IDs simples (entiers)
            if (is_int($accessUser) && $accessUser === $userId) {
                return 'patient:read';
            }
        }

        // Si l'utilisateur n'a pas d'accès, on retourne le groupe restreint
        return 'patient:read:restricted';
    }

    

    /**
     * @param array $months
     * @param array $disponibilities
     * @return object|null
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    public function getAgenda(array $months, array $filtre)
    {
        $a = null;
        $n = [];
        foreach ($months['months'] as $key => $month) {
            $a = $this->getDaysOfMonthAssoc($months['year'],  $month);
            foreach ($a as $key_a => $value) {
                $disponibilities = $this->entityManager->getRepository(Disponibilite::class)->findBy([
                    'date_j' => new DateTime($key_a),
                    'doctor' => $filtre['id_doctor'],
                    'hospital' => $filtre['id_hospital']
                ]);
                foreach ($disponibilities as $key_d => $disponibilitie) {
                    $a[$key_a][] = [
                        'id' => $disponibilitie->getId(),
                        'date_j' => $disponibilitie->getDateJ()->format('Y-m-d'),
                        'heure_debut' => $disponibilitie->getHeureDebut(),
                        'heure_fin' => $disponibilitie->getHeureFin(),
                    ];
                }
            }
            $n[$month] = $a;
        }
        return $n;
    }

    /**
     * Retourne un tableau associatif des jours du mois,
     * sous la forme ['YYYY-MM-DD' => []].
     *
     * @param int $year  Année (ex. 2025)
     * @param int $month Mois (1–12)
     * @return array<string, array>
     * 
     * @author Orphée Lié <lieloumloum@gmail.com>
     */
    function getDaysOfMonthAssoc(int $year, int $month): array
    {
        // 1) Premier jour du mois en DateTimeImmutable
        $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        // 2) Premier jour du mois suivant
        $end   = $start->modify('first day of next month');
        // 3) Intervalle d'un jour
        $interval = new DateInterval('P1D');
        // 4) Création du DatePeriod
        $period = new DatePeriod($start, $interval, $end);
        // 5) Construction du tableau associatif
        $daysAssoc = [];
        foreach ($period as $date) {
            // clé : date formatée 'YYYY-MM-DD'
            $key = $date->format('Y-m-d');
            // valeur : tableau vide
            $daysAssoc[$key] = [];
        }
        return $daysAssoc;
    }
}

