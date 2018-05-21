<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Entity\User;

interface SecurityServiceInterface
{
    public function registration(string $email, string $password): User;
    public function verification(int $id): User;
}
