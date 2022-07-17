<?php

namespace App\DTO\User;

use Symfony\Component\HttpFoundation\File\File;

class UserInput
{
    public string $username;
    public string $email;
    public string $password;
    public $role;
    public $file;
    public $phone;
}