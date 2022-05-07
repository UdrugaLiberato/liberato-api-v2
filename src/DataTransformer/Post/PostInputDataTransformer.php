<?php

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Post;

class PostInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        $post = new Post();
        $post->setTitle($object->title);
        $post->setBody($object->body);
        $post->setTags($object->tags);

        return $post;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Post) {
            return false;
        }

        return Post::class === $to && null !== ($context['input']['class'] ?? null);
    }
}