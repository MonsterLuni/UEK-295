<?php

namespace App\DTO;

class CreateUpdateComment
{
    public ?int $refstory = null;

    public ?string $text = null;

    public ?int $likes = null;

    public ?int $dislikes = null;
}