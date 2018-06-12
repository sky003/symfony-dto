<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Request;

interface DtoInitializerFactoryInterface
{
    public function getDtoInitializer(Request\DtoResourceInterface $dto): DtoInitializerInterface;
}
