<?php

namespace App\Utils;

interface LiberatoHelperInterface
{
    public static function slugify(string $string): string;

    public function transformImages(array $uploadedFiles, string $entityName): array;
}