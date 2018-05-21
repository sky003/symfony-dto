<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Check if request token is valid.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class RequestTokenValidator extends ConstraintValidator
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * RequestTokenValidator constructor.
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
        if (!$constraint instanceof RequestToken) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\RequestToken');
        }

        if (null === $value) {
            return;
        }

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $class */
        $class = $this->entityManager->getClassMetadata($constraint->entityClass);

        foreach ([$constraint->idPropertyName, $constraint->tokenPropertyName, $constraint->expiredAtPropertyName] as $fieldName) {
            if (!$class->hasField($fieldName)) {
                throw new ConstraintDefinitionException(
                    \sprintf(
                        'The field "%s" is not mapped by Doctrine, so it cannot be validated.',
                        $fieldName
                    )
                );
            }
        }

        $repository = $this->entityManager->getRepository($constraint->entityClass);
        $result = $repository->find($value->{'get'.\ucfirst($constraint->idPropertyName)}());

        if (!$result) {
            $this->context->buildViolation($constraint->notValidMessage)
                ->atPath($constraint->errorPath)
                ->setCode(RequestToken::NOT_VALID_ERROR)
                ->setCause('Request token not found in the repository.')
                ->addViolation();

            return;
        }

        if ($result->{'get'.\ucfirst($constraint->tokenPropertyName)}() !== $value->getToken()) {
            $this->context->buildViolation($constraint->notValidMessage)
                ->atPath($constraint->errorPath)
                ->setCode(RequestToken::NOT_VALID_ERROR)
                ->setCause('Request token not match the token founded in the repository.')
                ->addViolation();

            return;
        }

        if ($result->{'get'.\ucfirst($constraint->expiredAtPropertyName)}() < new \DateTime('now')) {
            $this->context->buildViolation($constraint->expiredMessage)
                ->atPath($constraint->errorPath)
                ->setCode(RequestToken::EXPIRED_ERROR)
                ->setCause('Request token is expired.')
                ->addViolation();

            return;
        }
    }
}
