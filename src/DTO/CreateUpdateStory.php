<?php

namespace App\DTO;

use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateStory
{
    #[Groups(["create","update"])]
    #[Assert\NotBlank(message: "Titel darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $title = null;
    #[Groups(["create","update"])]
    #[Assert\NotBlank(message: "Story darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $storie = null;
    #[Groups(["update"])]
    #[Assert\Blank(message: "Die Anzahl Likes darf nicht selber gesetzt werden", groups: ["create"])]
    #[Assert\PositiveOrZero(message: "Die Anzahl Likes muss Positiv sein", groups: ["update"])]
    public ?int $likes = 0;
    #[Groups(["update"])]
    #[Assert\Blank(message: "Die Anzahl Dislikes darf nicht selber gesetzt werden", groups: ["create"])]
    #[Assert\PositiveOrZero(message: "Die Anzahl Dislikes muss Positiv sein", groups: ["update"])]
    public ?int $dislikes = 0;
    #[Groups(["create","update"])]
    #[Assert\Blank(message: "Author muss leer sein / Darf nicht verändert werden.",groups: ["update"])]
    public ?string $author = null;
}