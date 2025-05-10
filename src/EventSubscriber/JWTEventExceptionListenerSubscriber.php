<?php

namespace App\EventSubscriber;

use App\Entity\AgentHospital;
use App\Entity\User;
use App\Entity\Doctor;
use App\Entity\HospitalAdmin;
use App\Entity\Patient;
use App\Entity\Urgentist;
use App\Services\Toolkit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTEventExceptionListenerSubscriber implements EventSubscriberInterface
{
    private $toolkit;
    private $entityManager;
    private SerializerInterface $serializer;

    public function __construct(ToolKit $toolkit, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->toolkit = $toolkit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }
    /**
     * Méthode appelée lorsque l'authentification est réussie.
     * Elle permet d'enrichir la réponse avec les informations de l'utilisateur authentifié,
     * son administration, et son rôle pour fournir plus de contexte au client après une connexion réussie.
     *
     * *@author Orphée Lié <lieloumloum@gmail.com>
     * 
     * @param AuthenticationSuccessEvent $event L'événement d'authentification réussie.
     */
    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        // Récupérer l'utilisateur qui vient de se connecter et les données associées à l'événement
        $user =  $event->getUser();
        $data = $event->getData();
        /**
         * Retrieves the true user entity from the database using the entity manager.
         *
         * @param User $user The user object from which to get the ID.
         * @return User|null The user entity retrieved from the database, or null if not found.
         */
        $trueUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        // Sérialiser l'entité utilisateur avec le groupe 'user' pour n'inclure que les données pertinentes
        $data_user = $this->serializer->serialize($trueUser, 'json', ['groups' => 'user:read']);
        $data_user = json_decode($data_user, true);
        $data['user'] = $data_user;
        // Si c'est un docteur, ajouter les infos dans 'extend'
        switch (true) {
            case in_array('ROLE_DOCTOR', $trueUser->getRoles()):
                $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $trueUser->getId()]);
                if ($doctor) {
                    $data_doctor = $this->serializer->serialize($doctor, 'json', ['groups' => 'doctor:read']);
                    $data_doctor = json_decode($data_doctor, true);
        
                    unset($data_doctor['user']); // Nettoyage
        
                    $data['extends'] = $data_doctor;
                }
                break;
        
            case in_array('ROLE_PATIENT', $trueUser->getRoles()):
                // Traitement pour le rôle patient
                $patient = $this->entityManager->getRepository(Patient::class)->findOneBy(['user' => $trueUser->getId()]);
                if ($patient) {
                    $data_patient = $this->serializer->serialize($patient, 'json', ['groups' => 'patient:read']);
                    $data_patient = json_decode($data_patient, true);
        
                    unset($data_patient['user']); // Nettoyage
        
                    $data['extends'] = $data_patient;
                }
                break;
        
            case in_array('ROLE_URGENTIST', $trueUser->getRoles()):
                // Traitement pour le rôle urgentist
                $urgentist = $this->entityManager->getRepository(Urgentist::class)->findOneBy(['user' => $trueUser->getId()]);
                if ($urgentist) {
                    $data_urgentist = $this->serializer->serialize($urgentist, 'json', ['groups' => 'urgentist:read']);
                    $data_urgentist = json_decode($data_urgentist, true);
        
                    unset($data_urgentist['user']); // Nettoyage
        
                    $data['extends'] = $data_urgentist;
                }
                break;
            
            case in_array('ROLE_ADMIN_HOSPITAL', $trueUser->getRoles()):
                // Traitement pour le rôle admin_hospital
                $hospital = $this->entityManager->getRepository(HospitalAdmin::class)->findOneBy(['user' => $trueUser->getId()]);
                if ($hospital) {
                    $data_hospital = $this->serializer->serialize($hospital, 'json', ['groups' => 'hospitaladmin:read']);
                    $data_hospital = json_decode($data_hospital, true);
        
                    unset($data_hospital['user']); // Nettoyage
        
                    $data['extends'] = $data_hospital;
                }
                break;

            case in_array('ROLE_AGENT_HOSPITAL', $trueUser->getRoles()):
                // Traitement pour le rôle agent_hospital
                $agent = $this->entityManager->getRepository(AgentHospital::class)->findOneBy(['user' => $trueUser->getId()]);
                if ($agent) {
                    $data_agent = $this->serializer->serialize($agent, 'json', ['groups' => "agenthospital:read"]);
                    $data_agent = json_decode($data_agent, true);
        
                    unset($data_agent['user']); // Nettoyage
        
                    $data['extends'] = $data_agent;
                }
                break;
            default:
                // Traitement par défaut si aucun rôle ne correspond
                break;
        }
        
        
        $data['user']['role'] = $user->getRoles();
        unset($data['user']['roles']);
        $event->setData($data);
        
    }
    /**
     * Méthode statique pour s'abonner aux événements.
     * Elle retourne un tableau associatif où les clés sont les noms des événements
     * et les valeurs sont les méthodes qui doivent être appelées lorsque ces événements se produisent.
     *
     * @return array Un tableau associatif d'événements et de méthodes correspondantes.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onSecurityAuthenticationSuccess',
        ];
    }
}
