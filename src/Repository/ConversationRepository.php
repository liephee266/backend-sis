<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversations>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

   public function findOneBetweenUsers(User $user1, User $user2): ?Conversation
    {
        $conversations = $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();

        foreach ($conversations as $conversation) {
            $participants = $conversation->getParticipants() ?? [];
            if (in_array($user1->getId(), $participants) && in_array($user2->getId(), $participants)) {
                return $conversation;
            }
        }

        return null;
    }

//    /**
//     * @return Conversations[] Returns an array of Conversations objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Conversations
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
