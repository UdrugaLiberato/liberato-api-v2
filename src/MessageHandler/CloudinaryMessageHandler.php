<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CloudinaryMessage;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloudinaryMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        public LiberatoHelperInterface $liberatoHelper,
        public EntityManagerInterface  $entityManager,
        public PostRepository          $postRepository)
    {
    }

    public
    function __invoke(CloudinaryMessage $message)
    {
        $post = $this->postRepository->find($message->getId());
        $newImages = $this->liberatoHelper->uploadToCloudinary($message->getImages(), "posts");
        $post->setImages($newImages);

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}