<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\DTO\DonationGiver\DonationGiverInput;
use App\DTO\DonationGiver\DonationGiverOutput;
use App\Repository\DonationGiverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonationGiverRepository::class)]
#[ApiResource(input: DonationGiverInput::class, output: DonationGiverOutput::class)]
class DonationGiver
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[
        ORM\Column(type: 'string'),
        ApiFilter(SearchFilter::class, strategy: 'ipartial')
    ]
    private string $name;

    #[ORM\Column(type: 'date')]
    private \DateTime $dateOfApplication;

    #[ORM\Column(type: 'boolean')]
    private bool $approved;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $dateOfApproval = null;

    #[ORM\Column(type: 'float')]
    private float $moneyRequested;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $moneyGiven;

    #[ORM\ManyToMany(targetEntity: Project::class, inversedBy: 'donationGivers')]
    private $projects;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
        $this->updatedAt = null;
        $this->dateOfApproval = null;
        $this->deletedAt = null;
        $this->projects = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateOfApplication(): ?\DateTime
    {
        return $this->dateOfApplication;
    }

    public function setDateOfApplication(\DateTime $dateOfApplication): self
    {
        $this->dateOfApplication = $dateOfApplication;

        return $this;
    }

    public function getApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    public function getDateOfApproval(): ?\DateTime
    {
        return $this->dateOfApproval;
    }

    public function setDateOfApproval(?\DateTime $dateOfApproval): self
    {
        $this->dateOfApproval = $dateOfApproval;

        return $this;
    }

    public function getMoneyRequested(): ?float
    {
        return $this->moneyRequested;
    }

    public function setMoneyRequested(float $moneyRequested): self
    {
        $this->moneyRequested = $moneyRequested;

        return $this;
    }

    public function getMoneyGiven(): ?float
    {
        return $this->moneyGiven;
    }

    public function setMoneyGiven(?float $moneyGiven): self
    {
        $this->moneyGiven = $moneyGiven;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->projects->removeElement($project);

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
