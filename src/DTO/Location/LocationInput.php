<?php

declare(strict_types=1);

namespace App\DTO\Location;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocationInput
{
    public string $category;
    public string $user;
    public string $city;
    public string $answers;

    /** @var array<UploadedFile> */
    public array $images;
    public string $name;
    public string $street;
    public string $email;
    public string $phone;
    public string $about;
    public bool $published;
    public bool $featured;
}
