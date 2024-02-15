<?php

namespace App\Entity;

use App\Entity\Memory;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_place'])]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'Le nom de l\'endroit doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le nom de l\'endroit ne peut pas dépasser {{ limit }} caractères.',
    )]
    #[Groups(['get_place'])]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'Le type d\'endroit doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le type d\'endroit ne peut pas dépasser {{ limit }} caractères.',
    )]
    #[Groups(['get_place'])]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'places')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\OneToMany(mappedBy: 'place', targetEntity: Memory::class, orphanRemoval: true)]
    private Collection $memories;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->memories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Memory>
     */
    public function getMemories(): Collection
    {
        return $this->memories;
    }

    public function addMemory(Memory $memory): static
    {
        if (!$this->memories->contains($memory)) {
            $this->memories->add($memory);
            $memory->setPlace($this);
        }

        return $this;
    }

    public function removeMemory(Memory $memory): static
    {
        if ($this->memories->removeElement($memory)) {
            // set the owning side to null (unless already changed)
            if ($memory->getPlace() === $this) {
                $memory->setPlace(null);
            }
        }

        return $this;
    }
}
