<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NewsArticleCloudinaryMessage;
use App\Repository\NewsArticleRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NewsArticleCloudinaryMessageHandler
{
    public function __construct(
        public LiberatoHelperInterface $liberatoHelper,
        public EntityManagerInterface $entityManager,
        public NewsArticleRepository $newsArticleRepository,
    ) {
    }

    public function __invoke(NewsArticleCloudinaryMessage $message): void
    {
        $newsArticle = $this->newsArticleRepository->find($message->getId());
        $images = $message->getImages();
        $newImage = $this->liberatoHelper->uploadImageToCloudinary($images, 'news');
        $newsArticle->setImage($newImage);

        $this->entityManager->persist($newsArticle);
        $this->entityManager->flush();
    }
}
