<?php

namespace App\DTO;

use App\Validator\StoryDoesExist;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateComment
{
    #[Assert\NotBlank(message: 'Es muss eine Referenzstory angegeben werden', groups: ['create'])]
    #[Assert\Blank(message: 'Die Referenzstory darf nicht verändert werden', groups: ['update'])]
    #[StoryDoesExist(groups: ['create'])]
    public ?int $refstory = null;
    // ES GIBT UPDATE NOCH NICHT
    // ANSCHEINEND GARNIE LUL (bitti betti kein punktabzug für diese Kommentare (ich vertraue darauf))
    #[Assert\NotBlank(message: 'Es muss ein Text angegeben werden', groups: ['create'])]
    public ?string $text = null;
    #[Assert\PositiveOrZero(message: 'Die anzahl Likes muss Positiv sein', groups: ['create', 'update'])]
    public ?int $likes = 0;
    #[Assert\PositiveOrZero(message: 'Die anzahl Dislikes muss Positiv sein', groups: ['create', 'update'])]
    public ?int $dislikes = 0;
}
