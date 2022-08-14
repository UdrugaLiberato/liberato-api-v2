<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PostCloudinaryMessage;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PostCloudinaryMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        public LiberatoHelperInterface $liberatoHelper,
        public EntityManagerInterface $entityManager,
        public PostRepository $postRepository
    ) {
    }

    public function __invoke(PostCloudinaryMessage $message): void
    {
        $post = $this->postRepository->find($message->getId());
        $newImages = $this->liberatoHelper->uploadToCloudinary($message->getImages(), 'posts');
        $post->setImages($newImages);

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
