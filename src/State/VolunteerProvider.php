<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\VolunteerRepository;
use Exception;

class VolunteerProvider implements ProviderInterface
{
  public function __construct(private readonly VolunteerRepository $volunteerRepository) {}

  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
      if ($operation instanceof CollectionOperationInterface) {
        try {
          return $this->volunteerRepository->findAll();
        } catch (Exception $exception) {
          throw new RuntimeException(sprintf('Unable to retrieve volunteer from external source: %s', $exception->getMessage()));
        }
      }
      return $this->volunteerRepository->find($uriVariables['id']);
    }
}
