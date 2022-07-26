<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserOutput;
use App\Entity\User;
use App\Utils\LiberatoHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class UserOutputDataTransformer implements DataTransformerInterface
{

    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return UserOutput
     */
    public function transform($object, string $to, array $context = []): UserOutput
    {
        $posts = $this->getPostsFromUser($object->getPosts());
        return new UserOutput(
            $object->getId(),
            $object->getName(),
            $object->getEmail(),
            $object->getRoles()[0],
            $object->getPhone() ?? null,
            $object->getAvatar() ?? null,
            $posts,
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
            $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }

    private function getPostsFromUser(Collection $posts): ArrayCollection
    {
        if (count($posts->getValues()) === 0) return new ArrayCollection();

        $filteredPostOutput = $posts->map(function ($post) {
            return [
                "id" => $post->getId(),
                "title" => $post->getTitle(),
                "slug" => LiberatoHelper::slugify($post->getTitle()),
                "body" => $post->getBody(),
                "tags" => $post->getTags(),
                "images" => $post->getImages(),
                "created_at" => $post->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        });

        return new ArrayCollection($filteredPostOutput->toArray());
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserOutput::class === $to && $data instanceof User;
    }
}