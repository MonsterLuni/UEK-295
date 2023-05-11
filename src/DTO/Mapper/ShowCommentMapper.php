<?php

namespace App\DTO\Mapper;

use App\DTO\ShowComment;

class ShowCommentMapper extends BaseMapper
{
    /**
     * Takes
     * @param object $entity. Takes an Entity and maps it to
     * @return ShowComment
     */
    public function mapEntityToDTO(object $entity) : ShowComment
    {

        $dto = new ShowComment();
        $dto->refstory = $entity->getrefstory()->getTitle();
        $dto->text = $entity->gettext();
        $dto->likes = $entity->getLikes();
        $dto->dislikes = $entity->getDislikes();

        return $dto;
    }
}