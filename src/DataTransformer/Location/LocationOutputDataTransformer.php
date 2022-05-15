<?php

namespace App\DataTransformer\Location;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Location\LocationOutput;
use App\Entity\Location;

class LocationOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        return new LocationOutput(
            $object->getName(),
            $object->getStreet(),
            $object->getPhone(),
            $object->getEmail(),
            $object->getPublished(),
            $object->getFeatured(),
            $object->getCategory(),
            $object->getCity(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUser(),
            $object->getAbout(),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s'),
            $object->getImages()
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return LocationOutput::class === $to && $data instanceof Location;

    }
}