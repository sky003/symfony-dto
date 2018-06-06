<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Entity\Token;
use App\Entity\User;

interface SecurityServiceInterface
{
    public function register(string $email, string $password): User;
    public function verify(int $verificationRequestId): User;
    public function issueToken(int $userId): Token;
}
