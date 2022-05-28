<?php

declare(strict_types=1);

namespace App\EventListener;


use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $additionalData = [
            'role' => $user->getRoles()[0],
            'username' => $user->getUsername(),
            'timestamp' => time() + 3600,
        ];

        $data = array_merge($data, $additionalData);

        $event->setData($data);
    }
}