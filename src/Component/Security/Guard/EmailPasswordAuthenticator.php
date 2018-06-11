<?php

declare(strict_types = 1);

namespace App\Component\Security\Guard;

use App\Component\Security\Core\User\UserInterface as AppUserInterface;
use App\Component\Security\Core\User\UserProviderInterface as AppUserProviderInterface;
use App\Dto\Request\AuthenticationEmailPassword;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Authenticator for email and password credentials.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
final class EmailPasswordAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var AuthenticationSuccessHandlerInterface
     */
    private $authenticationSuccessHandler;
    /**
     * @var AuthenticationFailureHandlerInterface
     */
    private $authenticationFailureHandler;

    /**
     * EmailPasswordAuthenticator constructor.
     *
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, EncoderFactoryInterface $encoderFactory)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param AuthenticationSuccessHandlerInterface $authenticationSuccessHandler
     */
    public function setAuthenticationSuccessHandler(AuthenticationSuccessHandlerInterface $authenticationSuccessHandler): void
    {
        $this->authenticationSuccessHandler = $authenticationSuccessHandler;
    }

    /**
     * @param AuthenticationFailureHandlerInterface $authenticationFailureHandler
     */
    public function setAuthenticationFailureHandler(AuthenticationFailureHandlerInterface $authenticationFailureHandler): void
    {
        $this->authenticationFailureHandler = $authenticationFailureHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse(
            [
                'code' => 0,
                'message' => 'Authentication credentials not provided.',
            ],
            401
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $request->getContentType() === 'json';
    }

    /**
     * Returns a DTO object with the authentication credentials.
     *
     * @param Request $request
     *
     * @return AuthenticationEmailPassword
     */
    public function getCredentials(Request $request): AuthenticationEmailPassword
    {
        /** @var AuthenticationEmailPassword $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            AuthenticationEmailPassword::class,
            'json'
        );

        return $dto;
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * You may throw an AuthenticationException if you wish. If you return
     * null, then a UsernameNotFoundException is thrown for you.
     *
     * @param AuthenticationEmailPassword $credentials
     * @param UserProviderInterface       $userProvider
     *
     * @throws AuthenticationException
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
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

        $errors = $this->validator->validate($credentials);
        if (\count($errors) > 0) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        return $userProvider->loadUserByEmail($credentials->getEmail());
    }

    /**
     * Returns true if the credentials are valid.
     *
     * If any value other than true is returned, authentication will
     * fail. You may also throw an AuthenticationException if you wish
     * to cause authentication to fail.
     *
     * The *credentials* are the return value from getCredentials()
     *
     * @param AuthenticationEmailPassword $credentials
     * @param UserInterface               $user
     *
     * @return bool
     *
     * @throws AuthenticationException
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

        return $this->encoderFactory->getEncoder($user)->isPasswordValid(
            $user->getPassword(),
            $credentials->getPassword(),
            null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if (null !== $this->authenticationFailureHandler) {
            return $this->authenticationFailureHandler->onAuthenticationFailure($request, $exception);
        }

        $data = [
            'code' => 0,
            'message' => 'Wrong email or password.',
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
        if (null !== $this->authenticationSuccessHandler) {
            return $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $token);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
