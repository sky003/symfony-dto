<?php

declare(strict_types = 1);

namespace App\Dto\Response;

/**
 * Interface for DTO object that represents a specific resource allowed to perform
 * the basic CRUD operations on (except "create" operation because it is non-idempotent;
 * in the current system response DTO object is always already created).
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface DtoResourceInterface
{
    /**
     * Returns a resource identifier.
     *
     * @return int
     */
    public function getId(): int;
}
