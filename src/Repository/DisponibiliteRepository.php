<?php

namespace App\Repository;

use App\Entity\Disponibilite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * @extends ServiceEntityRepository<Disponibilite>
 */
class DisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disponibilite::class);
    }

    /**
     * Récupère les disponibilités groupées par date pour un médecin et un hôpital donnés.
     *
     * @param int $doctorId
     * @param int $hospitalId
     * @return Disponibilite[]
     */
    public function findByDoctorAndHospitalGroupedByDate(int $doctorId, int $hospitalId): array
    {
        return $this->createQueryBuilder('d')
            ->select('d.date_j, COUNT(d.id) AS total')
            ->where('d.doctor = :doctorId')
            ->andWhere('d.hospital = :hospitalId')
            ->setParameters(new ArrayCollection([
                new Parameter('doctorId', $doctorId),
                new Parameter('hospitalId', $hospitalId),
            ]))
            ->groupBy('d.date_j')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Disponibilite[] Returns an array of Disponibilite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Disponibilite
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
