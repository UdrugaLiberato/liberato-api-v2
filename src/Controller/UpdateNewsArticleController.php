<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NewsArticle;
use App\Message\NewsArticleCloudinaryMessage;
use App\Repository\NewsArticleRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsController]
class UpdateNewsArticleController
{
    public function __construct(
        private NewsArticleRepository $newsArticleRepository,
        private LiberatoHelperInterface $liberatoHelper,
        private MessageBusInterface $bus
    ) {
    }

    public function __invoke(string $id, Request $request): NewsArticle
    {
        $newsArticleToUpdate = $this->newsArticleRepository->find($id);
        $newsArticleToUpdate->getImage()->map(function (string $imagePath): void {
            $file = $this->liberatoHelper->getImagePath('news/') . $imagePath;
            if (file_exists($file)) {
                unlink($file);
            }
        });

        $fileNames = $this->liberatoHelper->transformImage($request->files->get('image'), 'news');
        $this->bus->dispatch(new NewsArticleCloudinaryMessage($id, $fileNames));

        $newsArticleToUpdate->setTitle($request->request->get('title'));
        $newsArticleToUpdate->setUrl($request->request->get('url'));

        $this->newsArticleRepository->update($newsArticleToUpdate);

        return $newsArticleToUpdate;
    }
}
