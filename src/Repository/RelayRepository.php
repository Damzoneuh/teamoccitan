<?php

namespace App\Repository;

use App\Entity\Relay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relay[]    findAll()
 * @method Relay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relay::class);
    }

    // /**
    //  * @return Relay[] Returns an array of Relay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Relay
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
