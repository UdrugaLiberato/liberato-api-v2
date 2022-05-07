<?php

namespace App\DataTransformer\City;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\City\CityInput;
use App\Entity\City;

class CityInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        $city = new City();
        $city->setName($object->name);
        $city->setLatitude($object->latitude);
        $city->setLongitude($object->longitude);

        return $city;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CityInput::class === $to && $data instanceof City;
    }
}