<?php

namespace App\DTO\Post;

use App\Entity\User;

final class PostOutput
{
    public function __construct(
      public string $author,
      public string $id,
      public string $title,
      public string $body,
      public string $slug,
      public array $tags,
      public  $images,
      public string $createdAt,
      public ?string $updatedAt,
      public ?string $deletedAt
    ) {
    }
}