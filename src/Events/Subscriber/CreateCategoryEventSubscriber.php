<?php

declare(strict_types=1);

namespace App\Events\Subscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreateCategoryEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        public CategoryRepository $categoryRepository,
        public QuestionRepository $questionRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['addQuestionsToCategory', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function addQuestionsToCategory(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entity instanceof Category || Request::METHOD_POST !== $method) {
            return;
        }


    }
}
