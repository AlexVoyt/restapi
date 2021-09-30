<?php

namespace App\Repository;

use App\Entity\TodoThingy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TodoThingy|null find($id, $lockMode = null, $lockVersion = null)
 * @method TodoThingy|null findOneBy(array $criteria, array $orderBy = null)
 * @method TodoThingy[]    findAll()
 * @method TodoThingy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoThingyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoThingy::class);
    }

    // /**
    //  * @return TodoThingy[] Returns an array of TodoThingy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TodoThingy
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
