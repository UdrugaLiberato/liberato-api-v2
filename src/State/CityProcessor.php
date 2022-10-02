<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Utils\GoogleMapsInterface;

class CityProcessor implements ProcessorInterface
{
    public function __construct(private GoogleMapsInterface $googleMaps, private CityRepository $repository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        ['lat' => $lat, 'lng' => $lng] = $this->googleMaps->getCoordinateForCity($data->getName());

        $city = new City();
        $city->setName($data->getName());
        $city->setLatitude($lat);
        $city->setLongitude($lng);

        $this->repository->add($city);
    }
}
