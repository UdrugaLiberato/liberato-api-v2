<?php
declare(strict_types=1);

namespace App\DataTransformer\Post;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Post;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PostInputDataTransformer implements DataTransformerInterface
{
    public string $uploadDir;
    private ValidatorInterface $validator;

    public function __construct(public KernelInterface       $kernel,
                                public ValidatorInterface    $validatorI,
                                public TokenStorageInterface $token
    )
    {
        $this->validator = $validatorI;
        $this->uploadDir = $this->kernel->getProjectDir() . "/public/images/posts/";
    }

    public function transform($object, string $to, array $context = [])
    {
        $post = new Post();
        $post->setTitle($object->title);
        $post->setAuthor($this->token?->getToken()?->getUser());
        $post->setBody($object->body);
        $post->setTags(explode(",", $object->tags));
        $fileNames = $this->transformPictures($object->images);
        $post->setImages($fileNames);
        return $post;
    }

    private function transformPictures($uploadedFiles): array
    {
        $fileNames = [];
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $errors = $this->validator->validate($file, new Image());
                if (count($errors) > 0) {
                    throw new ValidationException("Only images can be uploaded!");
                }

                $originalFilename = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugify($originalFilename);
                $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                    (
                        microtime()
                    ) . '.'
                    . $file->guessExtension();
                $file->move(
                    $this->uploadDir,
                    $newFilename
                );
                $fileObj = [
                    "src" => $newFilename,
                    "title" => $file->getClientOriginalName(),
                    "extension" => $file->guessClientExtension()
                ];
                $fileNames[] = $fileObj;
            }
            return $fileNames;
        }
        return [];
    }

    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);

        return strtolower($title);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Post) {
            return false;
        }

        return Post::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
