<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CreateCategoryController extends AbstractController
{
    public function __invoke(Request $request): Category
    {
        dd($request->get("questions"));
    }
}