<?php

declare(strict_types = 1);

namespace App\Component\Mailer;

use App\Component\Security\Core\User\UserInterface;

interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation.
     *
     * @param UserInterface $user
     */
    public function sendVerificationRequestEmailMessage(UserInterface $user): void;

    /**
     * Send an email to a user to confirm the password reset.
     *
     * @param UserInterface $user
     */
    public function sendPasswordResetRequestEmailMessage(UserInterface $user): void;
}
