<?php

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostOutput;
use App\Entity\Post;
use App\Utils\LiberatoHelper;

class PostOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return PostOutput
     */
    public function transform($object, string $to, array $context = []): PostOutput
    {
        return new PostOutput(
            $object->getAuthor()->getName(),
            $object->getId(),
            $object->getTitle(),
            $object->getBody(),
            LiberatoHelper::slugify($object->getTitle()),
            $object->getTags(),
            LiberatoHelper::convertImagesArrayToOutput($object->getImages(), "posts/"),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostOutput::class === $to && $data instanceof Post;
    }
}