<?php

declare(strict_types=1);

namespace Ilimurzin\Esia;

final class User
{
    public function __construct(
        public string $oid,
        public string $token,
        public string $email,
        public string $firstName,
        public string $lastName,
        public ?string $patronymic,
    ) {}
}
