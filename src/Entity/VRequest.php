<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VRequestRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'read'],
    denormalizationContext: ['groups' => 'write'],
    attributes:['route_prefix'=>'/requests'],
    itemOperations: ['get', 'put']
)]
class VRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\OneToOne(inversedBy: 'vRequest', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('read')]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank()]
    private $idImage;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank()]
    private $message;

    #[ORM\ManyToOne(targetEntity: Status::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('read')]
    private $status;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups('read')]
    private $reason;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups('read')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'], columnDefinition: "DATETIME on update CURRENT_TIMESTAMP")]
    #[Groups('read')]
    private $modifiedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->modifiedAt = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdImage(): ?string
    {
        return $this->idImage;
    }

    public function setIdImage(string $idImage): self
    {
        $this->idImage = $idImage;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser(),
            'idImage' => $this->getIdImage(),
            'message' => $this->getMessage(),
            'status' => $this->getStatus(),
            'reason' => $this->getReason(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedAt' => ($this->getModifiedAt()) ? $this->getModifiedAt()->format('Y-m-d H:i:s') : null,
        ];
    }
}
