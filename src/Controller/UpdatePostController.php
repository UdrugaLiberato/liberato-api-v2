<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class UpdatePostController extends AbstractController
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private PostRepository $postRepository,
    ) {
    }

    public function __invoke(string $id, Request $request): Post
    {
        $oldPost = $this->postRepository->find($id);

        if ($request->get('title') && $request->get('title') !== $oldPost->getTitle()) {
            $oldPost->setTitle($request->get('title'));
        }

        if ($request->get('body') && $request->get('body') !== $oldPost->getBody()) {
            $oldPost->setBody($request->get('body'));
        }
        $oldPost->setTags(explode(',', $request->get('tags')));
        $fileNames = $this->liberatoHelper->transformImages($request->files->get('images'), 'posts');
        $oldPost->setImages($fileNames);

        $oldPost->setUpdatedAt(new DateTimeImmutable('now'));
        $this->postRepository->update($oldPost);

        return $oldPost;
    }
}
