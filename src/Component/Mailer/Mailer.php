<?php

declare(strict_types = 1);

namespace App\Component\Mailer;

use App\Component\Security\Core\User\UserInterface;
use App\Entity\EmailVerificationRequest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Templating\EngineInterface;

class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var EngineInterface
     */
    private $templateEngine;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var array
     */
    private $parameters;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templateEngine, EntityManagerInterface $entityManager, array $parameters)
    {
        $this->mailer = $mailer;
        $this->templateEngine = $templateEngine;
        $this->entityManager = $entityManager;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function sendVerificationRequestEmailMessage(UserInterface $systemUser): void
    {
        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($systemUser->getIdentifier());

        $emailVerificationRequest = new EmailVerificationRequest();
        $emailVerificationRequest
            ->setUser($user)
            ->setToken($this->generateVerificationToken())
            ->setStatus(EmailVerificationRequest::STATUS_PENDING)
            ->setExpiresAt(new \DateTime('now + 2 hours'));

        $this->entityManager->persist($emailVerificationRequest);
        $this->entityManager->flush();

        $renderedTemplate = $this->templateEngine->render('mailer/verification.html.twig', [
            'user' => $user,
            'emailVerificationRequest' => $emailVerificationRequest,
        ]);

        $this->sendEmailMessage(
            $renderedTemplate,
            [$this->parameters['verification.from_email']],
            [$user->getEmail()]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function sendPasswordResetRequestEmailMessage(UserInterface $user): void
    {
        throw new \BadMethodCallException('Method not implemented.');
    }

    /**
     * @param string $renderedTemplate
     * @param array  $fromEmail
     * @param array  $toEmail
     */
    protected function sendEmailMessage(string $renderedTemplate, array $fromEmail, array $toEmail): void
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $this->mailer->send($message);
    }

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(64));
    }
}
