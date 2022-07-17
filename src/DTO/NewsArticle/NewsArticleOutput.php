<?php
declare(strict_types=1);

namespace App\DTO\NewsArticle;

class NewsArticleOutput
{
    public function __construct(
        public string  $title,
        public string  $url,
        public         $file,
        public string  $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    )
    {
    }
}