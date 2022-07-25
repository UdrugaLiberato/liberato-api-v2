<?php

namespace App\Utils;

interface LiberatoHelperInterface
{
    public static function slugify(string $string): string;

}