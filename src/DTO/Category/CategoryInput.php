<?php

namespace App\DTO\Category;

use Symfony\Component\HttpFoundation\File\File;

class CategoryInput
{
    public string $name;
    public $file;
    public ?string $description = null;
}