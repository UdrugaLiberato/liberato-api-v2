<?php

declare(strict_types=1);

namespace App\DTO\Category;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryInput
{
    public string $name;
    public ?UploadedFile $file;
    public string $questions;
    public ?string $description;

    public function __construct()
    {
        $this->file = $this->file ?? null;
        $this->description = $this->description ?? null;
    }
}
