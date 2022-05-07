<?php

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\User;

class UserInputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): object
    {
        $user = new User();
        $user->setUsername($object->username);
        $user->setPassword($object->password);
        $user->setEmail($object->email);
        $user->setPhone($object->phone);
        $user->setPhone($object->phone);
        $user->setFile($object->file);
        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}