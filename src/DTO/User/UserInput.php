<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserInput
{
    public string $username;
    public string $email;
    public string $password;
    public string $role;
    public ?UploadedFile $file;
    public ?string $phone;
}
