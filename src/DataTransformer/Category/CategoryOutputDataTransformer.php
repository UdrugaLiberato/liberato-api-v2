<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Category\CategoryOutput;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(public QuestionRepository      $questionRepository,
                                public LiberatoHelperInterface $liberatoHelper)
    {
    }

    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return CategoryOutput
     */
    public function transform($object, string $to, array $context = []): CategoryOutput
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

    /**
     * @param object $object
     * @return ArrayCollection
     */
    private function getQuestionAndAnswerArr(object $object): ArrayCollection
    {
        $qAC = new ArrayCollection();
        $questions = $this->questionRepository->findBy(["category" => $object->getId()]);
        array_map(static function (Question $question) use ($qAC) {
            $qAC->add([
                "id" => $question->getId(),
                "question" => $question->getQuestion()
            ]);
        }, $questions);

        return $qAC;
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CategoryOutput::class === $to && $data instanceof Category;
    }
}