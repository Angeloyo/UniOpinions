<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use App\Entity\Degree;

/**
 * @extends ServiceEntityRepository<Subject>
 *
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function findOneBySlugAndDegree(string $slug, Degree $degree): ?Subject
    {
        $subject = $this->findOneBy(['slug' => $slug, 'degree' => $degree]);

        if (!$subject) {
            throw new EntityNotFoundException('La asignatura '.$slug.' no existe en el grado especificado.');
        }

        return $subject;
    }
}
