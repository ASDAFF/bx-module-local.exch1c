<?php defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arModConf = include __DIR__ . '/../mod_conf.php';
$module_id = strtolower($arModConf['prefix']);

$aMenu = array(
    array(
        'parent_menu' => 'global_menu_store',
        'sort' => 400,
        'text' => "Синхронизация с 1С (Проект \"Коленка\")",
        'title' => "",
        'url' => $module_id . '_main.php',
        'items_id' => $module_id . '_main'
    )
);

return $aMenu;