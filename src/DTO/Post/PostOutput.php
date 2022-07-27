<?php

declare(strict_types=1);

namespace App\DTO\Post;

use Doctrine\Common\Collections\ArrayCollection;

final class PostOutput
{
    /**
     * @param array<string> $tags
     */
    public function __construct(
        public string $id,
        public string $author,
        public string $title,
        public string $body,
        public string $slug,
        public array $tags,
        public ArrayCollection $images,
        public string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }
}
