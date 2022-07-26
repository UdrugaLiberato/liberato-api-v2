<?php

declare(strict_types=1);

namespace App\Events\Listener;

use App\Entity\Location;
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

        $user = $this->token->getToken()?->getUser();
        if (!$user) {
            $entity->setUser(null);
        } else {
            $entity->setUser($user);
        }
    }
}
