<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\NewsArticle;
use App\Repository\NewsArticleRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdateNewsArticleController
{
    public function __construct(
        private NewsArticleRepository   $newsArticleRepository,
        private LiberatoHelperInterface $liberatoHelper
    )
    {
    }

    public function __invoke(string $id, Request $request): NewsArticle
    {
        // ...
    }

}