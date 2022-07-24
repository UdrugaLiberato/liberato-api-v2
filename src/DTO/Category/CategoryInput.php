<?php

namespace App\DTO\Category;

class CategoryInput
{
    public string $name;
    public $file;
    public $questions;
    public ?string $description = null;
}