<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Assembler\Interview\InterviewDtoInitializer;
use App\Dto\Request;
use Doctrine\ORM\EntityManagerInterface;

class DtoInitializerFactory implements DtoInitializerFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDtoInitializer(Request\DtoResourceInterface $dto): DtoInitializerInterface
    {
        if ($dto instanceof Request\Interview) {
            return new InterviewDtoInitializer($dto, $this->entityManager);
        }

        throw new \LogicException('Unable to find an initializer for the entity you provide.');
    }
}
