<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\LocationRepository;

class DeleteLocationProcessor implements ProcessorInterface
{
    public function __construct(
        private LocationRepository $locationRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $location = $this->locationRepository->find($uriVariables['id']);
        $location->setDeletedAt(new \DateTimeImmutable());
        $this->locationRepository->save($location);
    }
}
