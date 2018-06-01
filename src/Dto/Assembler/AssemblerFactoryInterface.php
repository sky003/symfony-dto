<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Request\DtoResourceInterface;
use App\Entity\EntityResourceInterface;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface AssemblerFactoryInterface
{
    /**
     * @param EntityResourceInterface $entity Instance of an entity to assemble DTO from.
     *
     * @return DtoAssemblerInterface
     */
    public function loadEntity(EntityResourceInterface $entity): DtoAssemblerInterface;

    /**
     * @param DtoResourceInterface $dto Instance of a DTO object to assemble entity from.
     *
     * @return EntityAssemblerInterface
     */
    public function loadDto(DtoResourceInterface $dto): EntityAssemblerInterface;
}
