<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface as BaseUserProviderInterface;

interface UserProviderInterface extends BaseUserProviderInterface
{
    /**
     * @param string $email
     *
     * @return UserInterface
     */
    public function loadUserByEmail(string $email): UserInterface;
}
