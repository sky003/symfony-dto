<?php

declare(strict_types = 1);

namespace App\Component\Jwt;

/**
 * Interface to implement JWT token manager for this application.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
interface JwtTokenManagerInterface
{
    /**
     * Encode payload to JWT.
     *
     * @param array $payload
     *
     * @return string
     */
    public function encode(array $payload): string;

    /**
     * Decode JWT token to payload.
     *
     * @param string $jwtToken
     *
     * @return array
     */
    public function decode(string $jwtToken): array;
}
