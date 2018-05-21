<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Check if user's email is unique.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class UniqueEmailValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UniqueEmailValidator constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEmail) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueEmail');
        }

        if (null === $value) {
            return;
        }

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $value]);

        if (null !== $user) {
            $this->context->buildViolation($constraint->message)
                ->setCode(UniqueEmail::NOT_UNIQUE_ERROR)
                ->addViolation();

            return;
        }
    }
}
