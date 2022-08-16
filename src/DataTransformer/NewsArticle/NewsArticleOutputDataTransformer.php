<?php

declare(strict_types=1);

namespace App\DataTransformer\NewsArticle;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\NewsArticle\NewsArticleOutput;
use App\Entity\NewsArticle;
use App\Utils\LiberatoHelperInterface;

class NewsArticleOutputDataTransformer implements DataTransformerInterface
{
    public function __construct(private LiberatoHelperInterface $liberatoHelper)
    {
    }

    /**
     * @param NewsArticle  $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): NewsArticleOutput
    {
        return new NewsArticleOutput(
            '/api/news_articles/' . $object->getId(),
            $object->getTitle(),
            $object->getUrl(),
            $this->liberatoHelper->convertImageArrayToOutput($object->getImage(), 'news/'),
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
