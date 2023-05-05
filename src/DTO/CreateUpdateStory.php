<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUpdateStory
{

    #[Assert\NotBlank(message: "Titel darf nicht leer sein.",groups: ["create"])]
    public ?string $title = null;
    #[Assert\NotBlank(message: "Story darf nicht leer sein.",groups: ["create", "update"])]
    public ?string $storie = null;

    public ?int $likes = null;

    public ?int $dislikes = null;

    public ?string $author = null;
}