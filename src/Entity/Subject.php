<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'subjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Degree $degree = null;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Opinion::class, orphanRemoval: true)]
    private Collection $opinions;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private array $scoreCount = [
        '1' => 0,
        '2' => 0,
        '3' => 0,
        '4' => 0,
        '5' => 0,
    ];

    #[ORM\Column]
    private ?bool $accepted = false;

    #[ORM\Column]
    private ?bool $reviewed = false;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: RelationSubjectProfessor::class, orphanRemoval: true)]
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

    public function getDegree(): ?Degree
    {
        return $this->degree;
    }

    public function setDegree(?Degree $degree): static
    {
        $this->degree = $degree;

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
            $opinion->setSubject($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): static
    {
        if ($this->opinions->removeElement($opinion)) {
            // set the owning side to null (unless already changed)
            if ($opinion->getSubject() === $this) {
                $opinion->setSubject(null);
            }
        }

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
        // return sprintf('%s, %s, %s)', $this->name, $this->getDegree()->getName(),  $this->getDegree()->getUniversity()->getName());
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

    public function incrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]++;
    }

    public function decrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]--;
        // $this->scoreCount = max(0, $this->scoreCount - 1);
        // $this->scoreCount[$score] = max(0, $this->scoreCount[$score] - 1);

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

    public function isReviewed(): ?bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): static
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    // public function getAcceptedProfessors(): array
    // {
    //     $acceptedProfessors = [];

    //     foreach ($this->professors as $professor) {
    //         if ($professor->isAccepted() === true) {
    //             $acceptedProfessors[] = $professor;
    //         }
    //     }

    //     return $acceptedProfessors;
    // }

    public function getAcceptedProfessors(): array
    {
        $acceptedProfessors = [];

        foreach ($this->relationsSubjectProfessor as $relation) {
            if ($relation->isReviewed() === true) {
                $acceptedProfessors[] = $relation->getProfessor();
            }
        }

        return $acceptedProfessors;
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
            $relationsSubjectProfessor->setSubject($this);
        }

        return $this;
    }

    public function removeRelationSubjectProfessor(RelationSubjectProfessor $relationsSubjectProfessor): static
    {
        if ($this->relationsSubjectProfessor->removeElement($relationsSubjectProfessor)) {
            // set the owning side to null (unless already changed)
            if ($relationsSubjectProfessor->getSubject() === $this) {
                $relationsSubjectProfessor->setSubject(null);
            }
        }

        return $this;
    }
}
