<?php

namespace App\DTO\Mapper;

abstract class BaseMapper implements IMapper
{
    public function mapEntitiesToDTOS(iterable $entities): iterable
    {
        $dtos = [];
        foreach ($entities as $entity) {
            $dtos[] = $this->mapEntityToDTO($entity);
        }

        return $dtos;
    }
}
