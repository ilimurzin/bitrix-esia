<?php

declare(strict_types=1);

namespace Ilimurzin\Esia;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;

final class SocialService extends \CSocServAuth
{
    public function GetSettings(): array
    {
        return [
            [
                'ilimurzin_esia_client_id',
                Loc::getMessage('ILIMURZIN_ESIA_CLIENT_ID'),
                '',
                ['text', 40],
            ],
            [
                'ilimurzin_esia_portal_url',
                Loc::getMessage('ILIMURZIN_ESIA_PORTAL_URL'),
                'https://esia.gosuslugi.ru/',
                ['text', 40],
            ],
            [
                'note' => Loc::getMessage('ILIMURZIN_ESIA_PORTAL_URL_INSTRUCTION', [
                    '#URL#' => Esia::getCallbackUri(),
                ]),
            ],
            [
                'ilimurzin_esia_signer',
                Loc::getMessage('ILIMURZIN_ESIA_SIGNER'),
                '',
                [
                    'selectbox',
                    [
                        'openssl' => 'OpenSSL',
                        'openssl_cli' => 'Консольный OpenSSL',
                        'cp' => 'КриптоПро',
                        'cp_cli' => 'Консольный КриптоПро',
                    ],
                ],
            ],
            [
                'note' => Loc::getMessage('ILIMURZIN_ESIA_SIGNER_INSTRUCTION'),
            ],
            [
                'ilimurzin_esia_cert_path',
                Loc::getMessage('ILIMURZIN_ESIA_CERT_PATH'),
                '',
                ['text', 40],
            ],
            [
                'ilimurzin_esia_private_key_path',
                Loc::getMessage('ILIMURZIN_ESIA_PRIVATE_KEY_PATH'),
                '',
                ['text', 40],
            ],
            [
                'ilimurzin_esia_private_key_password',
                Loc::getMessage('ILIMURZIN_ESIA_PRIVATE_KEY_PASSWORD'),
                '',
                ['password', 40],
            ],
            [
                'note' => Loc::getMessage('ILIMURZIN_ESIA_OPENSSL_INSTRUCTION'),
            ],
            [
                'ilimurzin_esia_cryptcp_path',
                Loc::getMessage('ILIMURZIN_ESIA_CRYPTCP_PATH'),
                '/opt/cprocsp/bin/amd64/cryptcp',
                ['text', 40],
            ],
            [
                'ilimurzin_esia_cert_thumbprint',
                Loc::getMessage('ILIMURZIN_ESIA_CERT_THUMBPRINT'),
                '',
                ['text', 40],
            ],
            [
                'ilimurzin_esia_cert_pin',
                Loc::getMessage('ILIMURZIN_ESIA_CERT_PIN'),
                '',
                ['password', 40],
            ],
            [
                'note' => Loc::getMessage('ILIMURZIN_ESIA_CP_INSTRUCTION'),
            ],
        ];
    }

    public function CheckSettings(): bool
    {
        if (
            self::GetOption('ilimurzin_esia_signer') === 'openssl' ||
            self::GetOption('ilimurzin_esia_signer') === 'openssl_cli'
        ) {
            return self::GetOption('ilimurzin_esia_cert_path') && self::GetOption('ilimurzin_esia_private_key_path');
        }

        if (self::GetOption('ilimurzin_esia_signer') === 'cp') {
            return (bool) self::GetOption('ilimurzin_esia_cert_thumbprint');
        }

        if (self::GetOption('ilimurzin_esia_signer') === 'cp_cli') {
            return self::GetOption('ilimurzin_esia_cryptcp_path') && self::GetOption('ilimurzin_esia_cert_thumbprint');
        }

        return false;
    }

    public function GetFormHtml($arParams): array|string
    {
        if ($arParams['FOR_INTRANET']) {
            return [
                'ON_CLICK' => 'onclick="' . $this->GetOnClickJs($arParams) . '"',
            ];
        }

        return '<a href="javascript:void(0)" onclick="' . $this->GetOnClickJs($arParams)
            . '"><img alt="" src="/bitrix/images/ilimurzin.esia/esia_button.svg"></a>';
    }

    public function GetOnClickJs(array $arParams): string
    {
        $redirectUrl = sprintf(
            '/bitrix/tools/ilimurzin.esia/redirect.php?%s',
            http_build_query([
                'check_key' => \CSocServAuthManager::GetUniqueKey(),
                'backurl' => $arParams['BACKURL'] ?? '/',
            ]),
        );
        $escapedRedirectUrl = \CUtil::JSEscape($redirectUrl);

        return "top.location.href = '$escapedRedirectUrl';";
    }

    public function Authorize(): void
    {
        if (!isset($_GET['state'])) {
            $backurl = new Uri('/');
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => SOCSERV_AUTHORISATION_ERROR,
            ]);
            LocalRedirect($backurl);
        }

        $payload = State::getPayload($_GET['state']);

        if (!$payload['backurl']) {
            $backurl = new Uri('/');
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => SOCSERV_AUTHORISATION_ERROR,
            ]);
            LocalRedirect($backurl);
        }

        if (isset($_GET['error_description'])) {
            $backurl = new Uri($payload['backurl']);
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => SOCSERV_AUTHORISATION_ERROR,
            ]);
            LocalRedirect($backurl);
        }

        if (!isset($_GET['code'])) {
            $backurl = new Uri($payload['backurl']);
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => SOCSERV_AUTHORISATION_ERROR,
            ]);
            LocalRedirect($backurl);
        }

        $code = $_GET['code'];

        try {
            $user = Esia::getUser($code);
        } catch (\Throwable) {
            $backurl = new Uri($payload['backurl']);
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => SOCSERV_AUTHORISATION_ERROR,
            ]);
            LocalRedirect($backurl);
        }

        $result = $this->AuthorizeUser([
            'EXTERNAL_AUTH_ID' => 'ilimurzin_esia',
            'XML_ID' => $user->oid,
            'LOGIN' => $user->email,
            'EMAIL' => $user->email,
            'NAME' => $user->firstName,
            'LAST_NAME' => $user->lastName,
            'SECOND_NAME' => $user->patronymic,
        ]);

        if ($result !== true) {
            $backurl = new Uri($payload['backurl']);
            $backurl->addParams([
                'auth_service_id' => 'ilimurzin_esia',
                'auth_service_error' => $result,
            ]);
            LocalRedirect($backurl);
        }

        LocalRedirect($payload['backurl']);
    }
}
