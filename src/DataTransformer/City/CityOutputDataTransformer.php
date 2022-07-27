<?php

declare(strict_types=1);

namespace App\DataTransformer\City;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\City\CityOutput;
use App\Entity\City;

class CityOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param City $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): CityOutput
    {
        return new CityOutput(
            $object->getId(),
            $object->getName(),
            $object->getLatitude(),
            $object->getLongitude(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            \count($object->getLocations()->toArray())
        );
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CityOutput::class === $to && $data instanceof City;
    }
}
