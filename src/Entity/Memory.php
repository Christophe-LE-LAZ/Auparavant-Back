<?php

namespace App\Entity;

use App\Repository\MemoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemoryRepository::class)]
class Memory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $picture_date = null;

    #[ORM\Column(length: 2000)]
    private ?string $main_picture = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'memories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'memories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\OneToMany(mappedBy: 'memory', targetEntity: Picture::class, orphanRemoval: true)]
    private Collection $picture;

    public function __construct()
    {
        $this->picture = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPictureDate(): ?\DateTimeInterface
    {
        return $this->picture_date;
    }

    public function setPictureDate(\DateTimeInterface $picture_date): static
    {
        $this->picture_date = $picture_date;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->main_picture;
    }

    public function setMainPicture(string $main_picture): static
    {
        $this->main_picture = $main_picture;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
     * @return Collection<int, Picture>
     */
    public function getPicture(): Collection
    {
        return $this->picture;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->picture->contains($picture)) {
            $this->picture->add($picture);
            $picture->setMemory($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->picture->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getMemory() === $this) {
                $picture->setMemory(null);
            }
        }

        return $this;
    }
}
