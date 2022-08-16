<?php

declare(strict_types=1);

namespace App\DataTransformer\NewsArticle;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\NewsArticle\NewsArticleInput;
use App\Entity\NewsArticle;
use App\Utils\LiberatoHelperInterface;

class NewsArticleInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private LiberatoHelperInterface $liberatoHelper)
    {
    }

    /**
     * @param NewsArticleInput $object
     * @param array<mixed>     $context
     */
    public function transform($object, string $to, array $context = []): NewsArticle
    {
        $article = new NewsArticle();
        $article->setTitle($object->title);
        $article->setUrl($object->url);

        return $article;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof NewsArticle) {
            return false;
        }

        return NewsArticle::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
