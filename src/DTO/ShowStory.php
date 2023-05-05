<?php

namespace App\DTO;

class ShowStory
{
    public ?string $title = null;

    public ?string $storie = null;

    public ?int $likes = 0;

    public ?int $dislikes = 0;

    public ?string $author = null;

    public $comments = [];

}