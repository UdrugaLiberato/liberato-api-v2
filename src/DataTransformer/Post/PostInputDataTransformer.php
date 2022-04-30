<?php

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostInput;
use App\Entity\Post;

class PostInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        $post = new Post();
        $post->setTitle($object->title);
        $post->setBody($object->body);
        return $post;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostInput::class === $to && $data instanceof Post;
    }
}