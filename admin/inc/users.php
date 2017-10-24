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
];


use Local\Exch1c\FtpClient;

$ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass']);

echo $ftpClient->test();

\Bitrix\Main\Diag\Debug::dump($ftp);
?>