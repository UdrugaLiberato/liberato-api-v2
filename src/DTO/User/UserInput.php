<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserInput
{
    #[Assert\Length(min: 4, minMessage: 'Username must be at least {{ limit }} characters long!')]
    public string $username;
    #[Assert\Email]
    public string $email;
    #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters long!')]
    public string $password;
    public string $createEmail;
    public string $role;
    public ?UploadedFile $file;
    public ?string $phone;

    public function __construct()
    {
        $this->file = $this->file ?? null;
    }
}
