<?php

declare(strict_types=1);

namespace App\Events\Listener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $additionalData = [
            'id' => $user->getId(),
            'role' => $user->getRoles()[0],
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar(),
            'timestamp' => time() + 3600,
        ];

        $data = array_merge($data, $additionalData);

        $event->setData($data);
    }
}
