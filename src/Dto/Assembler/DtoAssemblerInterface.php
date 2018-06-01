<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Response;

/**
 * Interface to implement a DTO object assembler.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface DtoAssemblerInterface
{
    /**
     * Write a DTO object from an entity.
     *
     * @param string $version Version of DTO object (probably the same to REST API version) you want to write.
     *
     * @return Response\DtoResourceInterface
     */
    public function writeDto(string $version): Response\DtoResourceInterface;
}
