<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Category\CategoryOutput;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelperInterface;

class CategoryOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(public QuestionRepository      $questionRepository,
                                public LiberatoHelperInterface $liberatoHelper)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        return new CategoryOutput(
            $object->getName(),
            $this->getQuestionAndAnswerArr($object),
            null === $object->getDescription() ? null : $object->getDescription(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            null === $object->getDeletedAt() ? null : $object->getDeletedAt()->format('Y-m-d H:i:s"'),
            $this->liberatoHelper->convertImageArrayToOutput($object->getIcon(), "category"),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CategoryOutput::class === $to && $data instanceof Category;
    }

    private function getQuestionAndAnswerArr(object $object): array
    {
        $questions = $this->questionRepository->findBy(["category" => $object->getId()]);
        return array_map(static function (Question $question) {
            return [
                "id" => $question->getId(),
                "question" => $question->getQuestion()
            ];
        }, $questions);
    }
}