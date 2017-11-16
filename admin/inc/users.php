<?
/**
 * @var array $arModConf
 */

use \Local\Exch1c\FtpClient;
use \Local\Exch1c\ParserUser;
use \Local\Exch1c\SyncerUser;

if ($request->isPost()) {
    $request = \Bitrix\Main\Context::getCurrent()->getRequest();

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
    $filePrefixExport = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_FILE_PREFIX_EXPORT');
    $dirServer = \Bitrix\Main\Config\Option::get($module_id, $arModConf['name'] . '_SERVER_EXCH_DIR');

    $ftpClient = new FtpClient($ftp['path'], $ftp['user'], $ftp['pass'], $ftp['dir'], $dirServer);
    $xmlParser = new ParserUser($fileName, $filePrefix, $filePrefixExport);

    $ftpClient->setParser($xmlParser);

    $syncer = new SyncerUser();

    if ($request->getPost('rqType') === 'userDoImport') {
        $arData = $ftpClient->syncFile();
        $arResult = $syncer->import($arData);

        if($arResult) {
            echo 'Получено записей: ' . $arResult['CNT'] . '<br>';
            echo 'Создано клиентов: ' . $arResult['CNT_INS'] . '<br>';
            echo 'Обновлено клиентов: ' . $arResult['CNT_UPD'] . '<br>';
            echo 'Записей с ошибками: ' . $arResult['CNT_ERROR'] . '<br>';
            echo '<br><br>';
        }
    }

    if ($request->getPost('rqType') === 'userDoExport') {
        $arResult = $syncer->export($ftpClient);

        if($arResult) {
            echo 'Выгружено клиентов: ' . $arResult['CNT'] . '<br><br>';
        } else {
            echo 'Нет клиентов для экспорта в 1С или ранее выгруженный файл еще существует на сервере';
            echo '<br><br>';
        }
    }
}
?>

<form method="post" action="">
    <input type="hidden" name="rqType" value="userDoImport">
    <input type="submit" value="Получить пользователей из 1С">
</form>
<br>
<br>
<form method="post" action="">
    <input type="hidden" name="rqType" value="userDoExport">
    <input type="submit" value="Передать пользователей в 1С">
</form>
