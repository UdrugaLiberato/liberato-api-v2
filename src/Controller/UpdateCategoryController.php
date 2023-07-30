<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelper;
use App\Utils\LiberatoHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsController]
class UpdateCategoryController {
  public const BACKEND_URL_IMAGES = 'https://dev.udruga-liberato.hr/images/';
  public string $uploadDir;

  public function __construct(
      public KernelInterface          $kernel,
      private ImageRepository         $imageRepository,
      private LiberatoHelperInterface $liberatoHelper,
      private CategoryRepository      $categoryRepository,
      private QuestionRepository      $questionRepository
  ) {
    $this->uploadDir = $this->kernel->getProjectDir() . '/public/images/';
  }

  public function __invoke(string $id, Request $request): Category {
    $categoryToUpdate = $this->categoryRepository->find($id);
    if ($request->files->get('file')) {
      $categoryToUpdate->getImage()->map(function (Image $image) use ($categoryToUpdate): void {
        $arr = explode('/', $image->getSrc());
        $file = $this->liberatoHelper->getImagePath('category/') . end($arr);

        if (file_exists($file)) {
          $categoryToUpdate->getImage()->clear();
          unlink($file);
        }
      });
      $file = $request->files->get('file');
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
          $this->uploadDir . 'category/',
          $newFilename
      );

      $image = new Image();
      $image->setSrc(self::BACKEND_URL_IMAGES . 'category/' . $newFilename);
      $image->setName($safeFilename);
      $image->setMime($mime);
      $image->addCategory($categoryToUpdate);
      $this->imageRepository->save($image, true);
    }

    if ($request->get('name') && $request->get('name') !== $categoryToUpdate->getName()) {
      $categoryToUpdate->setName($request->get('name'));
    }
    if ($request->get('description') && $request->get('description') !==
        $categoryToUpdate->getDescription()) {
      $categoryToUpdate->setDescription($request->get('description'));
    }
    $categoryToUpdate->setUpdatedAt(new \DateTimeImmutable('now'));
    if ($request->get('questions')) {
      $questions = explode(',', $request->get('questions'));
      foreach ($questions as $question) {
        $alreadyExists = $this->questionRepository->findOneBy(['question' => $question]);
        if (!$categoryToUpdate->getQuestions()->map(fn(Question $question) => $question->getQuestion())->contains($question) && !$alreadyExists instanceof Question && !empty($question)) {
          $newQuestion = new Question();
          $newQuestion->setCategory($categoryToUpdate);
          $newQuestion->setQuestion($question);
          $this->questionRepository->add($newQuestion, true);
          $categoryToUpdate->addQuestion($newQuestion);
        }
      }
    }
    return $categoryToUpdate;
  }

  private function clearQuestions($categoryToUpdate): void {
    foreach ($categoryToUpdate->getQuestions() as $question) {
      $categoryToUpdate->removeQuestion($question);
    }
  }
}
