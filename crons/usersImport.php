<?
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../../../');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

/**
 * @var array $arModConf
 */

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserUser;
use \Local\Exch1c\SyncerUser;

$module_id = strtolower($arModConf['name']);
$module_prefix = str_replace('.', '_', $arModConf['name']);

$ftp = [
    'path' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PATH'),
    'user' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_USER'),
    'pass' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PASS'),
    'dir' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_EXCH_DIR'),
];

$fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_USERS');
$filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
$dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');

$ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
$xmlParser = new ParserUser($fileName, $filePrefix);

$ftpClient->setParser($xmlParser);
$arData = $ftpClient->syncFile();

$syncer = new SyncerUser();
$syncer->import($arData);