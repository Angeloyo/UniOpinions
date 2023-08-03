<?php

namespace App\Repository;

use App\Entity\RelationSubjectProfessor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelationSubjectProfessor>
 *
 * @method RelationSubjectProfessor|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelationSubjectProfessor|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelationSubjectProfessor[]    findAll()
 * @method RelationSubjectProfessor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationSubjectProfessorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelationSubjectProfessor::class);
    }

//    /**
//     * @return RelationSubjectProfessor[] Returns an array of RelationSubjectProfessor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RelationSubjectProfessor
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
