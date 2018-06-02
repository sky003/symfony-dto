<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserIdentityService implements UserIdentityServiceInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserIdentityService constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isUserAuthenticated(): bool
    {
        $token = $this->tokenStorage->getToken();

        return $token !== null && $token->isAuthenticated();
    }

    /**
     * {@inheritdoc}
     */
    public function getUser(): User
    {
        if (!$this->isUserAuthenticated()) {
            throw new \LogicException('Trying to load an entity of not authenticated user.');
        }
        
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new \UnexpectedValueException(
                sprintf('User implementation "%s" is not supported.', \get_class($user))
            );
        }

        /** @var User $entity */
        $entity = $this->entityManager
            ->getRepository(User::class)
            ->find($user->getIdentifier());

        return $entity;
    }
}
