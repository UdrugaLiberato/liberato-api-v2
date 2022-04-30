<?php

namespace App\DataPersister;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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
        $data = $data->setDeletedAt(new \DateTimeImmutable("now"));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
