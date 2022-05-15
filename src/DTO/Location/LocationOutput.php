<?php

namespace App\DTO\Location;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\User;

class LocationOutput
{
    public function __construct(
        public string   $name,
        public string   $street,
        public string   $phone,
        public string   $email,
        public bool     $published,
        public bool     $featured,
        public Category $category,
        public City     $city,
        public string   $createdAt,
        public ?User    $user,
        public ?string   $about,
        public ?string  $updatedAt,
        public ?string  $deletedAt,
        public array $images
    )
    {
    }
}