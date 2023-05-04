<?php

namespace App\DTO\Mapper;

use App\DTO\ShowComment;

class ShowCommentMapper
{
    public function mapEntityToDTO(object $entity)
    {
        $dto = new ShowComment();
        $dto->refstory = $entity->getrefstory()->getTitle();
        $dto->text = $entity->gettext();
        $dto->likes = $entity->getLikes();
        $dto->dislikes = $entity->getDislikes();

        return $dto;
    }
}