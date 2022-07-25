<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface LiberatoHelperInterface
{
    /**
     * @param string $string
     * @return string
     */
    public static function slugify(string $string): string;

    /**
     * @param array<string> $image
     * @param string $entityName
     * @return array<string>
     */
    public static function convertImageArrayToOutput(array $image, string $entityName): array;

    /**
     * @param array<array<string>> $images
     * @param string $entityName
     * @return array<array<string>>
     */
    public static function convertImagesArrayToOutput(array $images, string $entityName): array;

    /**
     * @param array<UploadedFile> $uploadedFiles
     * @param string $entityName
     * @return array<array<string>>
     */
    public function transformImages(array $uploadedFiles, string $entityName): array;

    /**
     * @param UploadedFile $file
     * @param string $entityName
     * @return array<string>
     */
    public function transformImage(UploadedFile $file, string $entityName): array;
}