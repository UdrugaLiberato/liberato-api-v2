<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\City;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class CityDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof City;
    }

    public function persist($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data): void
    {
        $data->setDeletedAt(new DateTimeImmutable('now'));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
