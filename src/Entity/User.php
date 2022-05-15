<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\DTO\User\UserInput;
use App\DTO\User\UserOutput;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable,
    ORM\Entity(repositoryClass: UserRepository::class),
    ApiResource(collectionOperations: [
        'get' => [
            'security' => "is_granted('ROLE_ADMIN')",
            'security_message' => "Only admin users are allowed to list users.",
        ],
        'post' => [
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
        ],
    ], input: UserInput::class, output: UserOutput::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_USER = "ROLE_USER";

    #[Vich\UploadableField(mapping: "avatar", fileNameProperty: "filePath"),
        Assert\File(
            mimeTypes: ["image/*"]
        )
    ]
    public ?File $file = null;

    #[
        ORM\Column(nullable: true),
    ]
    public ?string $filePath = null;

    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private string $id;

    #[
        ORM\Column(type: 'string', length: 180, unique: true),
        Assert\Email
    ]
    private string $email;

    #[
        ORM\Column(type: 'string', length: 180, nullable: true),
    ]
    private ?string $phone;

    #[
        ORM\Column(type: 'json'),
        Assert\NotNull
    ]
    private array $roles;

    #[
        ORM\Column(type: 'string'),
        Assert\Length(min: 8, max: 32, minMessage: "Password must be at least 8 characters long!",
            maxMessage: "Password must be at most 32 characters")
    ]
    private string $password;

    #[
        ApiFilter(SearchFilter::class, strategy: 'ipartial'),
        ORM\Column(type: 'string', length: 255),
        Assert\Length(min: 4, minMessage: "Username must be at least {{ limit }} characters long!")
    ]
    private string $username;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class)]
    private $posts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Location::class)]
    private $locations;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->roles[] = $this::ROLE_USER;
        $this->posts = new ArrayCollection();
        $this->phone = null;
        $this->locations = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(string $role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->username;
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

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setUser($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getUser() === $this) {
                $location->setUser(null);
            }
        }

        return $this;
    }

}
