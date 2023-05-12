<?php

namespace App\DTO\Mapper;

interface IMapper
{
    public function mapEntityToDTO(object $entity);

    public function mapEntitiesToDTOs(iterable $entities): iterable;
}
