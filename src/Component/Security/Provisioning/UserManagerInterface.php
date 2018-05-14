<?php

declare(strict_types = 1);

namespace App\Component\Security\Provisioning;

use App\Component\Security\Core\User\UserInterface;

interface UserManagerInterface
{
    public const VERIFICATION_REQUEST_TYPE_EMAIL = 1;

    public function createUser(UserInterface $user): void;
    public function makeVerificationRequest(UserInterface $user, int $type): void;
    public function updateUser(UserInterface $user): void;
    public function deleteUser(UserInterface $user): void;
    public function loadUserByIdentifier(int $id): ?UserInterface;
    public function loadUserByEmail(string $email): ?UserInterface;
}
