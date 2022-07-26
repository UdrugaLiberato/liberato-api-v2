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

    public function transformImages(ArrayCollection $uploadedFiles, string $entityName): ArrayCollection;

    public function transformImage(UploadedFile $file, string $entityName): ArrayCollection;
}
