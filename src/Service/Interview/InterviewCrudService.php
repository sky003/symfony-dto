<?php

declare(strict_types = 1);

namespace App\Service\Interview;

use App\Entity\Interview;
use App\Service\CrudServiceInterface;
use App\Service\Security\UserIdentityServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class InterviewCrudService implements CrudServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserIdentityServiceInterface
     */
    private $userIdentityService;

    /**
     * InterviewCrudService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserIdentityServiceInterface $userIdentityService
     */
    public function __construct(EntityManagerInterface $entityManager, UserIdentityServiceInterface $userIdentityService)
    {
        $this->entityManager = $entityManager;
        $this->userIdentityService = $userIdentityService;
    }

    public function get(int $id): ?object
    {
        // TODO: Implement get() method.

        return null;
    }

    public function getList(Criteria $criteria): Collection
    {
        // TODO: Implement getList() method.

        return new ArrayCollection();
    }

    public function create(object $entity): void
    {
        /** @var Interview $entity */
        $this->throwExceptionIfNotSupported($entity);

        // Assign an interview with the currently authenticate user.
        $entity->setUser(
            $this->userIdentityService->getUser()
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function update(object $entity): void
    {
        /** @var Interview $entity */
        $this->throwExceptionIfNotSupported($entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        // TODO: Implement delete() method.
    }

    private function throwExceptionIfNotSupported(object $entity): void
    {
        if (!$entity instanceof Interview) {
            throw new \InvalidArgumentException(
                sprintf('Entity must be an instance of "%s" ("%s" given).', Interview::class, \get_class($entity))
            );
        }
    }
}
