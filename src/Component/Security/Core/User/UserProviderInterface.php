<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\User;

use App\Component\Security\Core\User\Exception\EmailNotFoundException;
use App\Component\Security\Core\User\Exception\IdentifierNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface as BaseUserProviderInterface;

interface UserProviderInterface extends BaseUserProviderInterface
{
    /**
     * @param string $email
     *
     * @return UserInterface
     * @throws EmailNotFoundException
     */
    public function loadUserByEmail(string $email): UserInterface;

    /**
     * @param int $identifier
     *
     * @return UserInterface
     * @throws IdentifierNotFoundException
     */
    public function loadUserByIdentifier(int $identifier): UserInterface;
}
