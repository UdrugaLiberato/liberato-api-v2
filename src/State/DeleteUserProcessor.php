<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\UserRepository;

class DeleteUserProcessor implements ProcessorInterface {
  public function __construct(
      private UserRepository $userRepository
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
    $user = $this->userRepository->find($uriVariables['id']);
    if (NULL !== $user->getDeletedAt()) {
      throw new \Exception('User is already deactivated');
    }
    $user->setDeletedAt(new \DateTimeImmutable());
    $this->userRepository->update($user);
  }
}
