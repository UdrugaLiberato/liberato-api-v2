<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Question;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use App\Utils\LiberatoHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateCategoryController extends AbstractController
{
    public function __construct(
        private LiberatoHelperInterface $liberatoHelper,
        private CategoryRepository      $categoryRepository,
        private QuestionRepository      $questionRepository,
    )
    {
    }

    public function __invoke(Request $request)
    {
        $icon = $this->liberatoHelper->transformImage($request->files->get('icon'), 'category');
        if ($request->get("name") == null || strlen($request->get("name")) < 5) {
            return $this->json(["message" => "Name is required and must be at least 5 characters long"], 400);
        }

        if ($request->get("description") == null || strlen($request->get("description")) < 5) {
            return $this->json(["message" => "Description is required and must be at least 5 characters long"], 400);
        }
        $category = new Category();
        $category->setName($request->get('name'));
        $category->setDescription($request->get('description'));
        $category->setIcon($icon);
        $questions = explode(',', $request->get('questions'));

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