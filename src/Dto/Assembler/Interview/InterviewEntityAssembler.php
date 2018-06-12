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
     * @var Request\Interview
     */
    private $dto;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * InterviewEntityAssembler constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Request\Interview      $dto
     */
    public function __construct(Request\Interview $dto, EntityManagerInterface $entityManager)
    {
        $this->dto           = $dto;
        $this->entityManager = $entityManager;
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

    /**
     * Here's implemented PATCH-like update.
     *
     * @return Interview
     */
    private function buildUpdatedEntity(): Interview
    {
        /** @var Interview $interview */
        $interview = $this->entityManager
            ->getRepository(Interview::class)
            ->find($this->dto->getId());

        if (!$this->dto instanceof Request\PropertyChangeTrackerInterface) {
            throw new \LogicException('Can not use property change tracking strategy.');
        }

        if ($this->dto->isPropertyChanged('name')) {
            $interview->setName($this->dto->getName());
        }
        if ($this->dto->isPropertyChanged('intro')) {
            $interview->setIntro($this->dto->getIntro());
        }

        return $interview;
    }
}
