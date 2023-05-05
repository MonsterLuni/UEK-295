<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateStory
{

    #[Assert\NotBlank(message: "Titel darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $title = null;
    #[Assert\NotBlank(message: "Story darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $storie = null;

    public ?int $likes = null;

    public ?int $dislikes = null;
    #[Assert\Blank(message: "Author muss leer sein / Darf nicht verändert werden.",groups: ["create", "update"])]
    public ?string $author = null;
}