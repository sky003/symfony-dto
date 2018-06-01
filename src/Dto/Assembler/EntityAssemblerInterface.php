<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Entity\EntityResourceInterface;

/**
 * Interface to implement an entity assembler.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface EntityAssemblerInterface
{
    /**
     * Write an entity object from a DTO object.
     *
     * @return EntityResourceInterface
     */
    public function writeEntity(): EntityResourceInterface;
}
