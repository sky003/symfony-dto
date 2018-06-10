<?php

declare(strict_types = 1);

namespace App\Component\Jwt;
use App\Component\Jwt\Exception\JwtExpiredException;
use App\Component\Jwt\Exception\JwtInvalidException;
use App\Component\Jwt\Exception\JwtSignatureInvalidException;

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
     * @throws JwtExpiredException
     * @throws JwtInvalidException
     * @throws JwtSignatureInvalidException
     */
    public function decode(string $jwtToken): array;
}
