<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (\CModule::IncludeModule('socialservices')) {
    $oAuthManager = new \CSocServAuthManager();
    $oAuthManager->Authorize('ilimurzin_esia');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
