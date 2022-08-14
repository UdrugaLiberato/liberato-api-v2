<?php

declare(strict_types=1);

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostInput;
use App\Entity\Post;

class PostInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param PostInput $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Post
    {
        $post = new Post();
        $post->setTitle(trim($object->title));
        $post->setBody($object->body);
        $post->setTags(explode(',', $object->tags));

        return $post;
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Post) {
            return false;
        }

        return Post::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
