<?php

declare(strict_types = 1);

namespace App\Entity;

/**
 * Interface for an entity object that represents specific resource (REST resource).
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface EntityResourceInterface
{
    public function getId(): int;
    public function setId(int $id): void;
}
