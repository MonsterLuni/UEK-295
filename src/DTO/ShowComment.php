<?php

namespace App\DTO;

class ShowComment
{
    public ?string $refstory = null;

    public ?string $text = null;

    public ?int $likes = 0;

    public ?int $dislikes = 0;
}
