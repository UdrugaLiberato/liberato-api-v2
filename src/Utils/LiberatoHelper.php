<?php

namespace App\Utils;

class LiberatoHelper implements LiberatoHelperInterface
{
    public static function slugify(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);
        return strtolower($string);
    }

}