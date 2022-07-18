<?php

namespace App\DTO\User;

use Doctrine\Common\Collections\Collection;

class UserOutput
{
    public function __construct(
        public string  $id,
        public string  $username,
        public string  $email,
        public string  $role,
        public ?string $phone,
        public ?string $avatar,
        public Collection $posts,
        public string  $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    )
    {
    }
}