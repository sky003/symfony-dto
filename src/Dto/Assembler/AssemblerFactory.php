<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Assembler\Interview\InterviewDtoAssembler;
use App\Dto\Assembler\Interview\InterviewEntityAssembler;
use App\Dto\Request\DtoResourceInterface;
use App\Dto\Request;
use App\Entity\EntityResourceInterface;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;

class AssemblerFactory implements AssemblerFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadEntity(EntityResourceInterface $entity): DtoAssemblerInterface
    {
        if ($entity instanceof Interview) {
            return new InterviewDtoAssembler($entity);
        }

        throw new \LogicException('Unable to find an assembler for the entity you provide.');
    }

    public function loadDto(DtoResourceInterface $dto): EntityAssemblerInterface
    {
        if ($dto instanceof Request\Interview) {
            return new InterviewEntityAssembler($this->entityManager, $dto);
        }

        throw new \LogicException('Unable to find an assembler for the DTO object you provide.');
    }
}
