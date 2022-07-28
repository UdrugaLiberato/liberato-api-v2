<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements DataPersisterInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordEncoder)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist(mixed $data): void
    {
        if ($data->getPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->hashPassword($data, $data->getPassword())
            );
            $data->eraseCredentials();
        }

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
