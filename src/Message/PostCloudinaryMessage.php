<?php

declare(strict_types=1);

namespace App\Message;

use Doctrine\Common\Collections\ArrayCollection;

class PostCloudinaryMessage
{
    public function __construct(private string $id, private ArrayCollection $images)
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
