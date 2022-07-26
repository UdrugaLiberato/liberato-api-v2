<?php

declare(strict_types=1);

namespace App\DataTransformer\NewsArticle;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\NewsArticle\NewsArticleOutput;
use App\Entity\NewsArticle;

class NewsArticleOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): NewsArticleOutput
    {
        return new NewsArticleOutput(
            $object->getTitle(),
            $object->getUrl(),
            null === $object->getFilePath() ? null : $object->getFilePath(),
            $object->getCreatedAt()->format('Y-m-d H:i:s'),
            $object->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $object->getDeletedAt()?->format('Y-m-d H:i:s')
        );
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return NewsArticleOutput::class === $to && $data instanceof NewsArticle;
    }
}
