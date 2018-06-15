<?php

declare(strict_types = 1);

namespace App\Dto\Assembler\Interview;

use App\Dto\Assembler\DtoNormalizerInterface;
use App\Dto\Assembler\Exception\DtoIdentifierNotFoundException;
use App\Dto\Request;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;

class InterviewDtoNormalizer implements DtoNormalizerInterface
{
    /**
     * @var Request\Interview
     */
    private $dto;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Request\Interview $dto, EntityManagerInterface $entityManager)
    {
        $this->dto           = $dto;
        $this->entityManager = $entityManager;
    }

    /**
     * Initializes a DTO object.
     *
     * An implementation, for example, can just load the object properties from the database
     * if some property's values not provided after deserialization (e.g. after PATCH request deserialization).
     *
     * @return Request\DtoResourceInterface
     */
    public function initializeDto(): Request\DtoResourceInterface
    {
        if (!$this->dto instanceof Request\PropertyChangeTrackerInterface) {
            throw new \LogicException('Can not use property change tracking strategy.');
        }

        /** @var Interview $entity */
        $entity = $this->entityManager->find(Interview::class, $this->dto->getId());

        if (null === $entity) {
            throw new DtoIdentifierNotFoundException('Resource identifier not found in the database.');
        }

        $this->dto->setTrackerEnabled(false);

        if (!$this->dto->isPropertyChanged('name')) {
            $this->dto->setName($entity->getName());
        }
        if (!$this->dto->isPropertyChanged('intro')) {
            $this->dto->setIntro($entity->getIntro());
        }

        $this->dto->setTrackerEnabled(true);

        return $this->dto;
    }
}
