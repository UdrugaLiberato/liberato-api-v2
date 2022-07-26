<?php
declare(strict_types=1);

namespace App\DataTransformer\NewsArticle;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\NewsArticle;
use App\Utils\LiberatoHelperInterface;

class NewsArticleInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private LiberatoHelperInterface $liberatoHelper)
    {
    }

    /**
     * @param object $object
     * @param string $to
     * @param array<mixed> $context
     * @return NewsArticle
     */
    public function transform($object, string $to, array $context = []): NewsArticle
    {
        $image = $this->liberatoHelper->transformImage($object->file, "news");
        $article = new NewsArticle();
        $article->setTitle($object->title);
        $article->setUrl($object->url);
        $article->setImage($image);
        return $article;
    }

    /**
     * @param object $data
     * @param string $to
     * @param array<mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof NewsArticle) {
            return false;
        }

        return NewsArticle::class === $to && null !== ($context['input']['class'] ?? null);
    }
}