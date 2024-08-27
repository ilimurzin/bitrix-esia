<?php

declare(strict_types=1);

namespace Ilimurzin\Esia;

use Bitrix\Main\Application;

final class State
{
    public static function create(array $payload): string
    {
        $state = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
        );

        Application::getInstance()->getLocalSession('ilimurzin_esia')->set(
            $state,
            $payload,
        );

        return $state;
    }

    public static function getPayload(string $state): array
    {
        $payload = Application::getInstance()->getLocalSession('ilimurzin_esia')->get($state);

        if (!$payload) {
            throw new \LogicException('State not set');
        }

        return $payload;
    }
}
