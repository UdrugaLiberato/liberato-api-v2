<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreatePostAction;
use App\DTO\Post\PostInput;
use App\DTO\Post\PostOutput;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource(collectionOperations: [
  'get',
  'post' => [
    'controller' => CreatePostAction::class,
    'deserialize' => false,
    'input_formats' => [
      'multipart' => ['multipart/form-data'],
    ],
  ],
], output: PostOutput::class)]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: "CUSTOM"),
      ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;
    
    #[
      ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts'),
      ORM\JoinColumn(nullable: false)
    ]
    private User $author;
    
    #[
      ORM\Column(type: 'string', length: 255)
    ]
    private string $title;
    
    #[
      ORM\Column(type: 'text')
    ]
    private string $body;
    
    #[
      ORM\Column(type: 'string')
    ]
    private string $tags;
    
    #[ORM\Column(type: 'array')]
    private array $images;
    
    
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;
    
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;
    
    public function __construct()
    {
        $this->images = [];
        $this->createdAt = new \DateTimeImmutable("now");
        $this->deletedAt = null;
        $this->updatedAt = null;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getAuthor(): ?User
    {
        return $this->author;
    }
    
    public function setAuthor(?User $author): self
    {
        $this->author = $author;
        
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
    
    public function getBody(): ?string
    {
        return $this->body;
    }
    
    public function setBody(string $body): self
    {
        $this->body = $body;
        
        return $this;
    }
    
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
    
    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
    
    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        
        return $this;
    }
    
    public function getTags(): string
    {
        return $this->tags;
    }
    
    public function setTags(string $tags): void
    {
        $this->tags = $tags;
    }
    
    public function getImages(): ArrayCollection|array
    {
        return $this->images;
    }
    
    public function setImages(ArrayCollection|array $images): void
    {
        $this->images = $images;
    }
}
