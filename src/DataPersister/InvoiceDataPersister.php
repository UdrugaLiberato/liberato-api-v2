<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof Invoice;
    }

    public function persist($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data): void
    {
        $data = $data->setDeletedAt(new \DateTimeImmutable("now"));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}