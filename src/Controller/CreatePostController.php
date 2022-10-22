<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsController]
class CreatePostController extends AbstractController
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private PostRepository          $postRepository,
        private MessageBusInterface     $bus
    )
    {
    }

    public function __invoke(Request $data): Post
    {
        $post = new Post();
        $post->setTitle($data->get('title'));
        $post->setBody($data->get('body'));
        $post->setTags(explode(',', $data->get('tags')));
        $fileNames = $this->liberatoHelper->transformImages($data->files->get('images'), 'posts');
        $post->setImages($fileNames);
        return $post;
    }
}