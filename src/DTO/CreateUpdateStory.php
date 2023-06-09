<?php

namespace App\DTO;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateStory
{
    #[Groups(['create'])]
    #[Assert\NotBlank(message: 'Titel darf nicht leer sein.', groups: ['create'])]
    public ?string $title = null;
    #[Groups(['create', 'update'])]
    #[Assert\NotBlank(message: 'Story darf nicht leer sein.', groups: ['create'])]
    public ?string $storie = null;
    #[Groups(['update'])]
    #[Assert\PositiveOrZero(message: 'Die Anzahl Likes muss Positiv sein', groups: ['update'])]
    #[Assert\NotBlank(message: 'Author muss leer sein / Darf nicht verändert werden.', groups: ['create'])]
    public ?int $likes = 0;
    #[Groups(['update'])]
    #[Assert\PositiveOrZero(message: 'Die Anzahl Dislikes muss Positiv sein', groups: ['update'])]
    #[Assert\NotBlank(message: 'Author muss leer sein / Darf nicht verändert werden.', groups: ['create'])]
    public ?int $dislikes = 0;
    #[Groups(['create', 'update'])]
    #[Assert\Blank(message: 'Author muss leer sein / Darf nicht verändert werden.', groups: ['update'])]
    public ?string $author = null;
}
