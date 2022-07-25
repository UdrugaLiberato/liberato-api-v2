<?php

namespace App\Utils;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LiberatoHelper implements LiberatoHelperInterface
{
    public const BACKEND_URL = "https://dev.udruga-liberato.hr";
    public const BACKEND_URL_IMAGES = "https://dev.udruga-liberato.hr/images/";
    public string $uploadDir;

    public function __construct(public KernelInterface       $kernel,
                                public ValidatorInterface    $validator,
                                public TokenStorageInterface $token
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . "/public/images/";
    }

    public static function convertImagesArrayToOutput(array $images, string $entityName): array
    {
        return array_map(function ($image) use ($entityName) {
            return [
                "path" => $image["path"],
                "mime" => $image["mime"],
                "src" => self::BACKEND_URL_IMAGES . $entityName . $image["path"],
                "title" => $image["title"],
            ];
        }, $images);
    }

    public static function convertImageArrayToOutput(array $image, string $entityName): array
    {
        return [
            "path" => $image["path"],
            "mime" => $image["mime"],
            "src" => self::BACKEND_URL_IMAGES . $entityName . $image["path"],
            "title" => $image["title"],
        ];
    }

    public static function slugify(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);
        return strtolower($string);
    }

    public function transformImage(UploadedFile $file, string $entityName): array
    {
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
            $this->uploadDir . $entityName . "/",
            $newFilename
        );

        return [
            "path" => $newFilename,
            "title" => $file->getClientOriginalName(),
            "mime" => $mime,
        ];
    }

    public function transformImages(array $uploadedFiles, string $entityName): array
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
                    $this->uploadDir . $entityName,
                    $newFilename
                );
                $fileObj = [
                    "path" => $newFilename,
                    "title" => $file->getClientOriginalName(),
                    "mime" => $mime,
                ];
                $fileNames[] = $fileObj;
            }
            return $fileNames;
        }
        return [];
    }
}