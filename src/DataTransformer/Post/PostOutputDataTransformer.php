<?php

namespace App\DataTransformer\Post;

    use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Post\PostOutput;
use App\Entity\Post;

class PostOutputDataTransformer implements DataTransformerInterface
{

    public function transform($object, string $to, array $context = []): object
    {
        return new PostOutput(
            $object->getAuthor(),
            $object->getId(),
            $object->getTitle(),
            $object->getBody(),
            $this->slugify($object->getTitle()),
            $object->getTags(),
            $object->getImages(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }


    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);
        return strtolower($title);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return PostOutput::class === $to && $data instanceof Post;
    }
}