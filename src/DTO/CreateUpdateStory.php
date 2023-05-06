<?php

namespace App\DTO;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateStory
{
    #[Groups(["create"])]
    #[Assert\NotBlank(message: "Titel darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $title = null;
    #[Groups(["create"])]
    #[Assert\NotBlank(message: "Story darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $storie = null;
    #[Assert\PositiveOrZero(message: "Die Anzahl Likes muss Positiv sein", groups: ["create","update"])]
    public ?int $likes = 0;
    #[Assert\PositiveOrZero(message: "Die Anzahl Dislikes muss Positiv sein", groups: ["create","update"])]
    public ?int $dislikes = 0;
    #[Groups(["create"])]
    #[Assert\Blank(message: "Author muss leer sein / Darf nicht verändert werden.",groups: ["update"])]
    public ?string $author = null;
}