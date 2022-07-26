<?php

namespace App\DTO\Post;

use Doctrine\Common\Collections\ArrayCollection;

class PostInput
{
    public string $title;
    public string $body;
    public string $tags;
    public ArrayCollection $images;
}