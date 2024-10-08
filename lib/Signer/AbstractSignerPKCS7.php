<?php

namespace Ilimurzin\Esia\Signer;

use Ilimurzin\Esia\Signer\Exceptions\CannotReadCertificateException;
use Ilimurzin\Esia\Signer\Exceptions\CannotReadPrivateKeyException;
use Ilimurzin\Esia\Signer\Exceptions\NoSuchCertificateFileException;
use Ilimurzin\Esia\Signer\Exceptions\NoSuchKeyFileException;
use Ilimurzin\Esia\Signer\Exceptions\NoSuchTmpDirException;
use Ilimurzin\Esia\Signer\Exceptions\SignFailException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

abstract class AbstractSignerPKCS7
{
    use LoggerAwareTrait;

    /**
     * SignerPKCS7 constructor.
     */
    public function __construct(
        /**
         * Path to the certificate
         */
        protected string $certPath,
        /**
         * Path to the private key
         */
        protected string $privateKeyPath,
        /**
         * Password for the private key
         */
        protected ?string $privateKeyPassword,
        /**
         * Temporary directory for message signing (must me writable)
         */
        protected string $tmpPath,
    ) {
        $this->logger = new NullLogger();
    }

    /**
     * @throws SignFailException
     */
    protected function checkFilesExists(): void
    {
        if (!file_exists($this->certPath)) {
            throw new NoSuchCertificateFileException('Certificate does not exist');
        }
        if (!is_readable($this->certPath)) {
            throw new CannotReadCertificateException('Cannot read the certificate');
        }
        if (!file_exists($this->privateKeyPath)) {
            throw new NoSuchKeyFileException('Private key does not exist');
        }
        if (!is_readable($this->privateKeyPath)) {
            throw new CannotReadPrivateKeyException('Cannot read the private key');
        }
        if (!file_exists($this->tmpPath)) {
            throw new NoSuchTmpDirException('Temporary folder is not found');
        }
        if (!is_writable($this->tmpPath)) {
            throw new NoSuchTmpDirException('Temporary folder is not writable');
        }
    }

    /**
     * Generate random unique string
     */
    protected function getRandomString(): string
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Url safe for base64
     */
    protected function urlSafe(string $string): string
    {
        return rtrim(strtr(trim($string), '+/', '-_'), '=');
    }
}
