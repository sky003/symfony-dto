<?php

declare(strict_types = 1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a user in our system.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 *
 * @ORM\Entity()
 * @ORM\Table(name="app_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User
{
    public const STATUS_ENABLED = 1;
    public const STATUS_UNVERIFIED = 10;
    public const STATUS_LOCKED = 20;

    public const ROLE_ADMIN = 1;
    public const ROLE_INTERVIEWER = 10;
    public const ROLE_INTERVIEWEE = 20;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=254, unique=true)
     */
    protected $email;
    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $passwordHash;
    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=3)
     */
    protected $role;
    /**
     * @var int
     *
     * @ORM\Column(type="smallint", length=3)
     */
    protected $status;
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="EmailVerificationRequest", mappedBy="user", cascade={"persist", "remove"})
     */
    private $emailVerificationRequests;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     *
     * @return self
     */
    public function setPasswordHash(string $passwordHash): User
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     *
     * @return self
     */
    public function setRole(int $role): User
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus(int $status): User
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEmailVerificationRequests(): Collection
    {
        return $this->emailVerificationRequests;
    }

    /**
     * @param Collection $emailVerificationRequests
     *
     * @return self
     */
    public function setEmailVerificationRequests(Collection $emailVerificationRequests): User
    {
        $this->emailVerificationRequests = $emailVerificationRequests;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime('now');
    }
}