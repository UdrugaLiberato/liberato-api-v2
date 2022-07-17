<?php
declare(strict_types=1);

namespace App\DataTransformer\NewsArticle;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\NewsArticle;
use Symfony\Component\HttpKernel\KernelInterface;

class NewsArticleInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function transform($object, string $to, array $context = []): object
    {
        $originalFilename = pathinfo(
            $object->file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugify($originalFilename);
        $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
            (
                microtime()
            ) . '.'
            . $object->file->guessExtension();

        $object->file->move($this->kernel->getProjectDir() . '/public/media/news/', $newFilename);
        $article = new NewsArticle();
        $article->setTitle($object->title);
        $article->setUrl($object->url);
        $article->setFilePath('/media/news/' . $newFilename);
        return $article;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof NewsArticle) {
            return false;
        }

        return NewsArticle::class === $to && null !== ($context['input']['class'] ?? null);
    }

    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);

        return strtolower($title);
    }
}