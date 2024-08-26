<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

class ilimurzin_esia extends \CModule
{
    public $MODULE_ID = 'ilimurzin.esia';

    public function __construct()
    {
        Loc::loadMessages(__FILE__);

        $arModuleVersion = null;

        include __DIR__ . '/version.php';

        if (isset($arModuleVersion) && is_array($arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('ILIMURZIN_ESIA_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ILIMURZIN_ESIA_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('ILIMURZIN_ESIA_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('ILIMURZIN_ESIA_PARTNER_URI');
    }

    public function DoInstall(): void
    {
        $this->InstallDB();
        $this->InstallFiles();
        $this->InstallEvents();
    }

    public function DoUninstall(): void
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();
    }

    public function InstallDB(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function UnInstallDB(): void
    {
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallFiles(): void
    {
        CopyDirFiles(__DIR__ . '/css', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/ilimurzin.esia', true, true);
        CopyDirFiles(__DIR__ . '/images', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/images/ilimurzin.esia', true, true);
        CopyDirFiles(__DIR__ . '/tools', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tools/ilimurzin.esia', true, true);
    }

    public function UnInstallFiles(): void
    {
        DeleteDirFilesEx('bitrix/css/ilimurzin.esia');
        DeleteDirFilesEx('bitrix/images/ilimurzin.esia');
        DeleteDirFilesEx('bitrix/tools/ilimurzin.esia');
    }

    public function InstallEvents(): void
    {
        /** @see \Ilimurzin\Esia\EventHandlers\SocialServicesEventHandlers::onAuthServicesBuildList() */
        EventManager::getInstance()->registerEventHandler(
            'socialservices',
            'OnAuthServicesBuildList',
            $this->MODULE_ID,
            'Ilimurzin\\Esia\\EventHandlers\\SocialServicesEventHandlers',
            'onAuthServicesBuildList'
        );
    }

    public function UnInstallEvents(): void
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'socialservices',
            'OnAuthServicesBuildList',
            $this->MODULE_ID,
            'Ilimurzin\\Esia\\EventHandlers\\SocialServicesEventHandlers',
            'onAuthServicesBuildList'
        );
    }
}
