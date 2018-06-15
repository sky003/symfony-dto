<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Request;

interface DtoNormalizerFactoryInterface
{
    public function getDtoInitializer(Request\DtoResourceInterface $dto): DtoNormalizerInterface;
}
