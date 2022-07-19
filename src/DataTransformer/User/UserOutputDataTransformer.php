<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserOutput;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class UserOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): object
    {
        $posts = $this->getPostsFromUser($object->getPosts());
        return new UserOutput(
            $object->getId(),
            $object->getName(),
            $object->getEmail(),
            $object->getRoles()[0],
            null === $object->getPhone() ? null : $object->getPhone(),
            null === $object->getFilePath() ? null : $object->getFilePath(),
            $posts,
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }

    private function getPostsFromUser($posts): array|ArrayCollection
    {
        if (count($posts->getValues()) === 0) return [];

        return $posts->map(function ($post) {
            return [
                "id" => $post->getId(),
                "title" => $post->getTitle(),
                "slug" => $this->slugify($post->getTitle()),
                "body" => $post->getBody(),
                "tags" => $post->getTags(),
                "images" => $post->getImages(),
                "created_at" => $post->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        });
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
        return UserOutput::class === $to && $data instanceof User;
    }
}