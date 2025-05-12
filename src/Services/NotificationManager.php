<?php
namespace App\Services;

use Pusher\Pusher;
use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class NotificationManager
{
    public function __construct(
    #[Autowire('%pusher.app_id%')] private string $appId,
    #[Autowire('%pusher.app_key%')] private string $appKey,
    #[Autowire('%pusher.app_secret%')] private string $appSecret,
    #[Autowire('%pusher.app_cluster%')] private string $cluster,
    private EntityManagerInterface $em,
    private SerializerInterface $serializer,
    private Pusher $pusher
) {
    date_default_timezone_set('UTC');
    $this->pusher = new Pusher(
        $this->appKey,
        $this->appSecret,
        $this->appId,
        [
            'cluster' => $this->cluster,
            'useTLS' => true,
        ]
    );
}

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

    public function publishNotification(Notification $notification, ?string $customChannel = null, bool $isGlobal = false): void
    {
        $data = $this->serializer->serialize($notification, 'json', ['groups' => ['notification:read']]);

        // Choix du canal Pusher
        $channel = match (true) {
            $customChannel !== null => $customChannel,
            $isGlobal => 'notifications-global',
            default => 'private-user_' . $notification->getReceiver()->getId(),
        };

        $event = 'new_notification';

        $this->pusher->trigger($channel, $event, json_decode($data, true));
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->em->flush();
    }
}

