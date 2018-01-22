<?php

namespace Local\Exch1c;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

class SyncerStore implements ISyncer
{
    private $siteId;

    public function __construct()
    {
        $rsSites = \CSite::GetList($by="sort", $order="desc", ['ACTIVE' => 'Y']);
        $arSite = $rsSites->Fetch();
        $this->siteId = $arSite['ID'];
    }

    public function import(FtpClient $ftpClient)
    {
        // проверим что нет ранее запущенного импорта
        $fileFlagPath = $_SERVER["DOCUMENT_ROOT"] . '/IS_IMPORT_STORES';

        if (file_exists($fileFlagPath)) {
            $lastDate = strtotime(file_get_contents($fileFlagPath));

            // проверка в случае сбоя при удалении файла-флага
            // пытаемся запустить выгрузку, если от предыдущей прошло больше часа
            // или если в файле ошибочно проставлено время больше текущего
            if (time() > $lastDate + 60*60 || $lastDate > time()) {
                unlink($fileFlagPath);
            } else {
                return false;
            }

        }

        // создадим файл-флаг текущей выгрузки
        file_put_contents($fileFlagPath, date('Y-m-d H:i:s'));

        if(!\Bitrix\Main\Loader::includeModule('iblock')
            || !\Bitrix\Main\Loader::includeModule('catalog')) {

            throw new \Exception('ошибка подключения модулей');
        }

        $arResultMsg = [
            'type' => 'success',
            'msg' => 'ok',
        ];

        $arData = $ftpClient->syncFile();

        $arResult = [
            'CNT' => 0,
            'CNT_EXIST' => 0,
            'CNT_NO' => 0,
            'CNT_INS' => 0,
            'CNT_UPD' => 0,
            'CNT_ERROR' => 0,
        ];

        if (count($arData['CODES']) <= 0) {

            // Удаляем файл на FTP
            $ftpClient->rmFtpImportFile();

            // удалим файл-флаг статуса выгрузки
            unlink($fileFlagPath);

            return $arResult;
        }

//        var_dump($arData);

        // реализуем логику импорта

        // получим все товары. участвующие в обновлении
        $arOrder = [];
        // TODO: константы в модулях, связанные с основным кодом - не комильфо
        $arFilter = ['IBLOCK_ID' => IBID_CATALOG, 'XML_ID' => $arData['CODES']];
        $arSelect = ['ID', 'XML_ID', 'NAME'];
        $dbItems = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $obProduct = new \CCatalogProduct();
        $cntItems = 0;
        while($arItem = $dbItems->GetNext()) {
            $cntItems++;

            $success = $obProduct->Update($arItem['ID'], ['QUANTITY' => $arData['OBJECTS'][$arItem['XML_ID']]['ИТОГО']]);

            if ($success) {
                $arResult['CNT_UPD']++;
            } else {
                $arResult['CNT_ERROR']++;
            }
        }

        $arResult['CNT'] = count($arData['CODES']);
        $arResult['CNT_EXIST'] = $cntItems;
        $arResult['CNT_NO'] = $arResult['CNT'] - $arResult['CNT_EXIST'];

        // Удаляем файл на FTP
        $ftpClient->rmFtpImportFile();

        // лог
        $arResultMsg['msg'] = 'Всего записей: ' . $arResult['CNT']
            . '; найдено: ' . $arResult['CNT_EXIST']
            . '; не найдено: ' . $arResult['CNT_NO']
            . '; обновлено: ' . $arResult['CNT_UPD']
            . '; с ошибками: ' . $arResult['CNT_ERROR'] . ';';

        Tables\SyncHistoryTable::add([
            'name' => 'store',
            'operation' => 'import',
            'result' => $arResultMsg['type'],
            'msg' => $arResultMsg['msg'],
        ]);

        if($arResult['CNT_NO'] < 0) {

            $strRes = $arData['CODES'];

            if(is_array($arData['CODES'])) {
                $strRes = implode('; ', $arData['CODES']);
            }

            Tables\SyncHistoryTable::add([
                'name' => 'store',
                'operation' => 'import-bug',
                'result' => $arResultMsg['type'],
                'msg' => $strRes,
            ]);

            copy($_SERVER['DOCUMENT_ROOT'].'/upload/local.exch1c/1ctow_stores.xml', $_SERVER['DOCUMENT_ROOT'].'/upload/local.exch1c/1ctow_stores-bug.xml');
        }

        // удалим файл-флаг статуса выгрузки
        unlink($fileFlagPath);

        return $arResult;
    }

    public function export(FtpClient $ftpClient)
    {
        throw new \Exception('Экспорт остатков не предполагается');
    }

    public function unCheckExported($arData) {
        return true;
    }
}