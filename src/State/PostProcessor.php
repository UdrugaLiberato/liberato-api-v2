<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Post;
use App\Repository\PostRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostProcessor implements ProcessorInterface
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private PostRepository          $postRepository,
        private TokenStorageInterface   $token
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $post = new Post();
        $post->setTitle($data->title);
        $post->setBody($data->body);
        $post->setTags(explode(',', $data->tags));
        $fileNames = $this->liberatoHelper->transformImages($data->images, 'posts');
        $post->setImages($fileNames);
        $post->setSlug($this->liberatoHelper->slugify($data->title));
        $post->setAuthor($this->token->getToken()->getUser());
        $this->postRepository->add($post);

        return $post;
    }
}
