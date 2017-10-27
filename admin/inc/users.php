<?
/**
 * @var array $arModConf
 */

$module_id = strtolower($arModConf['name']);
$module_prefix = str_replace('.', '_', $arModConf['name']);

$ftp = [
    'path' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PATH'),
    'user' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_USER'),
    'pass' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PASS'),
    'dir' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_EXCH_DIR'),
];


use Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserUser;

$fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_USERS');
$filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
$dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');

$ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);

\Bitrix\Main\Diag\Debug::dump($fileName);
\Bitrix\Main\Diag\Debug::dump($filePrefix);
\Bitrix\Main\Diag\Debug::dump($ftp);

$parserUser = new ParserUser($fileName, $filePrefix);

\Bitrix\Main\Diag\Debug::dump($parserUser->getFileName());

$ftpClient->setParser($parserUser);

$file = $ftpClient->syncFile();

//\Bitrix\Main\Diag\Debug::dump($file);
?>