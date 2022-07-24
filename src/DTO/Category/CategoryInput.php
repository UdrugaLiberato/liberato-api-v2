<?php

namespace App\DTO\Category;

class CategoryInput
{
    public string $name;
    public $file;
    public array|string $questions;
    public ?string $description = null;
}