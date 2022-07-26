<?php

declare(strict_types=1);

namespace App\Events\Listener;

use App\Entity\Location;
use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateLocationEventListener
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Location) {
            return;
        }

        /** @var null|User $user */
        $user = $this->token->getToken()?->getUser();
        if (!$user) {
            $entity->setUser(null);
        } else {
            $entity->setUser($user);
        }
    }
}
