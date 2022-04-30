<?php

namespace App\DTO\User;

class UserOutput
{
    public function __construct(
      public string $id,
      public string $username,
      public string $email,
      public string $phone,
    public string $createdAt,
      public ?string $updatedAt,
      public ?string $deletedAt
    ) {
    }
}