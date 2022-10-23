<?php

declare(strict_types=1);

namespace App\DataTransformer\Question;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Question\QuestionInput;
use App\Entity\Question;

class QuestionInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param QuestionInput $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Question
    {
        $question = new Question();
        $question->setQuestion($object->question);
        $question->setCategory($object->category);

        return $question;
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Question) {
            return false;
        }

        return Question::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
