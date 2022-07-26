<?php

declare(strict_types=1);

namespace App\DTO\Category;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryInput
{
    public string $name;
    public UploadedFile $file;
    public string $questions;
    public ?string $description = null;
}
