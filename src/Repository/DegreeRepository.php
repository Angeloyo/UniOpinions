<?php

namespace App\Repository;

use App\Entity\Degree;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;
use App\Entity\University;

/**
 * @extends ServiceEntityRepository<Degree>
 *
 * @method Degree|null find($id, $lockMode = null, $lockVersion = null)
 * @method Degree|null findOneBy(array $criteria, array $orderBy = null)
 * @method Degree[]    findAll()
 * @method Degree[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DegreeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Degree::class);
    }

    public function findOneBySlugAndUniversity(string $slug, University $university): ?Degree
    {
        $degree = $this->findOneBy(['slug' => $slug, 'university' => $university]);

        if (!$degree) {
            throw new EntityNotFoundException('El grado '.$slug.' no existe en la universidad especificada');
        }

        return $degree;
    }

    public function findOneByIdAndUniversity(int $id, University $university): ?Degree
    {
        $degree = $this->findOneBy(['id' => $id, 'university' => $university]);

        if (!$degree) {
            throw new EntityNotFoundException('El grado con id '.$id.' no existe en la universidad especificada');
        }

        return $degree;
    }

    public function findByUniversityIdAndNameLike($universityId, $term)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.university', 'u')
            ->where('u.id = :universityId')
            ->andWhere('LOWER(UNACCENT(d.name)) LIKE LOWER(UNACCENT(:term))')
            ->setParameter('universityId', $universityId)
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }


}
