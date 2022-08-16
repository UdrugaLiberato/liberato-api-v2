<?php

declare(strict_types=1);

namespace App\Events\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\NewsArticle;
use App\Message\NewsArticleCloudinaryMessage;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class OptimizeNewsArticleImageEventSubscriber implements EventSubscriberInterface
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
        if (!$entity instanceof NewsArticle || Request::METHOD_POST !== $method) {
            return;
        }

        $image = $event->getRequest()->files->get('image');
        $fileNames = $this->liberatoHelper->transformImage($image, 'news');
        $this->bus->dispatch(new NewsArticleCloudinaryMessage($entity->getId(), $fileNames));
    }
}
