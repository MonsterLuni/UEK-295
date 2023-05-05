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

    public ?int $likes = 0;

    public ?int $dislikes = 0;

    #[Groups(["create"])]
    #[Assert\Blank(message: "Author muss leer sein / Darf nicht verändert werden.",groups: ["create", "update"])]
    public ?string $author = null;
}