<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Memory;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_picture'])]
    private ?int $id = null;

    #[ORM\Column(length: 2000)]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas valide',
    )]
    #[Groups(['get_memory'])]
    private ?string $main_picture = null;

    #[ORM\Column(length: 2000, nullable: true)]
    #[Assert\Url(
        message: 'L\'url {{ value }} n\'est pas valide',
    )]
    #[Groups(['get_picture'])]
    private ?string $picture = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'picture', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get_memory_id'])]
    private ?Memory $memory = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

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

    public function getMemory(): ?Memory
    {
        return $this->memory;
    }

    public function setMemory(?Memory $memory): static
    {
        $this->memory = $memory;

        return $this;
    }
}
