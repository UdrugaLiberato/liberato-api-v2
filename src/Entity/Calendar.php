<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\Event\EventInput;
use App\DTO\Event\EventOutput;
use App\Repository\CalendarRepository;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    input: EventInput::class, output: EventOutput::class
),
    ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[
        ORM\Id,
        ORM\Column(type: 'string', unique: true),
        ORM\GeneratedValue(strategy: "CUSTOM"),
        ORM\CustomIdGenerator(class: "doctrine.uuid_generator")
    ]
    private string $Id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $Subject;

    #[ORM\Column(type: 'string', length: 255)]
    private string $StartTime;

    public function getId(): string
    {
        return $this->Id;
    }

    public function getSubject(): ?string
    {
        return $this->Subject;
    }

    public function setSubject(string $Subject): self
    {
        $this->Subject = $Subject;

        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->StartTime;
    }

    public function setStartTime(string $StartTime): self
    {
        $this->StartTime = $StartTime;

        return $this;
    }

}
