<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Component\Jwt\JwtTokenManagerInterface;
use App\Component\Security\Core\User\SystemUser;
use App\Component\Security\Provisioning\RepositoryUserManager;
use App\Component\Security\Provisioning\UserManagerInterface;
use App\Entity\EmailVerificationRequest;
use App\Entity\Token;
use App\Entity\User;
use App\Service\Security\Exception\ServiceException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service responsible for the user related operations.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class SecurityService implements SecurityServiceInterface
{
    /**
     * Access token expires after 24 hours.
     */
    private const ACCESS_TOKEN_EXPIRES_AFTER = 86400;

    /**
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var JwtTokenManagerInterface
     */
    private $jwtTokenManager;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UserManagerInterface $userManager, JwtTokenManagerInterface $jwtTokenManager, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->userManager = $userManager;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Responsible for the user registration.
     *
     * Add a new user to our system and send a verification request to user's email address.
     *
     * @param string $email
     * @param string $password
     *
     * @return User Newly created user.
     * @throws ServiceException
     */
    public function register(string $email, string $password): User
    {
        $this->logger->debug('User registration process started.');

        $systemUser = new SystemUser(
            null,
            $email,
            $password,
            ['ROLE_INTERVIEWER'],
            true,
            false,
            true
        );

        $this->userManager->createUser($systemUser);
        $systemUser = $this->userManager->loadUserByEmail($email);

        if (null === $systemUser->getIdentifier()) {
            $this->logger->critical('Identifier have not been assigned to user by user manager.', [
                'UserManager' => \get_class($this->userManager),
            ]);

            throw new \UnexpectedValueException('Unable to get identifier of newly created system user.');
        }

        $this->logger->info('New user has been added to our system.', [
            'UserId' => $systemUser->getIdentifier()
        ]);

        $this->userManager->makeVerificationRequest($systemUser, UserManagerInterface::VERIFICATION_REQUEST_TYPE_EMAIL);

        $this->logger->info('Verification email has been sent to user.', [
            'UserId' => $systemUser->getIdentifier(),
        ]);

        if ($this->userManager instanceof RepositoryUserManager) {
            /** @var User $user */
            $user = $this->entityManager
                ->getRepository(User::class)
                ->find($systemUser->getIdentifier());

            if (null === $user) {
                $this->logger->critical('Unable to find a newly created system user.', [
                    'UserId' => $systemUser->getIdentifier(),
                ]);

                throw new ServiceException('Unable to find a newly created system user in the repository.');
            }

            $this->logger->debug('User registration process completed.');

            return $user;
        }

        throw new \LogicException(
            sprintf('Unsupported user manager instance "%s"', \get_class($this->userManager))
        );
    }

    /**
     * @param int $verificationRequestId Verification identifier to find its metadata.
     *
     * @return User
     * @throws ServiceException
     */
    public function verify(int $verificationRequestId): User
    {
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->entityManager->getRepository(EmailVerificationRequest::class)
            ->find($verificationRequestId);

        if (null === $verificationRequest) {
            $this->logger->critical('Unable to find the verification request metadata.', [
                'EmailVerificationRequestId' => $verificationRequest->getId(),
            ]);

            throw new \UnexpectedValueException('Unable to find the verification request metadata in the repository.');
        }

        $this->entityManager->beginTransaction();
        try {
            $verificationRequest
                ->setStatus(EmailVerificationRequest::STATUS_VERIFIED)
                ->setVerifiedAt(new \DateTime());
            $this->entityManager->merge($verificationRequest);

            $user = $verificationRequest->getUser();
            $user
                ->setStatus(User::STATUS_ENABLED);
            $this->entityManager->merge($user);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();

            $this->logger->critical('Transaction failed.', [
                'message' => $e->getMessage(),
            ]);
            $this->logger->critical('Unable to update the email verification metadata.', [
                'EmailVerificationRequestId' => $verificationRequest->getId(),
            ]);

            throw new ServiceException(
                sprintf('Transaction failed with message: %s', $e->getMessage()),
                0,
                $e
            );
        }

        return $user;
    }

    /**
     * @param int $userId
     *
     * @return Token
     */
    public function issueToken(int $userId): Token
    {
        /** @var User $user */
        //$user = $this->entityManager
        //    ->getRepository(User::class)
        //    ->find($userId);

        $expiresAt = time() + self::ACCESS_TOKEN_EXPIRES_AFTER;
        $payload = [
            'sub' => $userId,
            'exp' => $expiresAt,
        ];
        $accessToken = $this->jwtTokenManager->encode($payload);

        $token = new Token();
        $token
            ->setAccessToken($accessToken)
            ->setAccessTokenExpiresIn(new \DateTime('@'.$expiresAt));

        return $token;
    }
}
