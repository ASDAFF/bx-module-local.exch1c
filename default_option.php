<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arModConf = include __DIR__ . '/mod_conf.php';

$local_exch1c_default_option = array(
    $arModConf['name'] . '_FTP_PATH' => "",
    $arModConf['name'] . '_FTP_USER' => "",
    $arModConf['name'] . '_FTP_PASS' => "",
    $arModConf['name'] . '_FILE_PREFIX_IMPORT' => "1ctow_",
    $arModConf['name'] . '_FILE_PREFIX_EXPORT' => "wto1c_",
    $arModConf['name'] . '_FTP_EXCH_DIR' => "/1c",
    $arModConf['name'] . '_SERVER_EXCH_DIR' => "/upload/local.exch1c",
    $arModConf['name'] . '_FILE_NAME_USERS' => "users.xml",
    $arModConf['name'] . '_FILE_NAME_STORES' => "stores.xml",
    $arModConf['name'] . '_FILE_NAME_ORDERS' => "orders.xml",
);
?>