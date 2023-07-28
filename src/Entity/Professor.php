<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'professors')]
    private Collection $subject;

    #[ORM\OneToMany(mappedBy: 'professor', targetEntity: Opinion::class, orphanRemoval: true)]
    private Collection $opinions;

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

    public function __construct()
    {
        $this->subject = new ArrayCollection();
        $this->opinions = new ArrayCollection();
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
     * @return Collection<int, Subject>
     */
    public function getSubject(): Collection
    {
        return $this->subject;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subject->contains($subject)) {
            $this->subject->add($subject);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subject->removeElement($subject);

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

    public function incrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]++;
    }

    public function decrementScoreCount(int $score): void
    {
        $this->scoreCount[$score]--;
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
}
