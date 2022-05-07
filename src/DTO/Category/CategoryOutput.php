<?php

namespace App\DTO\Category;

class CategoryOutput
{
    public function __construct(
        public string $name,
        public string $icon,
        public ?string $description,
        public ?string $deletedAt
    )
    {
    }
}