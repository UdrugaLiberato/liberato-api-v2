<?php

namespace App\Events\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CreatePostEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['addUserToEntity', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function addUserToEntity(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$entity instanceof Post || Request::METHOD_POST !== $method) {
            return;
        }
        /** @var null|User $user */
        $user = $this->token->getToken()?->getUser();
        $entity->setAuthor($user ?? null);
    }
}