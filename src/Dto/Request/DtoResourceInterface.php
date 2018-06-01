<?php

declare(strict_types = 1);

namespace App\Dto\Request;

/**
 * Interface for DTO object that represents a specific resource (REST resource)
 * allowed to perform the basic CRUD operations on.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface DtoResourceInterface
{
    /**
     * Returns a resource identifier.
     *
     * @return int|null If identifier is `null`, that's a new resource
     *                  and you can only perform a "create" operation on it.
     */
    public function getId(): ?int;
}
