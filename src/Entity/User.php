<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\Controller\UpdateUserController;
use App\DTO\User\UserInput;
use App\Exception\UserIsDeactivatedException;
use App\Repository\UserRepository;
use App\State\DeleteUserProcessor;
use App\State\UserProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ApiResource(normalizationContext: ['groups' => ['user:read', 'news:read']]),
    GetCollection(),
    \ApiPlatform\Metadata\Post(
        inputFormats: ['multipart' => ['multipart/form-data']],
        input: UserInput::class,
        processor: UserProcessor::class,
    ),
    Get(),
    Put(
        inputFormats: ['multipart' => ['multipart/form-data']],
        controller: UpdateUserController::class,
        security: 'is_granted("ROLE_ADMIN") or object == user',
        securityMessage: 'Only admins can update other users',
    ),
    Delete(
        exceptionToStatus: [UserIsDeactivatedException::class => 400],
        security: 'is_granted("ROLE_ADMIN") or object == user',
        securityMessage: 'Only admins can delete other users',
        processor: DeleteUserProcessor::class
    )]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_USER = 'ROLE_USER';

    #[
        ApiProperty(identifier: true),
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: 'CUSTOM'),
        ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
        Groups(['user:read', 'news:read'])
    ]
    private string $id;

    #[
        ORM\Column(type: 'string', length: 180, unique: true),
        Assert\Email,
        Groups(['user:read'])
    ]
    private string $email;

    #[
        ORM\Column(type: 'string', length: 180, nullable: true),
        Groups(['user:read'])
    ]
    private ?string $phone = '';

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'json'), Groups(['user:read'])]
    private array $roles;

    #[
        ORM\Column(type: 'string'),
        Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters long!')
    ]
    private string $password;

    #[
        Groups(['user:read', 'news:read']),
        ORM\Column(type: 'string', length: 255),
        Assert\Length(min: 4, minMessage: 'Username must be at least {{ limit }} characters long!')
    ]
    private string $username;

    #[ORM\Column(type: 'array'), Groups(['news:read', 'user:read'])]
    private ArrayCollection $avatar;

    #[ORM\Column(type: 'datetime_immutable'), Groups(['user:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['user:read'])]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['user:read'])]
    private ?\DateTimeImmutable $deletedAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Location::class)]
    private Collection $locations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: News::class)]
    private Collection $news;

    public function __construct()
    {
        $this->phone = '';
        $this->avatar = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->locations = new ArrayCollection();
        $this->news = new ArrayCollection();
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
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(string $role): self
    {
        $this->roles = [];
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
        return $this->getName();
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

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int>
     */
    public function getAvatar(): ArrayCollection
    {
        return $this->avatar;
    }

    public function setAvatar(ArrayCollection $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getLocations(): ArrayCollection
    {
        return new ArrayCollection($this->locations->toArray());
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

    /**
     * @return Collection<int, News>
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news->add($news);
            $news->setUser($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->removeElement($news)) {
            // set the owning side to null (unless already changed)
            if ($news->getUser() === $this) {
                $news->setUser(null);
            }
        }

        return $this;
    }
}
