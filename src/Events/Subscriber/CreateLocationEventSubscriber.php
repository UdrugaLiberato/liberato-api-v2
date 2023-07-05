<?php

declare(strict_types=1);

namespace App\Events\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Location;
use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreateLocationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    #[ArrayShape([KernelEvents::VIEW => "array"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['addUserToEntity', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function addUserToEntity(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$entity instanceof Location || Request::METHOD_POST !== $method) {
            return;
        }

        /** @var null|User $user */
        $user = $this->token->getToken()?->getUser();
        $entity->setUser($user ?? null);
    }
}
