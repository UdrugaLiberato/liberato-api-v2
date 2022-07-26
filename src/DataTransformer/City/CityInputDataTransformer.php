<?php

namespace App\DataTransformer\City;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\City;
use App\Utils\GoogleMapsInterface;

class CityInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private GoogleMapsInterface $googleMaps)
    {
    }

    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return City
     */
    public function transform($object, string $to, array $context = []): City
    {
        ["lat" => $lat, "lng" => $lng] = $this->googleMaps->getCoordinateForCity($object->name);

        $city = new City();
        $city->setName($object->name);
        $city->setLatitude($lat);
        $city->setLongitude($lng);

        return $city;
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof City) {
            return false;
        }

        return City::class === $to && null !== ($context['input']['class'] ?? null);
    }
}