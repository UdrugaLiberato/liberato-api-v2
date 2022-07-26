<?php

declare(strict_types=1);

namespace App\DTO\NewsArticle;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class NewsArticleInput
{
    public string $title;
    public string $url;
    public UploadedFile $image;
}
