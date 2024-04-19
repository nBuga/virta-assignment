<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups('company:station:read1')]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups('company:station:read')]
    private ?string $name = null;

    #[ORM\Column(type: Types::FLOAT, precision: 8, scale: 6)]
    #[Assert\NotBlank]
    #[Groups('company:station:read')]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::FLOAT, precision: 8, scale: 6)]
    #[Assert\NotBlank]
    #[Groups('company:station:read')]
    private ?float $longitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups('company:station:read')]
    private ?string $address = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'stations')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(2)]
    #[Assert\NotBlank]
    private ?Company $company = null;

    #[ORM\Column]
    #[Groups('company:station:read')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups('company:station:read')]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }
}
