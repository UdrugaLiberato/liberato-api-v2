<?php
declare(strict_types=1);

namespace App\DTO\NewsArticle;

use Doctrine\Common\Collections\ArrayCollection;

class NewsArticleInput
{
    public string $title;
    public string $url;
    public ArrayCollection $image;
}