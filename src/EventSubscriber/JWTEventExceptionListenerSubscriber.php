<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Doctor;
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
        if (in_array('ROLE_DOCTOR', $trueUser->getRoles())) {
            // Si l'utilisateur est un médecin, récupérer les informations de l'administration associée
            $doctor = $this->entityManager->getRepository(Doctor::class)->findOneBy(['user' => $trueUser->getId()]);
            $data_doctor = $this->serializer->serialize($doctor, 'json', ['groups' => 'doctor:read']);
            $data_doctor = json_decode($data_doctor, true);
            unset($data_doctor['user']);
            unset($data_doctor['id']);
            $data['user'] = array_merge($data['user'], $data_doctor);
            # code...
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
