<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\City\CityOutput;
use App\Entity\City;
use App\Repository\CityRepository;
use App\Utils\GoogleMapsInterface;

class CityProcessor implements ProcessorInterface
{
    public function __construct(private GoogleMapsInterface $googleMaps, private CityRepository $repository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        ['lat' => $lat, 'lng' => $lng] = $this->googleMaps->getCoordinateForCity($data->name);

        $city = new City();
        $city->setName($data->name);
        $city->setLatitude($lat);
        $city->setLongitude($lng);

        $this->repository->add($city);

        return new CityOutput(
            $city->getId(),
            $city->getName(),
            $city->getLatitude(),
            $city->getLongitude(),
            $city->getCreatedAt()->format('Y-m-d H:i:s'),
            \count($city->getLocations()->toArray())
        );
    }
}
