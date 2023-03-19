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
    $cityToUpdate = $this->cityRepository->find($id);

    if ($name === $cityToUpdate->getName()) {
      throw new \Exception('Invalid city name or id');
    }
    ['lat' => $lat, 'lng' => $lng] = $this->googleMaps->getCoordinateForCity($name);

    $cityToUpdate->setName($name);
    $cityToUpdate->setLatitude($lat);
    $cityToUpdate->setLongitude($lng);

    $this->cityRepository->update($cityToUpdate);

    return $cityToUpdate;
  }
}
