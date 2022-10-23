<?php

declare(strict_types=1);

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\Category\CategoryOutput;
use App\Entity\Category;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\Common\Collections\ArrayCollection;
use function count;

class CategoryOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        public QuestionRepository      $questionRepository,
        public LiberatoHelperInterface $liberatoHelper
    )
    {
    }

    /**
     * @param Category $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): CategoryOutput
    {
        return new CategoryOutput(
            $object->getId(),
            $object->getName(),
            $this->getQuestionAndAnswerArr($object->getId()),
            $object->getDescription() ?? null,
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s"') ?? null,
            $this->liberatoHelper->convertImageArrayToOutput($object->getIcon(), 'category/'),
            count($object->getLocations()->toArray())
        );
    }

    private function getQuestionAndAnswerArr(string $id): ArrayCollection
    {
        $qAC = new ArrayCollection();
        $questions = $this->questionRepository->findBy(['category' => $id]);
        array_map(static function (Question $question) use ($qAC): void {
            $qAC->add([
                'id' => $question->getId(),
                'question' => $question->getQuestion(),
            ]);
        }, $questions);

        return $qAC;
    }

    /**
     * @param object $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CategoryOutput::class === $to && $data instanceof Category;
    }
}
