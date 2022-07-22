<?php

namespace App\DataTransformer\City;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\City\CityOutput;
use App\Entity\City;

class CityOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        return new CityOutput(
            $object->getName(),
            $object->getLatitude(),
            $object->getLongitude(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CityOutput::class === $to && $data instanceof City;
    }
}