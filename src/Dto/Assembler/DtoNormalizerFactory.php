<?php

declare(strict_types = 1);

namespace App\Dto\Assembler;

use App\Dto\Assembler\Interview\InterviewDtoNormalizer;
use App\Dto\Request;
use Doctrine\ORM\EntityManagerInterface;

class DtoNormalizerFactory implements DtoNormalizerFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDtoInitializer(Request\DtoResourceInterface $dto): DtoNormalizerInterface
    {
        if ($dto instanceof Request\Interview) {
            return new InterviewDtoNormalizer($dto, $this->entityManager);
        }

        throw new \LogicException('Unable to find an initializer for the entity you provide.');
    }
}
