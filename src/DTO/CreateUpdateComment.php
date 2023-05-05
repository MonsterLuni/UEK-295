<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateComment
{
    #[Assert\NotBlank(message: "Es muss eine Referenzstory angegeben werden", groups: ["create"])]
    #[Assert\Blank(message: "Die Referenzstory darf nicht verändert werden", groups: ["update"])]
    public ?int $refstory = null;
    //ES GIBT UPDATE NOCH NICHT
    #[Assert\NotBlank(message: "Es muss ein Text angegeben werden", groups: ["create", "update"])]
    public ?string $text = null;

    public ?int $likes = null;

    public ?int $dislikes = null;
}