<?php

namespace App\DTO\Mapper;

use App\DTO\ShowStory;

class ShowStoryMapper extends BaseMapper
{
    public function mapEntityToDTO(object $entity): object
    {
        $mapper = new ShowCommentMapper();

        $dto = new ShowStory();
        $dto->title = $entity->gettitle();
        $dto->storie = $entity->getstorie();
        $dto->likes = $entity->getLikes();
        $dto->dislikes = $entity->getDislikes();
        $dto->author = $entity->getAuthor();

        $dto->comments = $mapper->mapEntitiesToDTOS($entity->getComments());

        return $dto;
    }
}
