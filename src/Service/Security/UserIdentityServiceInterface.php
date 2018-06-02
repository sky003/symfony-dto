<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Entity\User;

/**
 * Interface to implement service responsible for providing metadata of authenticated user.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface UserIdentityServiceInterface
{
    /**
     * Whether the user is authenticated.
     *
     * If the user is not authenticated, you can not get a user entity, because
     * the user can not be identified.
     *
     * @return bool
     */
    public function isUserAuthenticated(): bool;

    /**
     * Returns the user entity of currently authenticated user.
     *
     * Make sure the user is authenticated before load an entity.
     *
     * @return User
     *
     * @throws \LogicException When trying to load a user entity of not authenticated user.
     */
    public function getUser(): User;
}
