<?php

namespace App\DTO;

use App\Validator\StoryDoesExist;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateComment
{
    #[Assert\NotBlank(message: "Es muss eine Referenzstory angegeben werden", groups: ["create"])]
    #[Assert\Blank(message: "Die Referenzstory darf nicht verändert werden", groups: ["update"])]
    #[StoryDoesExist(groups: ["create"])]
    public ?int $refstory = null;
    //ES GIBT UPDATE NOCH NICHT
    #[Assert\NotBlank(message: "Es muss ein Text angegeben werden", groups: ["create", "update"])]
    public ?string $text = null;
    #[Assert\PositiveOrZero(message: "Die anzahl muss mindestensPositiv sein", groups: ["create","update"])]
    public ?int $likes = null;
    #[Assert\PositiveOrZero(message: "Die anzahl muss Positiv sein", groups: ["create","update"])]
    public ?int $dislikes = null;
}