<?php

declare(strict_types=1);

namespace App\DTO\Question;

use App\Entity\Category;

class QuestionOutput
{
    public function __construct(
        public string   $question,
        public Category $category,
    )
    {
    }
}
