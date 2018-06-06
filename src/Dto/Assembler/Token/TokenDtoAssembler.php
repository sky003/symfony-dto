<?php

declare(strict_types = 1);

namespace App\Dto\Assembler\Token;

use App\Entity\Token;
use App\Dto\Response;

/**
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
final class TokenDtoAssembler
{
    /**
     * @var Token
     */
    private $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function writeDto(string $version): Response\Token
    {
        // Currently only v1 is supported.
        if ($version !== 'v1') {
            throw new \InvalidArgumentException('Unsupported version provided.');
        }

        $dto = new Response\Token();
        $dto
            ->setAccessToken($this->token->getAccessToken())
            ->setAccessTokenExpiresIn($this->token->getAccessTokenExpiresIn());

        return $dto;
    }
}
