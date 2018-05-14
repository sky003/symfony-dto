<?php

declare(strict_types = 1);

namespace App\Component\Security\Provisioning;

use App\Component\Mailer\MailerInterface;
use App\Component\Security\Core\User\SystemUser;
use App\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class RepositoryUserManager implements UserManagerInterface, EncoderAwareInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * RepositoryUserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoderInterface $passwordEncoder
     * @param MailerInterface $mailer
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderInterface $passwordEncoder, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
    }

    public function createUser(UserInterface $systemUser): void
    {
        $user = $this->buildNewUser($systemUser);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function makeVerificationRequest(UserInterface $systemUser, int $type): void
    {
        if ($type === self::VERIFICATION_REQUEST_TYPE_EMAIL) {
            $this->mailer->sendVerificationRequestEmailMessage($systemUser);

            return;
        }

        throw new \LogicException('Can not handle a verification request type you provide.');
    }

    public function updateUser(UserInterface $user): void
    {
        // TODO: Implement updateUser() method.
    }

    public function deleteUser(UserInterface $user): void
    {
        // TODO: Implement deleteUser() method.
    }

    public function loadUserByIdentifier(int $id): UserInterface
    {
        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($id);

        if (null === $user) {
            return null;
        }

        return $this->buildSystemUser($user);
    }

    public function loadUserByEmail(string $email): UserInterface
    {
        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (null === $user) {
            return null;
        }

        return $this->buildSystemUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getEncoderName(): string
    {
        return 'bcrypt';
    }

    private function buildNewUser(UserInterface $systemUser): User
    {
        $user = new User();
        $user
            ->setEmail($systemUser->getEmail())
            ->setPasswordHash(
                $this->passwordEncoder->encodePassword($systemUser->getPassword(), null)
            )
            ->setRole($this->buildUserRole($systemUser))
            ->setStatus($this->buildUserStatus($systemUser));

        return $user;
    }

    private function buildUserRole(UserInterface $systemUser): int
    {
        $rolesCount = \count($systemUser->getRoles());
        if ($rolesCount === 0) {
            throw new \LogicException('You must provide a user role.');
        }
        if ($rolesCount > 1) {
            throw new \LogicException('Can not handle more than one user role.');
        }

        switch($systemUser->getRoles()[0]) {
            case 'ROLE_INTERVIEWEE':
                return User::ROLE_INTERVIEWEE;
            case 'ROLE_INTERVIEWER':
                return User::ROLE_INTERVIEWER;
            case 'ROLE_ADMIN':
                return User::ROLE_ADMIN;
            default:
                throw new \LogicException(
                    sprintf(
                        'User role "%s" received from the system user instance can not be handled.',
                        $systemUser->getRoles()[0]
                    )
                );
        }
    }

    private function buildUserStatus(UserInterface $systemUser): int
    {
        if (!$systemUser->isAccountNonUnverified()) {
            return User::STATUS_UNVERIFIED;
        }
        if (!$systemUser->isAccountNonLocked()) {
            return User::STATUS_LOCKED;
        }
        if ($systemUser->isAccountEnabled()) {
            return User::STATUS_ENABLED;
        }

        throw new \LogicException('Can not handle account status of the system user.');
    }

    private function buildSystemUser(User $user): SystemUser
    {
        return new SystemUser(
            $user->getId(),
            $user->getEmail(),
            $user->getPasswordHash(),
            $this->buildSystemUserRoles($user),
            User::STATUS_ENABLED === $user->getStatus(),
            User::STATUS_UNVERIFIED !== $user->getStatus(),
            User::STATUS_LOCKED !== $user->getStatus()
        );
    }

    private function buildSystemUserRoles(User $user): array
    {
        switch($user->getRole()) {
            case User::ROLE_INTERVIEWEE:
                return ['ROLE_INTERVIEWEE'];
            case User::ROLE_INTERVIEWER:
                return ['ROLE_INTERVIEWER'];
            case User::ROLE_ADMIN:
                return ['ROLE_ADMIN'];
            default:
                throw new \LogicException(
                    sprintf('User role "%d" received from the repository can not be handled.', $user->getRole())
                );
        }
    }
}
