<?php
namespace App\Services;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
    }

    public function createNotification(string $title, string $content, bool $flush = true): Notification {
        $notification = new Notification();
        $notification
            ->setTitle($title)
            ->setContent($content);

        $this->em->persist($notification);
        
        if ($flush) {
            $this->em->flush();
        }

        return $notification;
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->em->flush();
    }
}