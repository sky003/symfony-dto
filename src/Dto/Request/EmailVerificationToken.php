<?php

declare(strict_types = 1);

namespace App\Dto\Request;

use App\Component\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 *
 * @AppAssert\RequestToken({
 *     entityClass="App\Entity\EmailVerificationRequest",
 * })
 */
class EmailVerificationToken
{
    /**
     * @var int
     *
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $token;

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
    public function setId(int $id): EmailVerificationToken
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken(string $token): EmailVerificationToken
    {
        $this->token = $token;

        return $this;
    }
}
