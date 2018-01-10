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

            if ($lastDate + 60*60 >= time() && $lastDate - 60*60 <= time()) {
                return false;
            } else {
                unlink($fileFlagPath);
            }

        }

        // создадим файл-флаг текущей выгрузки
        file_put_contents($fileFlagPath, date('Y.d.m H:i:s'));

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

        if (!$arData) {
            // удалим файл-флаг статуса выгрузки
            unlink($fileFlagPath);

            return $arResult;
        }

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

            $success = $obProduct->Update($arItem['ID'], ['QUANTITY' => $arData['OBJECTS'][$arItem['XML_ID']]['Доступно']]);

            if ($success) {
                $arResult['CNT_UPD']++;
            } else {
                $arResult['CNT_ERROR']++;
            }
        }

        $arResult['CNT'] = count($arData['CODES']);
        $arResult['CNT_EXIST'] = $cntItems;
        $arResult['CNT_NO'] = count($arData['CODES']) - $cntItems;

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