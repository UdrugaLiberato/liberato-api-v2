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
    public $qa;
    public $latitude;
    public $longitude;

    /** @var array<UploadedFile> */
    public array $images;
    public string $name;
    public string $street;
    public ?string $email;
    public ?string $phone;
    public ?string $about;
    public mixed $published;
    public mixed $featured;

    public function __construct()
    {
        $this->images = $this->images ?? [];
        $this->phone = $this->phone ?? null;
        $this->email = $this->email ?? null;
        $this->about = $this->about ?? null;
    }
}
