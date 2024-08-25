<?php

namespace Ilimurzin\Esia\Signer\Exceptions;

use Ilimurzin\Esia\Exceptions\AbstractEsiaException;

class SignFailException extends AbstractEsiaException
{
    protected function getMessageForCode(int $code): string
    {
        return 'Signing is failed';
    }
}
