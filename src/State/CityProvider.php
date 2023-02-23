<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Exception\RuntimeException;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\CityRepository;
use App\State\Extension\PaginationExtensionInterface;

class CityProvider implements ProviderInterface
{
    public function __construct(
        private CityRepository $repository,
        private PaginationExtensionInterface $paginationExtension
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        if ($operation instanceof CollectionOperationInterface) {
            try {
                $collection = $this->repository->findAll();
            } catch (\Exception $exception) {
                throw new RuntimeException(sprintf('Unable to retrieve cities from external source: %s', $exception->getMessage()));
            }

            if (!$this->paginationExtension->isEnabled($resourceClass, $operation, $context)) {
                return $collection;
            }

            return $this->paginationExtension->getResult($collection, $resourceClass, $operation, $context);
        }

        return $this->repository->find($uriVariables['id']);
    }
}
