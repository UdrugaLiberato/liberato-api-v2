<?php
declare(strict_types=1);

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class UpdatePostController extends AbstractController
{
    private string $uploadDir;

    public function __construct(
        private KernelInterface    $kernel,
        private PostRepository     $postRepository,
        private ValidatorInterface $validator
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . "/public/images/posts/";
    }

    public function __invoke(string $id, Request $request): Post
    {
        $oldPost = $this->postRepository->find($id);

        if ($request->get("title") && $request->get("title") !== $oldPost->getTitle()) {
            $oldPost->setTitle($request->get("title"));
        }

        if ($request->get("body") && $request->get("body") !== $oldPost->getBody()) {
            $oldPost->setBody($request->get("body"));
        }
        $oldPost->setTags(explode(",", $request->get("tags")));;
        $fileNames = $this->transformPictures($request->files->get("images"));
        $oldPost->setImages([]);
        $oldPost->setImages($fileNames);

        $oldPost->setUpdatedAt(new \DateTimeImmutable("now"));
        $this->postRepository->update($oldPost);

        return $oldPost;
    }

    private function transformPictures($uploadedFiles): array
    {
        $fileNames = [];
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $mime = $file->getMimeType();
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

                $F = file_get_contents($this->uploadDir . $newFilename);
                $base64 = base64_encode($F);
                $blob = 'data:'.$mime.';base64,'.$base64;
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

    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);

        return strtolower($title);
    }
}