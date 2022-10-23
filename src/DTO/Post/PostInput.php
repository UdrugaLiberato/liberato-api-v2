<?php

declare(strict_types=1);

namespace App\DTO\Post;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


class PostInput
{
    #[Assert\Length(min: 10, minMessage: 'Title must be at least {{ limit }} characters long!'), ]
    public string $title;
    #[Assert\Length(min: 125, minMessage: 'Title must be at least {{ limit }} characters long!'), ]
    public string $body;
    public string $tags;

    /** @var array<UploadedFile> */
    public array $images;
}
