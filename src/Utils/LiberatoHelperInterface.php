<?php

declare(strict_types=1);

namespace App\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface LiberatoHelperInterface
{
    public static function slugify(string $string): string;

    public static function convertImageArrayToOutput(ArrayCollection $image, string $entityName): ArrayCollection;

    public static function convertImagesArrayToOutput(ArrayCollection $images, string $entityName): ArrayCollection;

    /**
     * @param array<UploadedFile> $uploadedFiles
     * public function “transformImages(array $uploadedFiles, string $entityName): ArrayCollection;
     */

    public function transformImage(?UploadedFile $file, string $entityName): ArrayCollection;

    public function getImagePath(string $subdirectoryWithSlash): string;

    /** @param array<UploadedFile> $files */
    public function transformFiles(array $files, string $entityName): ArrayCollection;

    public function uploadToCloudinary(ArrayCollection $images, string $entityName);
}
