<?php

declare(strict_types=1);

namespace App\DataTransformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\User\UserInput;
use App\Entity\User;
use App\Utils\LiberatoHelperInterface;

class UserInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private LiberatoHelperInterface $liberatoHelper)
    {
    }

    /**
     * @param UserInput    $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): User
    {
        $user = new User();
        $avatar = $this->liberatoHelper->transformImage($object->file, 'avatar');
        $user->setUsername($object->username);
        $user->setPassword($object->password);
        $user->setEmail($object->email);
        $user->setPhone($object->phone ?? null);
        $user->setAvatar($avatar);

        return $user;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
