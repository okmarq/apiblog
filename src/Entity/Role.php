<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'read'],
    denormalizationContext: ['groups' => 'write'],
    attributes:['route_prefix'=>'/roles'],
    itemOperations: ['get', 'put'],
    collectionOperations:["get"]
)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\Column(type: 'string', length: 32, unique: true)]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank()]
    private $roleName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }
}
