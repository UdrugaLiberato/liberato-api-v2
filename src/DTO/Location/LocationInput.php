<?php

declare(strict_types=1);

namespace App\DTO\Location;

use Doctrine\Common\Collections\ArrayCollection;

class LocationInput
{
    public string $category;
    public string $user;
    public string $city;
    public ArrayCollection $answers;
    public ArrayCollection $images;
    public string $name;
    public string $street;
    public string $email;
    public string $phone;
    public string $about;
    public bool $published;
    public bool $featured;
}
