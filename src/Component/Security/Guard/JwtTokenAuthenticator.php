<?php

declare(strict_types = 1);

namespace App\Component\Security\Guard;

use App\Component\Security\Core\User\UserInterface as AppUserInterface;
use App\Component\Security\Core\User\UserProviderInterface as AppUserProviderInterface;
use App\Component\Jwt\Exception\JwtExpiredException;
use App\Component\Jwt\Exception\JwtInvalidException;
use App\Component\Jwt\Exception\JwtSignatureInvalidException;
use App\Component\Jwt\JwtTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Authenticator for JWT token.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var string
     */
    private $authHeader = 'Authorization';
    /**
     * @var string
     */
    private $authScheme = 'Bearer';
    /**
     * @var JwtTokenManagerInterface
     */
    private $jwtTokenManager;

    public function __construct(JwtTokenManagerInterface $jwtTokenManager)
    {
        $this->jwtTokenManager = $jwtTokenManager;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Unauthorized', 401);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has($this->authHeader)
               && null !== $this->extractCredentialsFromAuthHeaderValue($request->headers->get($this->authHeader));
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        $authHeader = $request->headers->get($this->authHeader);
        $token = $this->extractCredentialsFromAuthHeaderValue($authHeader);

        return [
            'token' => $token,
        ];
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param array $credentials
     * @param UserProviderInterface $userProvider In future that's really makes sense to create database-less provider.
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        if (!$userProvider instanceof AppUserProviderInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of "%s" ("%s" given).',
                    AppUserProviderInterface::class,
                    \get_class($userProvider)
                )
            );
        }

        $jwtToken = $credentials['token'];

        if (empty($jwtToken)) {
            throw new AuthenticationException('JWT token is empty.');
        }

        try {
            $payload = $this->jwtTokenManager->decode($jwtToken);
        } catch (JwtSignatureInvalidException $e) {
            throw new CustomUserMessageAuthenticationException('JWT token signature is invalid.');
        } catch (JwtExpiredException $e) {
            throw new CustomUserMessageAuthenticationException('JWT token is expired.');
        } catch (JwtInvalidException $e) {
            throw new CustomUserMessageAuthenticationException('JWT token is invalid.');
        }

        return $userProvider->loadUserByIdentifier((int) $payload['sub']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if (!$user instanceof AppUserInterface) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user must be an instance of "%s" ("%s" given).',
                    AppUserInterface::class,
                    \get_class($user)
                )
            );
        }

        if (!$user->isAccountNonUnverified()) {
            throw new CustomUserMessageAuthenticationException('Account is not verified.');
        }
        if (!$user->isAccountNonLocked()) {
            throw new CustomUserMessageAuthenticationException('Account is locked.');
        }
        if (!$user->isAccountEnabled()) {
            throw new CustomUserMessageAuthenticationException('Account has restrictions.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'code' => 0,
            'message' => 'Token authentication failed.',
        ];

        if ($exception instanceof CustomUserMessageAuthenticationException) {
            $data['code'] = $exception->getCode();
            $data['message'] = $exception->getMessage();
        }

        return new JsonResponse($data, 401);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * Extracts JWT token from the request header value.
     *
     * @param string $value
     *
     * @return null|string
     */
    private function extractCredentialsFromAuthHeaderValue(string $value): ?string
    {
        $headerValueParts = \explode(' ', $value);

        if (\count($headerValueParts) !== 2 && $headerValueParts[0] !== $this->authScheme) {
            return null;
        }

        return $headerValueParts[1];
    }
}
