<?php

declare(strict_types = 1);

namespace App\Component\HttpKernel\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException as BaseUnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * UnprocessableEntityHttpException with ability to provide validation errors.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class UnprocessableEntityHttpException extends BaseUnprocessableEntityHttpException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    public function __construct(ConstraintViolationListInterface $errors, string $message = null, \Exception $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message, $previous, $code, $headers);

        $this->errors = $errors;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
