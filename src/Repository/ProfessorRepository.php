<?php

namespace App\Repository;

use App\Entity\Professor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @extends ServiceEntityRepository<Professor>
 *
 * @method Professor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Professor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Professor[]    findAll()
 * @method Professor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professor::class);
    }

    public function findOneBySlug(string $slug): ?Professor
    {
        $professor = $this->findOneBy(['slug' => $slug]);

        if (!$professor) {
            throw new EntityNotFoundException('El profesor '.$slug.' no existe.');
        }

        return $professor;
    }
}
