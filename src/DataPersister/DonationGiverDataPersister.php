<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DonationGiver;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class DonationGiverDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof DonationGiver;
    }

    public function persist($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data): void
    {
        $data = $data->setDeletedAt(new DateTimeImmutable('now'));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
