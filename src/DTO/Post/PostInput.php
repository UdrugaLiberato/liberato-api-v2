<?php

declare(strict_types=1);

namespace App\DTO\Post;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostInput
{
    public string $title;
    public string $body;
    public string $tags;

    /** @var array<UploadedFile> */
    public array $images;
}
