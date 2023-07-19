<?php

namespace App\Repository;

use App\Entity\Opinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Professor;
use App\Entity\Subject;

/**
 * @extends ServiceEntityRepository<Opinion>
 *
 * @method Opinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opinion[]    findAll()
 * @method Opinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpinionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opinion::class);
    }

    public function findAcceptedByProfessor(Professor $professor)
    {
        return $this->createQueryBuilder('o')
            ->where('o.professor = :professor')
            ->andWhere('o.accepted = true')
            ->andWhere('o.comment IS NOT NULL')
            ->setParameter('professor', $professor)
            ->getQuery()
            ->getResult();
    }
    
    public function findAcceptedBySubject(Subject $subject)
    {
        return $this->createQueryBuilder('o')
            ->where('o.subject = :subject')
            ->andWhere('o.accepted = true')
            ->andWhere('o.comment IS NOT NULL')
            ->setParameter('subject', $subject)
            ->getQuery()
            ->getResult();
    }
}
