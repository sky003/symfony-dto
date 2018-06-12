<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Request;

/**
 * Initialize a DTO object after it have been deserialized.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface DtoInitializerInterface
{
    /**
     * Initializes a DTO object.
     *
     * An implementation, for example, can just load the object properties from the database
     * if some property's values not provided after deserialization (e.g. after PATCH request deserialization).
     *
     * @return Request\DtoResourceInterface
     */
    public function initializeDto(): Request\DtoResourceInterface;
}
