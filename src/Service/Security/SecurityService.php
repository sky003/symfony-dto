<?php

declare(strict_types = 1);

namespace App\Service\Security;

use App\Component\Security\Core\User\SystemUser;
use App\Component\Security\Provisioning\RepositoryUserManager;
use App\Component\Security\Provisioning\UserManagerInterface;
use App\Entity\EmailVerificationRequest;
use App\Entity\User;
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
     * @var UserManagerInterface
     */
    private $userManager;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UserManagerInterface $userManager, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->userManager = $userManager;
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
    public function registration(string $email, string $password): User
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
     * @param int $id Verification identifier to find its metadata.
     *
     * @return User
     * @throws ServiceException
     */
    public function verification(int $id): User
    {
        /** @var EmailVerificationRequest $verificationRequest */
        $verificationRequest = $this->entityManager->getRepository(EmailVerificationRequest::class)
            ->find($id);

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
}
