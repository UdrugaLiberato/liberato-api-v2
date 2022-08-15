<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Message\PostCloudinaryMessage;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsController]
class UpdatePostController extends AbstractController
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private PostRepository          $postRepository,
        private MessageBusInterface     $bus
    )
    {
    }

    public function __invoke(string $id, Request $request): Post
    {
        $postToUpdate = $this->postRepository->find($id);

        $postToUpdate->getImages()->map(function (array $image): void {
            $file = $this->liberatoHelper->getImagePath('posts/') . $image['path'];
            if (file_exists($file)) {
                unlink($file);
            }
        });
        if ($request->get('title') && $request->get('title') !== $postToUpdate->getTitle()) {
            $postToUpdate->setTitle($request->get('title'));
        }

        if ($request->get('body') && $request->get('body') !== $postToUpdate->getBody()) {
            $postToUpdate->setBody($request->get('body'));
        }
        $postToUpdate->setTags(explode(',', $request->get('tags')));
        $fileNames = $this->liberatoHelper->transformImages($request->files->get('images'), 'posts');
        $this->bus->dispatch(new PostCloudinaryMessage($id, $fileNames));
        $this->postRepository->update($postToUpdate);
        return $postToUpdate;
    }
}
