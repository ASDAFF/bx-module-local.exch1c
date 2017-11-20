<?
/**
 * @var array $arModConf
 */

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserStore;
use \Local\Exch1c\SyncerStore;

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

        $fileName = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_NAME_STORES');
        $filePrefix = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_IMPORT');
        $filePrefixExport = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_EXPORT');
        $dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');


        $ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
        $xmlParser = new ParserStore($fileName, $filePrefix, $filePrefixExport);

        $ftpClient->setParser($xmlParser);

        $syncer = new SyncerStore();

        if ($request->getPost('rqType') === 'storeDoImport') {
            $arResult = $syncer->import($ftpClient);

            if($arResult) {
                echo 'Всего записей: ' . $arResult['CNT']
                    . ';<br> найдено: ' . $arResult['CNT_EXIST']
                    . ';<br> не найдено: ' . $arResult['CNT_NO']
                    . ';<br> обновлено: ' . $arResult['CNT_UPD']
                    . ';<br> с ошибками: ' . $arResult['CNT_ERROR'] ;
                echo '<br><br>';
            }
        }

    } catch (Exception $e) {
        echo 'произошла ошибка: ' . $e->getMessage();
        echo '<br><br>';

        \Local\Exch1c\Tables\SyncHistoryTable::add([
            'name' => 'store',
            'operation' => 'prepare',
            'result' => 'error',
            'msg' => $e->getMessage(),
        ]);
    }
}
?>

<form method="post" action="">
    <input type="hidden" name="rqType" value="storeDoImport">
    <input type="submit" value="Получить остатки из 1С">
</form>
