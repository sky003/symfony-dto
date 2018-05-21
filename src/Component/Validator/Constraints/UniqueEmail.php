<?php

declare(strict_types = 1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * UniqueEmail constraint.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 *
 * @Annotation()
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueEmail extends Constraint
{
    public const NOT_UNIQUE_ERROR = '33528a26-afc7-468b-9830-ec13d426a3b2';

    public $message = 'Email is not unique.';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];
}
