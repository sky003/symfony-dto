<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\User;

use App\Component\Security\Core\User\Exception\EmailNotFoundException;
use App\Component\Security\Provisioning\UserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provider that delegates the user loading operation to the user manager.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class SystemUserProvider implements UserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * SystemUserProvider constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws EmailNotFoundException
     */
    public function loadUserByEmail(string $email): UserInterface
    {
        $user = $this->userManager->loadUserByEmail($email);

        if (null === $user) {
            throw new EmailNotFoundException('Email not found so the user can not be loaded.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username): UserInterface
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof \App\Component\Security\Core\User\UserInterface) {
            throw new UnsupportedUserException(
                sprintf('User "%s" is not supported.', \get_class($user))
            );
        }

        $user = $this->userManager->loadUserByIdentifier($user->getIdentifier());

        if (null === $user) {
            throw new \UnexpectedValueException('Can not load a user to refresh.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class): bool
    {
        return \App\Component\Security\Core\User\UserInterface::class === $class;
    }
}
