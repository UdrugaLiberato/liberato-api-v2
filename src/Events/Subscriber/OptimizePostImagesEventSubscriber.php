<?php

declare(strict_types=1);

namespace App\Events\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Post;
use App\Message\PostCloudinaryMessage;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class OptimizePostImagesEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $bus, private LiberatoHelperInterface $liberatoHelper)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['optimize', EventPriorities::POST_WRITE],
        ];
    }

    public function optimize(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        if (!$entity instanceof Post || Request::METHOD_POST !== $method) {
            return;
        }

        $this->bus->dispatch(new PostCloudinaryMessage($entity->getId(), $entity->getImages()));
    }
}
