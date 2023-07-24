<?php

namespace App\Repository;

use App\Entity\Opinion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Professor;
use App\Entity\Subject;
use App\Entity\User;

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

    public function existsByUserAndProfessor(User $user, Professor $professor)
    {
        $result = $this->createQueryBuilder('o')
                ->where('o.owner = :owner')
                ->andWhere('o.professor = :professor')
                ->setParameter('owner', $user)
                ->setParameter('professor', $professor)
                ->getQuery()
                ->getResult();

        return count($result) > 0;
    }

    public function existsByUserAndSubject(User $user, Subject $subject)
    {
        $result = $this->createQueryBuilder('o')
                ->where('o.owner = :owner')
                ->andWhere('o.subject = :subject')
                ->setParameter('owner', $user)
                ->setParameter('subject', $subject)
                ->getQuery()
                ->getResult();

        return count($result) > 0;
    }

}
