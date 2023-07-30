<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use App\Utils\GoogleMapsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateCityController {
  public function __construct(
      private CityRepository      $cityRepository,
      private GoogleMapsInterface $googleMaps
  ) {
  }

  public function __invoke(string $id, Request $request): City {
    $name = $request->toArray()['name'] ?? NULL;
    $radiusInKm = $request->toArray()['radiusInKm'] ?? NULL;
    $cityToUpdate = $this->cityRepository->find($id);

    if ($name !== $cityToUpdate->getName() && $name !== NULL) {
      ['lat' => $lat, 'lng' => $lng] = $this->googleMaps->getCoordinateForCity($name);
      $cityToUpdate->setLatitude($lat);
      $cityToUpdate->setLongitude($lng);
      $cityToUpdate->setName($name);
    }

    if ($radiusInKm !== $cityToUpdate->getRadiusInKm() && $radiusInKm !== NULL) {
      $cityToUpdate->setRadiusInKm($radiusInKm);
    }
     $this->cityRepository->update($cityToUpdate);

    return $cityToUpdate;
  }
}
