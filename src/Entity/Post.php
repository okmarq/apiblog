<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'read'],
    denormalizationContext: ['groups' => 'write'],
    attributes:['route_prefix'=>'/posts'],
    itemOperations: [
        "get", 
        'patch', 
        "delete",
        'get_by_slug' => [
            'method' => 'get',
            'path' => '/post/{slug}',
            'controller' => PostController::class,
            'read' => true,
            'openapi_context' => [
                'parameters' => [
                    [
                        'name' => 'slug',
                        'in' => 'slug',
                        'description' => 'the slug of the post',
                        'type' => 'string',
                        'required' => true,
                        'example' => 'lowercase-title-of-the-post',
                    ]
                ]
            ]
        ]
    ]
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["read", "write"])]
    #[Assert\NotBlank()]
    private $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups('read')]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank()]
    private $content;

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

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        if ($this->createdAt == null) {
            $this->createdAt = new \DateTimeImmutable('now');
        }
        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(): self
    {
        if ($this->modifiedAt == null) {
            $this->modifiedAt = new \DateTimeImmutable('now');
        }
        return $this;
    }

    public function toArray()
    {
        return [
            'title' => $this->getTitle(),
            'slug' => $this->getSlug(),
            'content' => $this->getContent(),
            'id' => $this->getId(),
            'firstname' => $this->getUser()->getFirstname(),
            'lastname' => $this->getUser()->getLastname(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'modifiedAt' => ($this->getModifiedAt()) ? $this->getModifiedAt()->format('Y-m-d H:i:s') : null,
        ];
    }
}
