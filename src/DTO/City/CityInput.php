<?php

declare(strict_types=1);

namespace App\DTO\City;

use Symfony\Component\Validator\Constraints as Assert;

final class CityInput
{
    #[Assert\NotBlank]
    public string $name;
}
