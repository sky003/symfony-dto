<?php

declare(strict_types = 1);

namespace App\Service\Interview;

use App\Entity\EntityResourceInterface;
use App\Entity\Interview;
use App\Service\CrudServiceInterface;
use App\Service\Exception\ResourceNotFoundException;
use App\Service\Security\UserIdentityServiceInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
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

    public function get(int $id): ?EntityResourceInterface
    {
        /** @var Interview $interview */
        $interview = $this->entityManager
            ->getRepository(Interview::class)
            ->find($id);

        return $interview;
    }

    public function getList(Criteria $criteria): Collection
    {
        $repository = $this->entityManager->getRepository(Interview::class);

        if (!$repository instanceof Selectable) {
            throw new \LogicException(
                sprintf('Repository must implement "%s" interface.', Selectable::class)
            );
        }

        // Load all interviews of currently authenticated user only.
        $user = $this->userIdentityService->getUser();
        $criteria
            ->where(Criteria::expr()->eq('user', $user));

        return $repository->matching($criteria);
    }

    public function create(EntityResourceInterface $entity): void
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

    public function update(EntityResourceInterface $entity): void
    {
        /** @var Interview $entity */
        $this->throwExceptionIfNotSupported($entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $entity = $this->get($id);

        if (null === $entity) {
            throw new ResourceNotFoundException(
                \sprintf('Interview with id #%d not found so tit can not be deleted.', $id)
            );
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
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
