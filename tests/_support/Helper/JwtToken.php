<?php

declare(strict_types = 1);

namespace Helper;

use App\Component\Jwt\JwtTokenManagerInterface;
use Codeception\Module;

/**
 * Helper for JWT token management.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class JwtToken extends Module
{
    /**
     * @param array $payload
     *
     * @return string
     * @throws \Codeception\Exception\ModuleException
     */
    public function createJwtToken(array $payload): string
    {
        /** @var Module\Symfony $symfony */
        $symfony = $this->getModule('Symfony');
        /** @var JwtTokenManagerInterface $jwtTokenManager */
        $jwtTokenManager = $symfony->_getContainer()->get('test.app.component.jwt.jwt_token_manager');

        if (!isset($payload['exp'])) {
            $payload['exp'] = \time() + 86400;
        }

        return $jwtTokenManager->encode($payload);
    }
}
