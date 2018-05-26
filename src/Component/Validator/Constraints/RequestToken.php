<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * RequestToken constraint.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 *
 * @Annotation()
 * @Target({"CLASS", "ANNOTATION"})
 */
class RequestToken extends Constraint
{
    public const NOT_VALID_ERROR = 'c06d072f-c3e9-49b5-a307-b9f295ff44c9';
    public const EXPIRED_ERROR = 'a2cd0f16-0bbc-4d1d-8bc8-5123d6d6a834';

    public $notValidMessage = 'Request token is not valid.';
    public $expiredMessage = 'Request token is expired.';

    /**
     * @var string
     */
    public $entityClass;
    /**
     * @var string Token identifier field name.
     */
    public $idPropertyName = 'id';
    /**
     * @var string Token field name.
     */
    public $tokenPropertyName = 'token';
    public $expiresAtPropertyName = 'expiresAt';
    /**
     * @var string
     */
    public $errorPath = 'token';

    protected static $errorNames = array(
        self::NOT_VALID_ERROR => 'NOT_VALID_ERROR',
        self::EXPIRED_ERROR => 'EXPIRED_ERROR',
    );

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions(): array
    {
        return ['entityClass'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption(): string
    {
        return 'entityClass';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
