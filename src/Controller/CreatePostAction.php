<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreatePostAction extends AbstractController
{
    public function __invoke(
      Request $request,
      ManagerRegistry $doctrine
    ): Post {
        $entityManager = $doctrine->getManager();
        
        $uploadDir = $this->getParameter('post_images');
        $fileNames = [];
        
        $post = new Post();
        
        $post->setTitle($request->get("title"));
        $post->setBody($request->get("body"));
        $post->setTags($request->get("tags"));
        $entityManager->persist($post);
        
        $uploadedFiles = $request->files->get('images');
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $key => $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(),
                                             PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $this->slugify($originalFilename);
                $newFilename = date('Y-m-d') . "_" . $safeFilename . md5
                  (microtime()) . '.'
                  . $file->guessExtension();
                $file->move(
                  $uploadDir,
                  $newFilename
                );
                $fileNames[$key] = $newFilename;
            }
            $post->setImages($fileNames);
            $entityManager->persist($post);
        }
        $entityManager->flush();
        
        return $post;
    }
    
    private function slugify(string $title): string
    {
        $title = preg_replace('~[^\pL\d]+~u', '-', $title);
        $title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);
        $title = preg_replace('~[^-\w]+~', '', $title);
        $title = trim($title, '-');
        $title = preg_replace('~-+~', '-', $title);
        
        return strtolower($title);
    }
}