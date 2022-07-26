<?php

namespace App\DTO\Question;

use App\Entity\Category;

class QuestionInput
{
    public string $question;
    public Category $category;
}