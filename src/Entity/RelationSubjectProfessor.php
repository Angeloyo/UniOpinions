<?php

namespace App\Entity;

use App\Repository\RelationSubjectProfessorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationSubjectProfessorRepository::class)]
class RelationSubjectProfessor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'relationsSubjectProfessor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professor $professor = null;

    #[ORM\ManyToOne(inversedBy: 'relationsSubjectProfessor')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column]
    private ?bool $reviewed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }

    public function setProfessor(?Professor $professor): static
    {
        $this->professor = $professor;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function isReviewed(): ?bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): static
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s-%s)', $this->professor->getName(), $this->getSubject()->getName());
    }
}
