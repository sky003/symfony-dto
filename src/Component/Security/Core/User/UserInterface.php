<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\User;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getIdentifier(): ?int;
    public function getEmail(): ?string;
    public function isAccountNonLocked(): bool;
    public function isAccountNonUnverified(): bool;
    public function isAccountEnabled(): bool;
}
