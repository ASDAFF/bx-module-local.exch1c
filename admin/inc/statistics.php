<?
/**
 * @var array $arModConf
 */

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserStatistic;
use \Local\Exch1c\SyncerStatistic;

if ($request->isPost()) {
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

        $fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_STATISTICS');
        $filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
        $filePrefixExport = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_EXPORT');
        $dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');


        $ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
        $xmlParser = new ParserStatistic($fileName, $filePrefix, $filePrefixExport);

        $ftpClient->setParser($xmlParser);

        $syncer = new SyncerStatistic();

        if ($request->getPost('rqType') === 'statisticDoImport') {
            $arResult = $syncer->import($ftpClient);

            if($arResult) {
                echo 'Получено записей: ' . $arResult['CNT'] . '<br>';
                echo 'Создано заказов: ' . $arResult['CNT_INS'] . '<br>';
                echo 'Обновлено заказов: ' . $arResult['CNT_UPD'] . '<br>';
                echo 'Записей с ошибками: ' . $arResult['CNT_ERROR'] . '<br>';
                echo '<br><br>';
            }
        }

    } catch (Exception $e) {
        echo 'произошла ошибка: ' . $e->getMessage() . '[File: ' . $e->getFile() . '; Line: ' . $e->getLine() .']';
        \Bitrix\Main\Diag\Debug::dump($e->getTrace());
        echo '<br><br>';

        \Local\Exch1c\Tables\SyncHistoryTable::add([
            'name' => 'statistic',
            'operation' => 'prepare',
            'result' => 'error',
            'msg' => $e->getMessage(),
        ]);
    }
}
?>

<form method="post" action="">
    <input type="hidden" name="rqType" value="statisticDoImport">
    <input type="submit" value="Получить статистику заказов из 1С">
</form>
<br>
<br>
