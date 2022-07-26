<?php

declare(strict_types=1);

namespace App\Events\Listener;

use App\Entity\Post;
use App\Entity\User;
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

        /** @var null|User $user */
        $user = $this->token->getToken()?->getUser();
        if (!$user) {
            throw new UserNotFoundException();
        }
        $entity->setAuthor($user);
    }
}
