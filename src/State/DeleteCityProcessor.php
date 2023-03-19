<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\CityRepository;

class DeleteCityProcessor implements ProcessorInterface {
  public function __construct(
      public CityRepository $cityRepository,
  ) {
  }

  public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
    $city = $this->cityRepository->find($uriVariables['id']);
    $city->setDeletedAt(new \DateTimeImmutable());
    $this->cityRepository->update($city);
  }
}
