<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\DTO\Post\PostInput;
use App\DTO\Post\PostOutput;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(collectionOperations: [
    'get',
    'post' => [
        'input_formats' => [
            'multipart' => ['multipart/form-data'],
        ],
    ],
], input: PostInput::class, output: PostOutput::class)]
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

    #[
        ORM\ManyToMany(targetEntity: MediaObject::class),
        ORM\JoinTable(),
        Groups(['media_object:create'])
    ]
    private $images;


    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    public function __construct()
    {

        $this->images = new ArrayCollection();
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


    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(MediaObject $image)
    {
        $this->images->add($image);
    }

    public function removeImage(MediaObject $image)
    {
        $this->images->removeElement($image);
    }
}
