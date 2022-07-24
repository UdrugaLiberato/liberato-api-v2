<?php

namespace App\DTO\Category;

class CategoryOutput
{
    public function __construct(
        public string  $name,
        public         $questions,
        public ?string $description,
        public string  $createdAt,
        public ?string $deletedAt,
        public         $icon
    )
    {
    }
}