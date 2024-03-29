<?php

namespace App\Entity;

use App\Repository\ExaminationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ExaminationRepository::class)]
class Examination
{
    private const ONE_HOUR_IN_MINUTES = 60;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(
        [
            'examination_created',
            'examination_showAll'
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(
        [
            'examination_created',
            'examination_showAll',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(
        [
            'examination_created',
            'examination_showAll',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?int $duration = null;

    #[ORM\Column]
    #[Groups(
        [
            'examination_created',
            'examination_showAll',
            'healthRecord_created',
            'healthRecord_showAll'
        ]
    )]
    private ?int $price = null;

    #[ORM\Column]
    #[Groups(
        [
            'examination_created',
            'examination_showAll'
        ]
    )]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(
        [
//            'examination_created',
            'examination_showAll'
        ]
    )]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'examination', targetEntity: HealthRecord::class)]
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

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


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
            $healthRecord->setExamination($this);
        }

        return $this;
    }

    public function removeHealthRecord(HealthRecord $healthRecord): self
    {
        if ($this->healthRecords->removeElement($healthRecord)) {
            // set the owning side to null (unless already changed)
            if ($healthRecord->getExamination() === $this) {
                $healthRecord->setExamination(null);
            }
        }

        return $this;
    }

    public function descriptiveLength(): bool
    {
        if ($this->getDuration() > 60)
            return 'Long';
        if ($this->getDuration() > 30)
            return 'Medium';
        if ($this->getDuration() > 15)
            return 'Short';

        return 'Mini';
    }
}
