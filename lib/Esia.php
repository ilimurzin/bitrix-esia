<?php

declare(strict_types=1);

namespace Ilimurzin\Esia;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Uri;
use Ilimurzin\Esia\Signer\CliCryptoProSigner;
use Ilimurzin\Esia\Signer\CliSignerPKCS7;
use Ilimurzin\Esia\Signer\CryptoProSigner;
use Ilimurzin\Esia\Signer\SignerInterface;
use Ilimurzin\Esia\Signer\SignerPKCS7;
use Psr\Log\NullLogger;

final class Esia
{
    public static function getCallbackUri(): Uri
    {
        return (new Uri('/bitrix/tools/ilimurzin.esia/callback.php'))->toAbsolute();
    }

    public static function buildUrl(string $state): string
    {
        return self::getOpenId()->buildUrl($state);
    }

    private static function getOpenId(): OpenId
    {
        return new OpenId(
            self::getConfig(),
            new HttpClient(),
            new NullLogger(),
            self::getSigner()
        );
    }

    private static function getConfig(): Config
    {
        return new Config([
            'clientId' => \CSocServAuth::GetOption('ilimurzin_esia_client_id'),
            'redirectUrl' => self::getCallbackUri(),
            'portalUrl' => \CSocServAuth::GetOption('ilimurzin_esia_portal_url'),
            'scope' => ['fullname', 'email'],
        ]);
    }

    private static function getSigner(): SignerInterface
    {
        $signer = \CSocServAuth::GetOption('ilimurzin_esia_signer');

        if ($signer === 'openssl') {
            return new SignerPKCS7(
                \CSocServAuth::GetOption('ilimurzin_esia_cert_path'),
                \CSocServAuth::GetOption('ilimurzin_esia_private_key_path'),
                \CSocServAuth::GetOption('ilimurzin_esia_private_key_password'),
                sys_get_temp_dir(),
            );
        }

        if ($signer === 'openssl_cli') {
            return new CliSignerPKCS7(
                \CSocServAuth::GetOption('ilimurzin_esia_cert_path'),
                \CSocServAuth::GetOption('ilimurzin_esia_private_key_path'),
                \CSocServAuth::GetOption('ilimurzin_esia_private_key_password'),
                sys_get_temp_dir(),
            );
        }

        if ($signer === 'cp') {
            return new CryptoProSigner(
                \CSocServAuth::GetOption('ilimurzin_esia_cert_thumbprint'),
                \CSocServAuth::GetOption('ilimurzin_esia_cert_pin'),
            );
        }

        if ($signer === 'cp_cli') {
            return new CliCryptoProSigner(
                \CSocServAuth::GetOption('ilimurzin_esia_cryptcp_path'),
                \CSocServAuth::GetOption('ilimurzin_esia_cert_thumbprint'),
                \CSocServAuth::GetOption('ilimurzin_esia_cert_pin'),
                sys_get_temp_dir(),
            );
        }

        throw new \RuntimeException('Unsupported signer selected');
    }

    public static function getUser(string $code): User
    {
        $openId = self::getOpenId();

        $token = $openId->getToken($code);

        $oid = $openId->getConfig()->getOid();

        if (!$oid) {
            throw new \RuntimeException('`oid` is required');
        }

        $person = $openId->getPersonInfo();
        $contacts = $openId->getContactInfo();

        $email = '';

        foreach ($contacts as $contact) {
            if ($contact['type'] === 'EML') {
                $email = $contact['value'];
            }
        }

        if (!$email) {
            throw new \RuntimeException('`email` is required');
        }

        return new User(
            $oid,
            $token,
            $email,
            $person['firstName'],
            $person['lastName'],
            $person['middleName'] ?? null
        );
    }
}
