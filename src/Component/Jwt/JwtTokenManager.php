<?php

declare(strict_types = 1);

namespace App\Component\Jwt;

use App\Component\Jwt\Exception\JwtExpiredException;
use App\Component\Jwt\Exception\JwtInvalidException;
use App\Component\Jwt\Exception\JwtSignatureInvalidException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;

/**
 * Simple implementation JwtTokenManager.
 *
 * @see https://jwt.io/introduction/
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class JwtTokenManager implements JwtTokenManagerInterface
{
    /**
     * @var string
     */
    private $privateKeyFile;
    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * JwtTokenManager constructor.
     *
     * @param string $privateKeyFile
     * @param string $publicKeyFile
     */
    public function __construct(string $privateKeyFile, string $publicKeyFile)
    {
        $this->privateKeyFile = $privateKeyFile;
        $this->publicKeyFile = $publicKeyFile;
    }

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
    public function decode(string $jwtToken): array
    {
        $parser = new Parser();
        $token = $parser->parse($jwtToken);

        $verified = $token->verify(new Sha256(), $this->getPublicKey());
        if (!$verified) {
            throw new JwtSignatureInvalidException('JWT token not passed signature verification.');
        }

        if ($token->isExpired()) {
            throw new JwtExpiredException('JWT token expired.');
        }

        $validationData = new ValidationData();
        $valid = $token->validate($validationData);
        if (!$valid) {
            throw new JwtInvalidException('JWT token not valid.');
        }

        return $token->getClaims();
    }

    /**
     * Encode payload to JWT.
     *
     * @param array $payload
     *
     * @return string
     */
    public function encode(array $payload): string
    {
        $tokenBuilder = new Builder();
        foreach ($payload as $claim => $value) {
            $tokenBuilder->set($claim, $value);
        }
        $tokenBuilder->sign(new Sha256(), $this->getPrivateKey());

        return (string) $tokenBuilder->getToken();
    }

    private function getPrivateKey(): Key
    {
        return new Key(file_get_contents($this->privateKeyFile));
    }

    private function getPublicKey(): Key
    {
        return new Key(file_get_contents($this->publicKeyFile));
    }
}
