<?php

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostOutput;
use App\Entity\Post;

class PostOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new PostOutput(
            $object->getAuthor(),
            $object->getId(),
            $object->getBody(),
            $object->getTitle(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostOutput::class === $to && $data instanceof Post;
    }
}