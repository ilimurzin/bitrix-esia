<?php

namespace Ilimurzin\Esia\Signer;

use Ilimurzin\Esia\Signer\Exceptions\SignFailException;

interface SignerInterface
{
    /**
     * @throws SignFailException
     */
    public function sign(string $message): string;
}
