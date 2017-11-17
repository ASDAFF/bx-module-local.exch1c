<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arModConf = include __DIR__ . '/mod_conf.php';

$local_exch1c_default_option = array(
    $arModConf['name'] . '_FTP_PATH' => "",
    $arModConf['name'] . '_FTP_PORT' => "21",
    $arModConf['name'] . '_FTP_USER' => "",
    $arModConf['name'] . '_FTP_PASS' => "",
    $arModConf['name'] . '_FILE_PREFIX_IMPORT' => "1ctow_",
    $arModConf['name'] . '_FILE_PREFIX_EXPORT' => "wto1c_",
    $arModConf['name'] . '_FTP_EXCH_DIR' => "/1c",
    $arModConf['name'] . '_SERVER_EXCH_DIR' => "/upload/local.exch1c",
    $arModConf['name'] . '_FILE_NAME_USERS' => "users.xml",
    $arModConf['name'] . '_FILE_NAME_STORES' => "stores.xml",
    $arModConf['name'] . '_FILE_NAME_ORDERS' => "orders.xml",
    $arModConf['name'] . '_IB_CODE' => "LOCALEXCH1C_REGREQUEST",
    $arModConf['name'] . '_EMAIL_TMPL_REGCONFIRM' => "LOCALEXCH1C_REGCONFIRM",
    $arModConf['name'] . '_EMAIL_TMPL_REGREQUEST' => "LOCALEXCH1C_REGREQUEST",
    $arModConf['name'] . '_EMAIL_TMPL_EDITREQUEST' => "LOCALEXCH1C_EDITREQUEST",
);
?>