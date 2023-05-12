<?php

namespace App\DTO;

class FilterStory
{
    public ?int $likes = 0;

    public ?int $dislikes = 0;

    public ?string $author = null;

    public ?string $orderby = null;

    public ?string $orderdirection = null;
}
