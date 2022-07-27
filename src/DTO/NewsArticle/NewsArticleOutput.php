<?php

declare(strict_types=1);

namespace App\DTO\NewsArticle;

use Doctrine\Common\Collections\ArrayCollection;

class NewsArticleOutput
{
    public function __construct(
        public string $id,
        public string $title,
        public string $url,
        public ArrayCollection $image,
        public string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }
}
