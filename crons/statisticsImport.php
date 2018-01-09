<?
$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../../../../');

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define('CHK_EVENT', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

@set_time_limit(0);
@ignore_user_abort(true);

/**
 * @var array $arModConf
 */
$arModConf = include __DIR__ . '/../mod_conf.php';
// нужна для управления правами модуля
$module_id = strtolower($arModConf['name']);

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserStatistic;
use \Local\Exch1c\SyncerStatistic;
use \Bitrix\Main\Loader;

Loader::includeModule($module_id);

try {
    $request = \Bitrix\Main\Context::getCurrent()->getRequest();

    $module_id = strtolower($arModConf['name']);
    $module_prefix = str_replace('.', '_', $arModConf['name']);

    $ftp = [
        'path' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PATH'),
        'user' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_USER'),
        'pass' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_PASS'),
        'dir' => \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FTP_EXCH_DIR'),
    ];

    $fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_STORES');
    $filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
    $filePrefixExport = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_EXPORT');
    $dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');


    $ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
    $xmlParser = new ParserStatistic($fileName, $filePrefix, $filePrefixExport);

    $ftpClient->setParser($xmlParser);

    $syncer = new SyncerStatistic();

    $arResult = $syncer->import($ftpClient);

} catch (Exception $e) {
    echo 'произошла ошибка: ' . $e->getMessage();
    echo '<br><br>';

    \Local\Exch1c\Tables\SyncHistoryTable::add([
        'name' => 'order',
        'operation' => 'prepare',
        'result' => 'error',
        'msg' => $e->getMessage(),
    ]);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");