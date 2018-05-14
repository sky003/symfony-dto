<?php

declare(strict_types = 1);

namespace App\Component\Security\Core\User;

/**
 * A user in our system.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class SystemUser implements UserInterface
{
    /**
     * @var int|null
     */
    private $identifier;
    /**
     * @var null|string
     */
    private $email;
    /**
     * @var null|string
     */
    private $password;
    /**
     * @var array
     */
    private $roles;
    /**
     * @var bool
     */
    private $accountEnabled;
    /**
     * @var bool
     */
    private $accountNonUnverified;
    /**
     * @var bool
     */
    private $accountNonLocked;

    public function __construct(?int $identifier, ?string $email, ?string $password, array $roles = [], bool $accountEnabled = true, bool $accountNonUnverified = true, bool $accountNonLocked = true)
    {
        if ((0 === $identifier || null === $identifier)
            || ('' === $email || null === $email)) {
            throw new \InvalidArgumentException('You need provide at least identifier or email.');
        }

        $this->identifier = $identifier;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->accountEnabled = $accountEnabled;
        $this->accountNonUnverified = $accountNonUnverified;
        $this->accountNonLocked = $accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): ?int
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked(): bool
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonUnverified(): bool
    {
        return $this->accountNonUnverified;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountEnabled(): bool
    {
        return $this->accountEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->email;
    }
}
