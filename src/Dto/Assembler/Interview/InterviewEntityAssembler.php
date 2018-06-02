<?php

declare(strict_types = 1);

namespace App\Dto\Assembler\Interview;

use App\Dto\Assembler\EntityAssemblerInterface;
use App\Dto\Request;
use App\Entity\EntityResourceInterface;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
final class InterviewEntityAssembler implements EntityAssemblerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Request\Interview
     */
    private $dto;

    /**
     * InterviewEntityAssembler constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Request\Interview      $dto
     */
    public function __construct(EntityManagerInterface $entityManager, Request\Interview $dto)
    {
        $this->entityManager = $entityManager;
        $this->dto           = $dto;
    }

    public function writeEntity(): EntityResourceInterface
    {
        return $this->isNewEntity() ? $this->buildNewEntity() : $this->buildUpdatedEntity();
    }

    private function isNewEntity(): bool
    {
        return null === $this->dto->getId();
    }

    private function buildNewEntity(): Interview
    {
        $interview = new Interview();
        $interview
            ->setName($this->dto->getName())
            ->setIntro($this->dto->getIntro());

        return $interview;
    }

    private function buildUpdatedEntity(): Interview
    {
        /** @var Interview $interview */
        $interview = $this->entityManager
            ->getRepository(Interview::class)
            ->find($this->dto->getId());

        // TODO: Implement PATCH-like update.

        return $interview;
    }
}
