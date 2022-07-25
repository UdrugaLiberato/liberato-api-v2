<?php

namespace App\DataTransformer\Category;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\Category;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CategoryInputDataTransformer implements DataTransformerInterface
{
    public string $uploadDir;

    public function __construct(public KernelInterface    $kernel,
                                public QuestionRepository $questionRepository,
                                public ValidatorInterface $validator,
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . "/public/images/category/";
    }

    public function transform($object, string $to, array $context = []): object
    {
        $errors = $this->validator->validate($object->file, new Image());
        if (count($errors) > 0) {
            throw new ValidationException("Only images can be uploaded!");
        }
        $mime = $object->file->getMimeType();
        $originalFilename = pathinfo(
            $object->file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        // this is needed to safely include the file name as part of the URL
        $safeFilename = LiberatoHelper::slugify($originalFilename);
        $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
            (
                microtime()
            ) . '.'
            . $object->file->guessExtension();
        $object->file->move(
            $this->uploadDir,
            $newFilename
        );

        $F = file_get_contents($this->uploadDir . $newFilename);
        $base64 = base64_encode($F);
        $blob = 'data:' . $mime . ';base64,' . $base64;
        $fileObj = [
            "path" => $newFilename,
            "title" => $object->file->getClientOriginalName(),
            "mime" => $mime,
            "src" => $blob,
        ];
        $category = new Category();
        $category->setName($object->name);
        $category->setIcon($fileObj);
        $category->setDescription($object->description);

        return $category;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Category) {
            return false;
        }

        return Category::class === $to && null !== ($context['input']['class'] ?? null);
    }

}