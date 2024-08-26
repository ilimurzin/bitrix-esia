<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!\CSocServAuthManager::CheckUniqueKey()) {
    LocalRedirect($_GET['backurl'], true);
}

\Bitrix\Main\Loader::requireModule('ilimurzin.esia');

$state = \Ilimurzin\Esia\State::create([
    'backurl' => $_GET['backurl'],
]);

$url = \Ilimurzin\Esia\Esia::buildUrl($state);

LocalRedirect($url, true);
