<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_location'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['get_location'])]
    private ?string $area = null;

    #[ORM\Column(length: 30)]
    #[Groups(['get_location'])]
    private ?string $department = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['get_location'])]
    private ?string $district = null;

    #[ORM\Column(length: 50)]
    #[Groups(['get_location'])]
    private ?string $street = null;

    #[ORM\Column(length: 50)]
    #[Groups(['get_location'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['get_location'])]
    private ?int $zipcode = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8)]
    #[Groups(['get_location'])]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 11, scale: 8)]
    #[Groups(['get_location'])]
    private ?string $longitude = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Memory::class, orphanRemoval: true)]
    #[Groups(['get_location'])]
    private Collection $memories;

    #[ORM\OneToMany(mappedBy: 'location', targetEntity: Place::class, orphanRemoval: true)]
    #[Groups(['get_location'])]
    private Collection $places;


    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->memories = new ArrayCollection();
        $this->places = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(string $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): static
    {
        $this->district = $district;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?int
    {
        return $this->zipcode;
    }

    public function setZipcode(int $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

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
            $memory->setLocation($this);
        }

        return $this;
    }

    public function removeMemory(Memory $memory): static
    {
        if ($this->memories->removeElement($memory)) {
            // set the owning side to null (unless already changed)
            if ($memory->getLocation() === $this) {
                $memory->setLocation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
            $place->setLocation($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getLocation() === $this) {
                $place->setLocation(null);
            }
        }

        return $this;
    }

}
