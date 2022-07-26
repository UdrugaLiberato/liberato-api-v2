<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Answer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class AnswerDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof Answer;
    }

    public function persist($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data): void
    {
        $data = $data->setDeletedAt(new DateTimeImmutable("now"));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}