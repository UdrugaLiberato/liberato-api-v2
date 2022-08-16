<?php

namespace App\Message;

use Doctrine\Common\Collections\ArrayCollection;

class NewsArticleCloudinaryMessage
{
    public function __construct(private string $id, private $images)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImages(): ArrayCollection
    {
        return $this->images;
    }
}