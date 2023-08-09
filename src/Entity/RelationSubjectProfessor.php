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
    private ?bool $accepted = false;

    #[ORM\Column]
    private array $scoreCount = [
        '1' => 0,
        '2' => 0,
        '3' => 0,
        '4' => 0,
        '5' => 0,
    ];

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

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s-%s)', $this->professor->getName(), $this->getSubject()->getName());
    }

    public function getScoreCount(): array
    {
        return $this->scoreCount;
    }

    public function setScoreCount(array $scoreCount): static
    {
        $this->scoreCount = $scoreCount;

        return $this;
    }

    public function incrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]++;
    }

    public function decrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]--;
        // $this->scoreCount[$score] = max(0, $this->scoreCount[$score] - 1);
    }
}
