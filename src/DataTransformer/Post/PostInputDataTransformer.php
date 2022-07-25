<?php
declare(strict_types=1);

namespace App\DataTransformer\Post;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Post;
use App\Utils\LiberatoHelper;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Image;


class PostInputDataTransformer implements DataTransformerInterface
{
    public function __construct(public LiberatoHelperInterface $liberatoHelper, public TokenStorageInterface $token
    )
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $post = new Post();
        $post->setTitle(trim($object->title));
        $post->setAuthor($this->token?->getToken()?->getUser());
        $post->setBody($object->body);
        $post->setTags(explode(",", $object->tags));
        $fileNames = $this->liberatoHelper->transformImages($object->images, "posts");
        $post->setImages($fileNames);
        return $post;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Post) {
            return false;
        }

        return Post::class === $to && null !== ($context['input']['class'] ?? null);
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
                $mime = $file->getMimeType();
                $originalFilename = pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                // this is needed to safely include the file name as part of the URL
                $safeFilename = LiberatoHelper::slugify($originalFilename);
                $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                    (
                        microtime()
                    ) . '.'
                    . $file->guessExtension();
                $file->move(
                    $this->uploadDir,
                    $newFilename
                );

                $F = file_get_contents($this->uploadDir . $newFilename);
                $base64 = base64_encode($F);
                $blob = 'data:' . $mime . ';base64,' . $base64;
                $fileObj = [
                    "path" => $newFilename,
                    "title" => $file->getClientOriginalName(),
                    "mime" => $mime,
                    "src" => $blob,
                ];
                $fileNames[] = $fileObj;
            }
            return $fileNames;
        }
        return [];
    }
}
