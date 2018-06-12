<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Check if an entity exists in the database.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class ExistEntityValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ExistEntity');
        }

        if (null === $value) {
            return;
        }

        $properties = (array) $constraint->properties;

        /**
         * @var $class \Doctrine\ORM\Mapping\ClassMetadata
         */
        $class = $this->entityManager->getClassMetadata($constraint->entityClass);

        $criteria = [];
        foreach ($properties as $propertyName) {
            if (!$class->hasField($propertyName)) {
                throw new ConstraintDefinitionException(
                    sprintf('The field "%s" is not mapped by Doctrine, so it cannot be validated for existence.', $propertyName)
                );
            }

            $criteria[$propertyName] = $value;
        }

        $repository = $this->entityManager->getRepository($constraint->entityClass);

        $result = $repository->{$constraint->repositoryMethod}($criteria);

        if (0 !== \count($result)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setCode(ExistEntity::NOT_EXIST_ERROR)
            ->addViolation();
    }
}
