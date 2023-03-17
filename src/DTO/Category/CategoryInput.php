<?php

declare(strict_types=1);

namespace App\DTO\Category;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CategoryInput
{
    public $name;
    public ?UploadedFile $file;
    public $questions;
    public $description;

    public function __construct()
    {
        $this->file = $this->file ?? null;
        $this->description = $this->description ?? null;
    }
}
