<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * RequestToken constraint.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class RequestToken extends Constraint
{
    public const NOT_VALID_ERROR = '';
    public const EXPIRED_ERROR = '';

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
    public $expiredAtPropertyName = 'expiredAt';
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
        return ['entityClass', 'idPropertyName', 'tokenPropertyName'];
    }
}
