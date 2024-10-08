<?php

namespace Ilimurzin\Esia\Signer;

use Ilimurzin\Esia\Signer\Exceptions\SignFailException;

final class CryptoProSigner implements SignerInterface
{
    public function __construct(
        private string $thumbprint,
        private ?string $pin = null,
    ) {}

    public function sign(string $message): string
    {
        $store = new \CPStore();
        $store->Open(CURRENT_USER_STORE, 'My', STORE_OPEN_READ_ONLY);

        $certificates = $store->get_Certificates();
        $found = $certificates->Find(CERTIFICATE_FIND_SHA1_HASH, $this->thumbprint, 0);
        $certificate = $found->Item(1);
        if (!$certificate) {
            throw new SignFailException('Cannot read the certificate');
        }
        if ($certificate->HasPrivateKey() === false) {
            throw new SignFailException('Cannot read the private key');
        }

        $signer = new \CPSigner();
        $signer->set_Certificate($certificate);
        if ($this->pin) {
            $signer->set_KeyPin($this->pin);
        }

        $sd = new \CPSignedData();
        $sd->set_ContentEncoding(BASE64_TO_BINARY);
        $sd->set_Content(base64_encode($message));

        return $sd->SignCades($signer, CADES_BES, true, ENCODE_BASE64);
    }
}
