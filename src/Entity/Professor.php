<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Subject;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'professor', targetEntity: Opinion::class, orphanRemoval: true)]
    private Collection $opinions;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable:true)]
    private array $scoreCount = [
        '1' => 0,
        '2' => 0,
        '3' => 0,
        '4' => 0,
        '5' => 0,
    ];

    #[ORM\Column]
    private ?bool $accepted = false;

    #[ORM\OneToMany(mappedBy: 'professor', targetEntity: RelationSubjectProfessor::class, orphanRemoval: true)]
    private Collection $relationsSubjectProfessor;

    public function __construct()
    {
        $this->opinions = new ArrayCollection();
        $this->relationsSubjectProfessor = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Opinion>
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): static
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions->add($opinion);
            $opinion->setProfessor($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): static
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getProfessor() === $this) {
                $opinion->setProfessor(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
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

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * @return Collection<int, RelationSubjectProfessor>
     */
    public function getRelationsSubjectProfessor(): Collection
    {
        return $this->relationsSubjectProfessor;
    }

    public function addRelationSubjectProfessor(RelationSubjectProfessor $relationsSubjectProfessor): static
    {
        if (!$this->relationsSubjectProfessor->contains($relationsSubjectProfessor)) {
            $this->relationsSubjectProfessor->add($relationsSubjectProfessor);
            $relationsSubjectProfessor->setProfessor($this);
        }

        return $this;
    }

    public function removeRelationSubjectProfessor(RelationSubjectProfessor $relationsSubjectProfessor): static
    {
        if ($this->relationsSubjectProfessor->removeElement($relationsSubjectProfessor)) {
            // set the owning side to null (unless already changed)
            if ($relationsSubjectProfessor->getProfessor() === $this) {
                $relationsSubjectProfessor->setProfessor(null);
            }
        }

        return $this;
    }

    public function getRelationWithSubject(Subject $subject): ?RelationSubjectProfessor {
        foreach ($this->relationsSubjectProfessor as $relation) {
            if ($relation->getSubject() === $subject) {
                return $relation;
            }
        }
        return null;  // si no se encuentra la relación
    }
}
