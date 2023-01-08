<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Question;
use App\Repository\ImageRepository;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelper;
use Symfony\Component\HttpKernel\KernelInterface;

class CreateCategoryProcessor implements ProcessorInterface
{
    public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
    public string $uploadDir;

    public function __construct(
        public KernelInterface     $kernel,
        private QuestionRepository $questionRepository,
        private ImageRepository    $imageRepository,
    )
    {
        $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Category
    {
        $category = new Category();

        $category->setName($data->name);
        $category->setDescription($data->description);
        $file = $data->file;
        $ext = $file->guessExtension();
        $mime = $file->getMimeType();

        $originalFilename = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        // this is needed to safely include the file name as part of the URL
        $safeFilename = LiberatoHelper::slugify($originalFilename);
        $newFilename = date('Y-m-d') . '_' . $safeFilename . '.'
            . $ext;
        $file->move(
            $this->uploadDir . "category/",
            $newFilename
        );
        $image = new Image();
        $image->setSrc(self::BACKEND_URL_IMAGES . "category/" . $newFilename);
        $image->setName($safeFilename);
        $image->setMime($mime);
        $image->addCategory($category);
        $this->imageRepository->save($image, true);
        $category->addImage($image);

        $category->addImage($image);

        $questions = explode(',', $data->questions);
        foreach ($questions as $question) {
            $addQuestion = new Question();
            $addQuestion->setCategory($category);
            $addQuestion->setQuestion($question);
            $this->questionRepository->add($addQuestion);
            $category->addQuestion($addQuestion);
        }

        return $category;
    }
}
