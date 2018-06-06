<?php

namespace App\Service\Security\Exception;

/**
 * Something wrong with the service.
 *
 * Http error with 500 code should be generally thrown. But pay attention: message of
 * this exception is *not safety*, so don't show it to user.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class ServiceException extends \RuntimeException
{
}
