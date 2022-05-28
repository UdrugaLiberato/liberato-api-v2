<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource]
class Project
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'date')]
    private \DateTime $start;

    #[ORM\Column(type: 'date')]
    private \DateTime $end;

    #[ORM\Column(type: 'float')]
    private float $moneyNeeded;

    #[ORM\Column(type: 'float')]
    private float $moneyGathered;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private \DateTimeImmutable $deletedAt;

    #[ORM\ManyToMany(targetEntity: DonationGiver::class, mappedBy: 'projects')]
    private $donationGivers;

    #[ORM\OneToOne(mappedBy: 'project', targetEntity: Invoice::class, cascade: ['persist', 'remove'])]
    private $invoice;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
        $this->moneyGathered = 0.00;
        $this->donationGivers = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

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

    public function getMoneyNeeded(): ?float
    {
        return $this->moneyNeeded;
    }

    public function setMoneyNeeded(float $moneyNeeded): self
    {
        $this->moneyNeeded = $moneyNeeded;

        return $this;
    }

    public function getMoneyGathered(): ?float
    {
        return $this->moneyGathered;
    }

    public function setMoneyGathered(float $moneyGathered): self
    {
        $this->moneyGathered = $moneyGathered;

        return $this;
    }

    /**
     * @return Collection<int, DonationGiver>
     */
    public function getDonationGivers(): Collection
    {
        return $this->donationGivers;
    }

    public function addDonationGiver(DonationGiver $donationGiver): self
    {
        if (!$this->donationGivers->contains($donationGiver)) {
            $this->donationGivers[] = $donationGiver;
            $donationGiver->addRoject($this);
        }

        return $this;
    }

    public function removeDonationGiver(DonationGiver $donationGiver): self
    {
        if ($this->donationGivers->removeElement($donationGiver)) {
            $donationGiver->removeRoject($this);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }


    public function setInvoice(Invoice $invoice): self
    {
        // set the owning side of the relation if necessary
        if ($invoice->getProject() !== $this) {
            $invoice->setProject($this);
        }

        $this->invoice = $invoice;

        return $this;
    }
}
