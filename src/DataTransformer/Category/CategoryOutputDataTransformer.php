<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Category\CategoryOutput;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\QuestionRepository;

class CategoryOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(public QuestionRepository $questionRepository)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $questions = $this->questionRepository->findBy(["category" => $object->getId()]);
        $qA = array_map(static function (Question $question) {
            return [
                "id" => $question->getId(),
                "question" => $question->getQuestion()
            ];
        }, $questions);

        return new CategoryOutput(
            $object->getName(),
            $qA,
            null === $object->getDescription() ? null : $object->getDescription(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            null === $object->getDeletedAt() ? null : $object->getDeletedAt()->format('Y-m-d H:i:s"'),
            $object->getIcon(),
        );
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CategoryOutput::class === $to && $data instanceof Category;
    }
}