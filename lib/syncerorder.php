<?php

namespace Local\Exch1c;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Order;

class SyncerOrder implements ISyncer
{
    private $siteId;

    public function __construct()
    {
        $rsSites = \CSite::GetList($by="sort", $order="desc", ['ACTIVE' => 'Y']);
        $arSite = $rsSites->Fetch();
        $this->siteId = $arSite['ID'];
    }

    private function _create($arUser)
    {

        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

//        $arFields = [
//        ];
//
//        if (!$ID) {
////            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
//            Debug::dump($user->LAST_ERROR);
//            return false;
//        }
//
//        return [
//            'ID' => $ID,
//            'LOGIN' => $arFields['LOGIN'],
//            'EMAIL' => $arFields['EMAIL'],
//            'PASSWORD' => $arFields['PASSWORD'],
//        ];
    }

    private function _update($arUser)
    {
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

//        $arFields = [
//
//        ];
//
//        if (!$userID) {
////            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
//            Debug::dump($user->LAST_ERROR);
//            return false;
//        }
//
//        return [
//            'ID' => $userID,
//            'LOGIN' => $arFields['LOGIN'],
//            'EMAIL' => $arFields['EMAIL'],
//            'PASSWORD' => $arFields['PASSWORD'],
//        ];
    }

    public function import(FtpClient $ftpClient)
    {

        $arResultMsg = [
            'type' => 'success',
            'msg' => 'ok',
        ];

        $arData = $ftpClient->syncFile();

        $arResult = [
            'CNT' => 0,
            'CNT_INS' => 0,
            'CNT_UPD' => 0,
            'CNT_ERROR' => 0,
        ];

        if (!$arData) {
            return $arResult;
        }

        $arNewRows = [];

        foreach ($arData['OBJECTS'] as $arObj) {
            Debug::dump($arObj);
        }

        // Удаляем файл на FTP
        //$ftpClient->rmFtpImportFile();

        // лог
        $arResultMsg['msg'] = 'Всего записей: ' . $arResult['CNT']
            . '; создано: ' . $arResult['CNT_INS']
            . '; обновлено: ' . $arResult['CNT_UPD']
            . '; с ошибками: ' . $arResult['CNT_ERROR'] . ';';

        Tables\SyncHistoryTable::add([
            'name' => 'order',
            'operation' => 'import',
            'result' => $arResultMsg['type'],
            'msg' => $arResultMsg['msg'],
        ]);

        return $arResult;
    }

    public function export(FtpClient $ftpClient)
    {
        $arResultMsg = [
            'type' => 'success',
            'msg' => 'ok',
        ];

        // Проверяем наличие предыдущего файла
        if($ftpClient->ftpFileExists($ftpClient->getParser()->getFileNameExport())) {
            return false;
        }

        // Собираем всех заказы для передачи
        $arData = $this->getDataForExport();

        if(count($arData) <= 0) {
            return false;
        }

        // Создаем XML код
        $xml = $ftpClient->getParser()->makeXml($arData);

        $xml->saveXml($ftpClient->getServerDir() . $ftpClient->getParser()->getFileNameExport());

        // Передаем файл на FTP
        if ($ftpClient->uploadFile()) {
            // Сбросим флаг необходимости передачи в 1С
            $this->unCheckExported($arData);
        } else {
            $arResultMsg = [
                'type' => 'error',
                'msg' => 'ftp upload error',
            ];
        }

        // лог
        $arResultMsg['msg'] = 'Передано записей: ' . count($arData);

        Tables\SyncHistoryTable::add([
            'name' => 'order',
            'operation' => 'export',
            'result' => $arResultMsg['type'],
            'msg' => $arResultMsg['msg'],
        ]);

        return [
            'CNT' => count($arData),
        ];
    }

    /**
     * Получение кодов заказов для экспорта (с заполненным свойством "EXPORT_DO" = "Y")
     * @return array
     */
    private function getOrderForExportIDs()
    {
        $arOrder = [];
        $arFilter = [
            "CODE" => 'EXPORT_DO_UR',
            "VALUE" => 'Y',
        ];

        $arSelect = [
            'ORDER_ID',
            'CODE',
            'NAME',
            'VALUE',
        ];

        $arIDs = [];
        $dbRes = \CSaleOrderPropsValue::GetList($arOrder, $arFilter, false, false, $arSelect);

        while ($arRes = $dbRes->GetNext()) {
            $arIDs[] = $arRes['ORDER_ID'];
        }

        if(count($arIDs) <= 0 ) {
            $arOrder = [];
            $arFilter = [
                "CODE" => 'EXPORT_DO',
                "VALUE" => 'Y',
            ];

            $arSelect = [
                'ORDER_ID',
                'CODE',
                'NAME',
                'VALUE',
            ];

            $arIDs = [];
            $dbRes = \CSaleOrderPropsValue::GetList($arOrder, $arFilter, false, false, $arSelect);

            while ($arRes = $dbRes->GetNext()) {
                $arIDs[] = $arRes['ORDER_ID'];
            }
        }

        return $arIDs;
    }

    /**
     * Получение заказов для экспорта
     * @return array
     */
    private function getOrdersForExport($arIDs)
    {
        $arOrder = [];
        $arFilter = [
            "ID" => $arIDs,
        ];
        $arSelect = [
            'ID',
            'XML_ID',
            'ACCOUNT_NUMBER',
            'DATE_INSERT',
            'PRICE',
            'COMMENTS',
            'USER_ID',
            'USER_LOGIN',
        ];

        $dbRes = \CSaleOrder::GetList($arOrder, $arFilter, false, false, $arSelect);

        $arData = [];
        while ($arRow = $dbRes->GetNext()) {
            $arData[] = $arRow;
        }

        return $arData;
    }

