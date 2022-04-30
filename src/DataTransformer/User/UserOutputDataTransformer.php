<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserOutput;
use App\Entity\User;

class UserOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): object
    {
        return new UserOutput(
          $object->getId(),
          $object->getUsername(),
          $object->getEmail(),
          $object->getCreatedAt()->format('Y-m-d H:i:s'),
          $object->getUpdatedAt()?->format("Y-m-d H:i:s"),
          $object->getDeletedAt()?->format("Y-m-d H:i:s")
        );
    }
    
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserOutput::class === $to && $data instanceof User;
    }
}