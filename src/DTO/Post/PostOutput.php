<?php

namespace App\DTO\Post;

use Doctrine\Common\Collections\ArrayCollection;

final class PostOutput
{

    /**
     * @param array<string> $tags
     */
    public function __construct(
        public string          $author,
        public string          $id,
        public string          $title,
        public string          $body,
        public string          $slug,
        public array           $tags,
        public ArrayCollection $images,
        public string          $createdAt,
        public ?string         $updatedAt,
        public ?string         $deletedAt
    )
    {
    }
}