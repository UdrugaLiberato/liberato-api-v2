<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question {
  #[
      ORM\Id,
      ORM\Column(type: 'string', unique: true),
      ORM\GeneratedValue(strategy: 'CUSTOM'),
      ORM\CustomIdGenerator(class: 'doctrine.uuid_generator'),
      Groups(['category:read'])
  ]
  private string $id;

  #[
      ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'questions'),
      ORM\JoinColumn(
          name: 'category_id',
          referencedColumnName: 'id',
          onDelete: 'CASCADE'
      ),
  ]
  private ?Category $category = NULL;

  #[
      ORM\Column(type: 'string', length: 255),
      Assert\Length(min: 5, minMessage: 'Question must be at least {{ limit }} characters long!'),
      Groups(['category:read'])
  ]
  private string $question;

  #[
      ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class,
          cascade: ['persist', 'remove']),
      ORM\JoinColumn(name: 'answer_id', referencedColumnName: 'id', onDelete: 'CASCADE'),
  ]
  private ?Collection $answer;

  #[ORM\Column(type: 'datetime_immutable')]
  private \DateTimeImmutable $createdAt;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  private ?\DateTimeImmutable $updatedAt = NULL;

  #[ORM\Column(type: 'datetime_immutable', nullable: true)]
  private ?\DateTimeImmutable $deletedAt = NULL;

  public function __construct() {
    $this->createdAt = new \DateTimeImmutable('now');
    $this->answer = new ArrayCollection();
  }

  public function getId(): string {
    return $this->id;
  }

  public function getCategory(): ?Category {
    return $this->category;
  }

  public function setCategory(?Category $category): self {
    $this->category = $category;

    return $this;
  }

  public function getQuestion(): ?string {
    return $this->question;
  }

  public function setQuestion(string $question): self {
    $this->question = $question;

    return $this;
  }

  public function getCreatedAt(): \DateTimeImmutable {
    return $this->createdAt;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void {
    $this->updatedAt = $updatedAt;
  }

  public function getDeletedAt(): ?\DateTimeImmutable {
    return $this->deletedAt;
  }

  public function setDeletedAt(?\DateTimeImmutable $deletedAt): void {
    $this->deletedAt = $deletedAt;
  }
}
