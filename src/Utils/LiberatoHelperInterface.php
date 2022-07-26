<?php

namespace App\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface LiberatoHelperInterface
{
    /**
     * @param string $string
     * @return string
     */
    public static function slugify(string $string): string;

    /**
     * @param ArrayCollection $image
     * @param string $entityName
     * @return ArrayCollection
     */
    public static function convertImageArrayToOutput(ArrayCollection $image, string $entityName): ArrayCollection;

    /**
     * @param ArrayCollection $images
     * @param string $entityName
     * @return ArrayCollection
     */
    public static function convertImagesArrayToOutput(ArrayCollection $images, string $entityName): ArrayCollection;

    /**
     * @param ArrayCollection $uploadedFiles
     * @param string $entityName
     * @return ArrayCollection
     */
    public function transformImages(ArrayCollection $uploadedFiles, string $entityName): ArrayCollection;

    /**
     * @param UploadedFile $file
     * @param string $entityName
     * @return ArrayCollection
     */
    public function transformImage(UploadedFile $file, string $entityName): ArrayCollection;
}