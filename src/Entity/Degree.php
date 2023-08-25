<?php

namespace App\Entity;

use App\Repository\DegreeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DegreeRepository::class)]
class Degree
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'degrees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?University $university = null;

    #[ORM\OneToMany(mappedBy: 'degree', targetEntity: Subject::class, orphanRemoval: true)]
    private Collection $subjects;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $accepted = false;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
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

    public function getUniversity(): ?University
    {
        return $this->university;
    }

    public function setUniversity(?University $university): static
    {
        $this->university = $university;

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->setDegree($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            // set the owning side to null (unless already changed)
            if ($subject->getDegree() === $this) {
                $subject->setDegree(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
        // return sprintf('%s (%s)', $this->name, $this->getUniversity()->getName());
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

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getAcceptedSubjects()
    {
        return $this->subjects->filter(function($subject) {
            return $subject->isAccepted() === true;
        });
    }

}