    /**
     * Получение клиентов по ID
     * @return array
     */
    private function getUsers($arIDs) {

        $by = "timestamp_x";
        $order = "desc";

        $arFilter = [
            "ID" => implode(' | ', $arIDs),
        ];

        $arSelect = [
            'ID',
            'LOGIN',
        ];

        $dbRes = \CUser::GetList($by, $order, $arFilter, ['FIELDS' => $arSelect]);
        $arData = [];
        while ($arRes = $dbRes->GetNext()) {
            $arData[$arRes["ID"]] = $arRes["LOGIN"];
        }

        return $arData;
    }

    /**
     * Получение значений свойств заказов
     * @return array
     */
    private function getOrderProps($arIDs, $arPropCodes = []) {
        $arOrder = [];

        $arFilter = [
            "ORDER_ID" => $arIDs,
            "CODE" => $arPropCodes,
        ];

        $arSelect = [
            'ORDER_ID',
            'CODE',
            'NAME',
            'VALUE',
        ];

        $arProps = [];
        $dbRes = \CSaleOrderPropsValue::GetList($arOrder, $arFilter, false, false, $arSelect);
        while ($arRes = $dbRes->GetNext()) {
            $key = $arRes["ORDER_ID"] . $arRes["CODE"];
            $arProps[$key] = $arRes["VALUE"];
        }

        return $arProps;
    }

    /**
     * Получение товаров заказов
     * @return array
     */
    private function getOrderProds($arIDs) {
        $arOrder = [];
        $arFilter = [
            "ORDER_ID" => $arIDs,
        ];

        $arSelect = [
            'ID',
            'ORDER_ID',
            'NAME',
            'QUANTITY',
            'PRICE',
            'PRODUCT_XML_ID',
        ];

        $dbRes = \CSaleBasket::GetList($arOrder, $arFilter, false, false, $arSelect);

        $arItems = [];
        while ($arRes = $dbRes->GetNext()) {
            $arItems[$arRes['ORDER_ID']][] = $arRes;
        }

        return $arItems;
    }

    /**
     * Получение данных по заказам и товарам для экспорта
     * @return array
     */
    protected function getDataForExport()
    {
        // Найдем коды заказов с заполненным свойством "EXPORT_DO"
        $arZakIDs = $this->getOrderForExportIDs();

        // Получим заказы для экспорта
        $arZaks = $this->getOrdersForExport($arZakIDs);

        $arUserIDs = [];
        foreach ($arZaks as $arZak) {
            $arUserIDs[] = $arZak['USER_ID'];
        }

        // Получаем клиентов заказа
        $arClients = $this->getUsers($arUserIDs);

        // Получаем свойства заказа
        $arPropCodes = [
            'EXCH_STATUS',
            'EXPORT_DO',
            'IS_IMPORTED',
            'EDIT_REQUEST_DT',
            'EDIT_RESPONS_DT',
            'EXT_STATUS',

            'EXCH_STATUS_UR',
            'EXPORT_DO_UR',
            'IS_IMPORTED_UR',
            'EDIT_REQUEST_DT_UR',
            'EDIT_RESPONS_DT_UR',
            'EXT_STATUS_UR',

            'EMAIL'
        ];

        $arZakProps = $this->getOrderProps($arZakIDs, $arPropCodes);

        // Получаем товары заказа
        $arZakProds = $this->getOrderProds($arZakIDs);

        // итоговая сборка данных
        foreach ($arZaks as $key => $arZak) {
            $arOrderProps = [];
            foreach ($arPropCodes as $arPropCode) {
                $propKey = $arZak["ID"] . $arPropCode;
                $arOrderProps[$arPropCode] = isset($arZakProps[$propKey]) ? $arZakProps[$propKey] : '';
            }

            $arZaks[$key]['USER_LOGIN'] = isset($arClients[$arZak["USER_ID"]]) ? $arClients[$arZak["USER_ID"]] : '';
            $arZaks[$key]['PROPS'] = $arOrderProps;
            $arZaks[$key]['ITEMS'] = $arZakProds[$arZak["ID"]];
        }

        return $arZaks;
    }

    /**
     * Снимаем флаг 'EXPORT_DO' на заказах
     * @return boolean
     */
    public function unCheckExported($arData) {
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

        foreach($arData as $arOrder) {
            $order = \Bitrix\Sale\Order::load($arOrder['ID']);

            $propertyCollection = $order->getPropertyCollection();

            /** @var \Bitrix\Sale\PropertyValue $obProp */
            foreach ($propertyCollection as $obProp) {
                $arProp = $obProp->getProperty();

                switch ($arProp["CODE"]) {
                    case "EXPORT_DO":
                    case "EXPORT_DO_UR":
                        $obProp->setValue('NNN');
                        break;

                    case "EDIT_REQUEST_DT":
                    case "EDIT_REQUEST_DT_UR":
                        $obProp->setValue($expDate);
                        break;

                    default:
                        continue;
                }

            }

            $res = $order->save();
            if (!$res->isSuccess()) {
                Debug::dump($res->getErrorMessages());
            }
        }

        return true;
    }

    protected function sendEmailForNewUsers($arUsers) {

        $arModConf = include __DIR__ . '/../mod_conf.php';

        foreach ($arUsers as $arUser) {
            $tmplName = \Bitrix\Main\Config\Option::get(strtolower($arModConf['name']), $arModConf['name'].'_EMAIL_TMPL_REGCONFIRM');
            \CEvent::Send($tmplName, $this->siteId, $arUser);
        }
    }

}