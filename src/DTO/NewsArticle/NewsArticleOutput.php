<?php
declare(strict_types=1);

namespace App\DTO\NewsArticle;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsArticleOutput
{
    public function __construct(
        public string       $title,
        public string       $url,
        public UploadedFile $file,
        public string       $createdAt,
        public ?string      $updatedAt,
        public ?string      $deletedAt
    )
    {
    }
}