<?php

namespace App\Repository;

use App\Entity\VRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method VRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method VRequest[]    findAll()
 * @method VRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VRequest::class);
    }

    // /**
    //  * @return VRequest[] Returns an array of VRequest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VRequest
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
