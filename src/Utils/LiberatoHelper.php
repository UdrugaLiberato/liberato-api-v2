<?php

declare(strict_types=1);

namespace App\Utils;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use Cloudinary\Cloudinary;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LiberatoHelper implements LiberatoHelperInterface
{
    public const BACKEND_URL = 'https://dev.udruga-liberato.hr';
    public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
    public static Cloudinary $cloudinary;
    public string $uploadDir;

    public function __construct(
        public KernelInterface $kernel,
        public ValidatorInterface $validator,
        public TokenStorageInterface $token,
        private string $cloudinaryApiKey
    ) {
        $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
        self::$cloudinary = new Cloudinary($this->cloudinaryApiKey);
    }

    public static function convertImagesArrayToOutput(ArrayCollection $images, string $entityName): ArrayCollection
    {
        return $images->map(static function ($image) use ($entityName) {
            return [
                'path' => $image['path'],
                'mime' => $image['mime'],
                'src_full' => self::BACKEND_URL_IMAGES . $entityName . $image['path'],
                'title' => $image['title'],
                'src' => $image['optimized_image'],
            ];
        });
    }

    public static function convertImageArrayToOutput(ArrayCollection $image, string $entityName): ArrayCollection
    {
        return new ArrayCollection([
            'path' => $image['path'],
            'mime' => $image['mime'],
            'src_full' => self::BACKEND_URL_IMAGES . $entityName . $image['path'],
            'title' => $image['title'],
            'src' => $image['optimized_image'],
        ]);
    }

    public function transformImage(?UploadedFile $file, string $entityName): ArrayCollection
    {
        if (null === $file) {
            return new ArrayCollection();
        }
        $errors = $this->validator->validate($file, new Image());
        if (\count($errors) > 0) {
            throw new ValidationException('Only images can be uploaded!');
        }
        $mime = $file->getMimeType();
        $originalFilename = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        // this is needed to safely include the file name as part of the URL
        $safeFilename = self::slugify($originalFilename);
        $newFilename = date('Y-m-d') . '_' . $safeFilename . md5(
            microtime()
        ) . '.'
            . $file->guessExtension();
        $file->move(
            $this->uploadDir . $entityName . '/',
            $newFilename
        );

        return new ArrayCollection([
            'path' => $newFilename,
            'title' => $file->getClientOriginalName(),
            'mime' => $mime,
        ]);
    }

    public static function slugify(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', (string) $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);

        return mb_strtolower($string);
    }

    public function transformImages(array $uploadedFiles, string $entityName): ArrayCollection
    {
        $fileNames = new ArrayCollection();
        foreach ($uploadedFiles as $file) {
            $mime = $file->getMimeType();
            if ('text/html' === $mime) {
                continue;
            }
            $originalFilename = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            // this is needed to safely include the file name as part of the URL
            $safeFilename = self::slugify($originalFilename);
            $newFilename = date('Y-m-d') . '_' . $safeFilename . md5(
                microtime()
            ) . '.'
                . $file->guessExtension();
            $file->move(
                $this->uploadDir . $entityName,
                $newFilename
            );
            $fileObj = [
                'path' => $newFilename,
                'title' => $file->getClientOriginalName(),
                'mime' => $mime,
            ];
            $fileNames->add($fileObj);
        }

        return $fileNames;
    }

    public function uploadToCloudinary(ArrayCollection $images, string $entityName): ArrayCollection
    {
        $uploadDir = $this->uploadDir . $entityName;
        $newImages = new ArrayCollection();
        foreach ($images as $image) {
            $fullImagePath = $uploadDir . '/' . $image['path'];
            $file = new SymfonyFile($fullImagePath);

            if ('image' !== explode('/', $file->getMimeType())[0]) {
                throw new ValidationException('This is not image!');
            }

            $r = self::$cloudinary->uploadApi()->upload($fullImagePath, [
                'width' => 600, 'height' => 600,
                'crop' => 'fill',
                'gravity' => 'center',
                'quality' => 'auto',
                'fetch_format' => 'jpg',
                'folder' => $entityName,
            ]);
            $fileObj = [
                'path' => $image['path'],
                'title' => $image['title'],
                'mime' => $image['mime'],
                'optimized_image' => $r->getArrayCopy()['secure_url'],
            ];
            $newImages->add($fileObj);
        }

        return $newImages;
    }

    public function getImagePath(string $subdirectoryWithSlash): string
    {
        return $this->kernel->getProjectDir() . '/public/images/' . $subdirectoryWithSlash;
    }

    public function transformFiles(array $files, string $entityName): ArrayCollection
    {
        $fileNames = new ArrayCollection();
        foreach ($files as $file) {
            $errors = $this->validator->validate($file, new File());
            if (\count($errors) > 0) {
                throw new ValidationException('Only files can be uploaded!');
            }
            $mime = $file->getMimeType();
            $originalFilename = pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            );
            // this is needed to safely include the file name as part of the URL
            $safeFilename = self::slugify($originalFilename);
            $newFilename = date('Y-m-d') . '_' . $safeFilename . md5(
                microtime()
            ) . '.'
                . $file->guessExtension();
            $file->move(
                $this->uploadDir . $entityName,
                $newFilename
            );
            $fileObj = [
                'path' => $newFilename,
                'src' => self::BACKEND_URL_IMAGES . $entityName . $newFilename,
                'title' => $file->getClientOriginalName(),
                'mime' => $mime,
            ];
            $fileNames->add($fileObj);
        }

        return $fileNames;
    }
}
