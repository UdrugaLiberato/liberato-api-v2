<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\PostInput;
use App\Entity\Post;

class PostInputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = [])
    {
        return new PostInput();
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostInput::class === $to && $data instanceof Post;
    }
}