<?php

declare(strict_types=1);

namespace Ilimurzin\Esia\EventHandlers;

final class SocialServicesEventHandlers
{
    public static function onAuthServicesBuildList(): array
    {
        \CJSCore::RegisterExt('ilimurzin.esia', [
            'css' => '/bitrix/css/ilimurzin.esia/styles.css',
        ]);
        \CJSCore::Init(['ilimurzin.esia']);

        return [
            'ID' => 'ilimurzin_esia',
            'CLASS' => 'Ilimurzin\Esia\SocialService',
            'NAME' => 'Госуслуги',
            'ICON' => 'ilimurzin-esia-icon',
        ];
    }
}
