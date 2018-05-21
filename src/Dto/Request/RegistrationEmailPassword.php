<?php

declare(strict_types = 1);

namespace App\Dto\Request;

use App\Component\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationEmailPassword
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @AppAssert\UniqueEmail()
     */
    private $email;
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=8)
     */
    private $password;

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
    public function setEmail(string $email): RegistrationEmailPassword
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return self
     */
    public function setPassword(string $password): RegistrationEmailPassword
    {
        $this->password = $password;

        return $this;
    }
}
