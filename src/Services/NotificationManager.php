<?php
namespace App\Services;

use App\Entity\User;
use App\Entity\Notification;
use Symfony\Component\Mercure\Update;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Serializer\SerializerInterface;

class NotificationManager
{
    private EntityManagerInterface $em;
    private HubInterface $hub;
    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $em,
        HubInterface $hub,
        SerializerInterface $serializer
    ) {
        $this->em = $em;
        $this->hub = $hub;
        $this->serializer = $serializer;
    }

    /**
     * Crée et publie une notification pour un utilisateur.
     */
    public function createNotification(string $title, string $content, ?User $receiver = null, bool $flush = true): Notification
    {
        $notification = new Notification();
        $notification
            ->setTitle($title)
            ->setContent($content)
            ->setReceiver($receiver)
            ->setIsRead(false);


        $this->em->persist($notification);

        if ($flush) {
            $this->em->flush();
        }

        $this->publishNotification($notification);

        return $notification;
    }

    public function publishNotification(Notification $notification, ?string $customTopic = null, bool $isGlobal = false): void
    {
        $data = $this->serializer->serialize($notification, 'json', ['groups' => ['notification:read']]);

        // Choix du topic
        $topic = match (true) {
            $customTopic !== null => $customTopic,
            $isGlobal => '/notifications/global',
            default => '/notifications/user/' . $notification->getReceiver()->getId(),
        };

        $update = new Update($topic, $data);

        $this->hub->publish($update);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->em->flush();
    }
}