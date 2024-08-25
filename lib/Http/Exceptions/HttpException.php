<?php

namespace Ilimurzin\Esia\Http\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class HttpException extends RuntimeException implements ClientExceptionInterface
{
}
