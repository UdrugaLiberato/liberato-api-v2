<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\LocationRepository;

class LocationProvider implements ProviderInterface
{
    public function __construct(
        private LocationRepository $locationRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        if ($operation instanceof CollectionOperationInterface) {
            try {
                return $this->locationRepository->findAll();
            } catch (\Exception $exception) {
                throw new RuntimeException(sprintf('Unable to retrieve cities from external source: %s', $exception->getMessage()));
            }
        }

        return $this->locationRepository->find($uriVariables['id']);
    }
}
