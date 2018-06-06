<?php

declare(strict_types = 1);

namespace App\Entity;

/**
 * Token entity.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class Token
{
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var \DateTime
     */
    private $accessTokenExpiresIn;
    /**
     * @var string
     */
    private $refreshToken;
    /**
     * @var \DateTime
     */
    private $refreshTokenExpiresIn;

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     *
     * @return self
     */
    public function setAccessToken(string $accessToken): Token
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAccessTokenExpiresIn(): \DateTime
    {
        return $this->accessTokenExpiresIn;
    }

    /**
     * @param \DateTime $accessTokenExpiresIn
     *
     * @return self
     */
    public function setAccessTokenExpiresIn(\DateTime $accessTokenExpiresIn): Token
    {
        $this->accessTokenExpiresIn = $accessTokenExpiresIn;

        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     *
     * @return self
     */
    public function setRefreshToken(string $refreshToken): Token
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRefreshTokenExpiresIn(): \DateTime
    {
        return $this->refreshTokenExpiresIn;
    }

    /**
     * @param \DateTime $refreshTokenExpiresIn
     *
     * @return self
     */
    public function setRefreshTokenExpiresIn(\DateTime $refreshTokenExpiresIn): Token
    {
        $this->refreshTokenExpiresIn = $refreshTokenExpiresIn;

        return $this;
    }
}
