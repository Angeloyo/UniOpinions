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

        return $professor;
    }

    public function findBySubjectIdAndNameLike($subjectId, $term)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.subject', 's')
            ->where('s.id IN (:subjectId)')
            ->andWhere('LOWER(UNACCENT(p.name)) LIKE LOWER(UNACCENT(:term))')
            ->setParameter('subjectId', $subjectId)
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByNameLike($term){
        return $this->createQueryBuilder('p')  
            ->where('LOWER(UNACCENT(p.name)) LIKE LOWER(UNACCENT(:term))')
            ->andWhere('p.accepted = :accepted')
            ->setParameters([
                'term' => '%' . $term . '%',
                'accepted' => true
            ])
            ->getQuery()
            ->getResult();
    }

    public function findByNameLikeAndUniversity($term, $university){
        return $this->createQueryBuilder('p')
            ->join('p.relationsSubjectProfessor', 'rsp') // Relación con RelationSubjectProfessor
            ->join('rsp.subject', 's') // Relación con Subject
            ->join('s.degree', 'd') // Asume que 'degree' es el campo en la entidad Subject que se relaciona con Degree
            ->where('LOWER(UNACCENT(p.name)) LIKE LOWER(UNACCENT(:term))')
            ->andWhere('d.university = :university') // Ahora usamos 'd.university' en lugar de 's.university'
            ->andWhere('p.accepted = :accepted')
            ->setParameters([
                'term' => '%' . $term . '%',
                'university' => $university,
                'accepted' => true,
            ])
            ->getQuery()
            ->getResult();
    }

        
}
