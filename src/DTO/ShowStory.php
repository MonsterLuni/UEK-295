<?php

namespace App\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;

class ShowStory
{
    public ?string $title = null;

    public ?string $storie = null;

    public ?int $likes = 0;

    public ?int $dislikes = 0;

    public ?string $author = null;

    #[Property(
        'Comments',
        type: 'array',
        items: new Items(
            ref: new Model(
                type: ShowComment::class
            )
        )
    )]
    public ?array $comments = [];
}
