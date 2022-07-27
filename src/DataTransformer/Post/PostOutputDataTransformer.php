<?php

declare(strict_types=1);

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostOutput;
use App\Entity\Post;
use App\Utils\LiberatoHelper;

class PostOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Post         $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): PostOutput
    {
        return new PostOutput(
            $object->getId(),
            $object->getAuthor()->getName(),
            $object->getTitle(),
            $object->getBody(),
            LiberatoHelper::slugify($object->getTitle()),
            $object->getTags(),
            LiberatoHelper::convertImagesArrayToOutput($object->getImages(), 'posts/'),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s')
        );
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostOutput::class === $to && $data instanceof Post;
    }
}
