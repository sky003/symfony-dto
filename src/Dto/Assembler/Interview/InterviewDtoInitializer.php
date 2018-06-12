<?php

declare(strict_types = 1);

namespace App\Dto\Assembler\Interview;

use App\Dto\Assembler\DtoInitializerInterface;
use App\Dto\Request;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;

class InterviewDtoInitializer implements DtoInitializerInterface
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
     * @throws \Doctrine\ORM\ORMException
     */
    public function initializeDto(): Request\DtoResourceInterface
    {
        if (!$this->dto instanceof Request\PropertyChangeTrackerInterface) {
            throw new \LogicException('Can not use property change tracking strategy.');
        }

        /** @var Interview $entity */
        $entity = $this->entityManager->getReference(Interview::class, $this->dto->getId());

        if (null === $entity) {
            throw new \UnexpectedValueException('Can not receive an entity form repository to initialize a DTO object.');
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
