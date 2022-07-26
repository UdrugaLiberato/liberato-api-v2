<?php

declare(strict_types=1);

namespace App\Events;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreateCategoryEventListener implements EventSubscriberInterface
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

    public function addQuestionsToCategory(ViewEvent $event): void
    {
        if (Request::METHOD_POST === $event->getRequest()->getMethod() && $event->getRequest()->get('questions')) {
            $questions = explode(',', $event->getRequest()->get('questions'));
            $category = $this->categoryRepository->findOneBy(['name' => $event->getRequest()->get('name')]);

            foreach ($questions as $question) {
                $addQuestion = new Question();
                $addQuestion->setCategory($category);
                $addQuestion->setQuestion($question);
                $this->questionRepository->add($addQuestion);
                $category->addQuestion($addQuestion);
            }
        }
    }
}
