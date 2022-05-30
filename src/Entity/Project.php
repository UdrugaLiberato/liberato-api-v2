<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\DTO\Project\ProjectOutput;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    denormalizationContext: ['groups' => ['write']],
output: ProjectOutput::class),
    ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private $id;

    #[
        ORM\Column(type: 'string', length: 255),
        ApiFilter(SearchFilter::class, strategy: 'ipartial'),
        Groups(["write"])
    ]
    private string $name;

    #[
        ORM\Column(type: 'text'),
        Groups(["write"])
    ]
    private string $description;

    #[
        ORM\Column(type: 'date'),
        Groups(["write"])
    ]
    private \DateTime $start;

    #[
        ORM\Column(type: 'date'),
        Groups(["write"])
    ]
    private \DateTime $end;

    #[
        ORM\Column(type: 'float'),
        Groups(["write"])
    ]
    private float $moneyNeeded;

    #[
        ORM\Column(type: 'float'),
        Groups(["write"])
    ]
    private float $moneyGathered;

    #[
        ORM\Column(type: 'datetime_immutable'),
    ]
    private \DateTimeImmutable $createdAt;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
    ]
    private ?\DateTimeImmutable $updatedAt;

    #[
        ORM\Column(type: 'datetime_immutable', nullable: true),
    ]
    private ?\DateTimeImmutable $deletedAt;

    #[
        ORM\ManyToMany(targetEntity: DonationGiver::class, mappedBy: 'projects'),
        Groups(["write"])
    ]
    private $donationGivers;

    #[
        ORM\OneToMany(mappedBy: 'project', targetEntity: Invoice::class, cascade: ["persist"]),
        Groups(["write"])
    ]
    private $invoices;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable("now");
        $this->moneyGathered = 0.00;
        $this->updatedAt = null;
        $this->deletedAt = null;
        $this->donationGivers = new ArrayCollection();
        $this->invoices = new ArrayCollection();
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

    public function setStart(\DateTime $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): self
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
            $donationGiver->addProject($this);
        }

        return $this;
    }

    public function removeDonationGiver(DonationGiver $donationGiver): self
    {
        if ($this->donationGivers->removeElement($donationGiver)) {
            $donationGiver->removeProject($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setProject($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getProject() === $this) {
                $invoice->setProject(null);
            }
        }

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
