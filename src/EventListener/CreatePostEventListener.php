<?php

namespace App\EventListener;

use App\Entity\Post;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;


class CreatePostEventListener
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Post) {
            return;
        }

        $user = $this->token?->getToken()?->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        } else {
            $entity->setAuthor($user);
        }
    }
}