<?php

namespace App\DataTransformer\Media;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Media\MediaOutput;
use App\Entity\MediaObject;


class MediaOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        return new MediaOutput($object->getId());
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return MediaOutput::class === $to && $data instanceof MediaObject;
    }
}