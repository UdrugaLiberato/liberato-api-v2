<?php

namespace App\Controller;


use App\Entity\MediaObject;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateMediaObjectAction extends AbstractController
{
    public function __invoke(Request $request)
    {
        $uploadedFiles = $request->files->get('file');
        foreach ($uploadedFiles as $file) {
            return $this->getFiles($file);
        }
    }

    private function getFiles(File $file): MediaObject
    {
        $mediaObject = new MediaObject();
        $mediaObject->file = $file;

        return $mediaObject;
    }
}

