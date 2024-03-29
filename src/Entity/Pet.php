<?php

namespace App\Entity;

use App\Repository\PetRepository;

//use DateTime;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'pet_created',
            'pet_showAll',
            'pet_showByUser',
            'pet_foundPet',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(
        [
            'pet_created',
            'pet_showAll',
            'pet_showByUser',
            'pet_foundPet',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'pet_created',
            'pet_showAll',
            'pet_showByUser',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?string $animal = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'pet_created',
            'pet_showAll',
            'pet_showByUser',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?string $breed = null;

    #[ORM\ManyToOne(inversedBy: 'pets')]
    #[Groups(
        [
            'pet_created',
            'pet_showAll',
            'pet_foundPet'
        ]
    )]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $owner = null;

    #[ORM\Column]
    #[Groups(
        [
            'pet_created',
            'pet_showByUser',
            'pet_showAll'
        ]
    )]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(
        [
            'pet_created',
            'pet_showAll'
        ]
    )]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'pet', targetEntity: HealthRecord::class,cascade: ['persist','remove'])]
    private Collection $healthRecords;

    public function __construct()
    {
        $this->healthRecords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getAnimal(): ?string
    {
        return $this->animal;
    }

    public function setAnimal(string $animal): self
    {
        $this->animal = $animal;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

//    public function setCreatedAt(\DateTimeImmutable $createdAt): self
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, HealthRecord>
     */
    public function getHealthRecords(): Collection
    {
        return $this->healthRecords;
    }

    public function addHealthRecord(HealthRecord $healthRecord): self
    {
        if (!$this->healthRecords->contains($healthRecord)) {
            $this->healthRecords->add($healthRecord);
            $healthRecord->setPet($this);
        }

        return $this;
    }

    public function removeHealthRecord(HealthRecord $healthRecord): self
    {
        if ($this->healthRecords->removeElement($healthRecord)) {
            // set the owning side to null (unless already changed)
            if ($healthRecord->getPet() === $this) {
                $healthRecord->setPet(null);
            }
        }

        return $this;
    }
}
