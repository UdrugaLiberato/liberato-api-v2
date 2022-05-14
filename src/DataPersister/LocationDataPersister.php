<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;

class LocationDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof Location;
    }

    public function persist($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data)
    {
        $data = $data->setDeletedAt(new \DateTimeImmutable("now"));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}