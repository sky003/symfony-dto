<?php

namespace App\Dto\Assembler\Exception;

/**
 * DTO object cant not be initialized because it can not be loaded by provided identifier.
 *
 * @author Anton Pelykh <anton.pelykh.dev@gmail.com>
 */
class DtoIdentifierNotFoundException extends \RuntimeException
{
}
