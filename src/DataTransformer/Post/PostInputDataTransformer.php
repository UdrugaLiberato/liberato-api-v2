<?php

declare(strict_types=1);

namespace App\DataTransformer\Post;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Entity\Post;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        public LiberatoHelperInterface $liberatoHelper,
        public TokenStorageInterface $token
    ) {
    }

    /**
     * @param object       $object
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Post
    {
        $post = new Post();
        $post->setTitle(trim($object->title));
        $post->setAuthor($this->token->getToken()?->getUser());
        $post->setBody($object->body);
        $post->setTags(explode(',', $object->tags));
        $fileNames = $this->liberatoHelper->transformImages($object->images, 'posts');
        $post->setImages($fileNames);

        return $post;
    }

    /**
     * @param object       $data
     * @param array<mixed> $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Post) {
            return false;
        }

        return Post::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
