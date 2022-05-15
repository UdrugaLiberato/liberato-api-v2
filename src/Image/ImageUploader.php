<?php

namespace App\Image;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function upload(File $file, string $path): string
    {
        $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move('%kernel.project_dir%/public/location/images' . $path, $fileName);

        return $fileName;
    }

    public function convert(string $base64Image): File
    {
        $base64Exploded = explode(',', $base64Image);
        $img = $base64Exploded[1];
        $ext = explode('/', explode(':', explode(';', $base64Exploded[0])[0])[1])[1];

        $filesystem = new Filesystem();
        $content = base64_decode($img);

        $filesystem->dumpFile('%kernel.project_dir%/public/location/images' . 'file.' . $ext, $content);

        return new File('%kernel.project_dir%/public/location/images' . 'file.' . $ext);
    }
}
