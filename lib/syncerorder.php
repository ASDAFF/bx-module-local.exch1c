<?php

namespace Local\Exch1c;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Sale\BasketPropertyItem;
use Bitrix\Sale\Internals\BasketPropertyTable;
use Bitrix\Sale\Order;
use Bitrix\Sale\PropertyValue;
use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Delivery;
use \Bitrix\Sale\PaySystem;

class SyncerOrder implements ISyncer
{
    private $siteId;

    public function __construct()
    {
        $rsSites = \CSite::GetList($by="sort", $order="desc", ['ACTIVE' => 'Y']);
        $arSite = $rsSites->Fetch();
        $this->siteId = $arSite['ID'];
    }

    private function _create($arData)
    {
        Loader::includeModule('sale');
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

//        Debug::dump($arData);

        // проверим наличие пользователя
        $arUser = \CUser::GetByLogin($arData['КодКлиента'])->Fetch();

        if(!$arUser) {
            Tables\SyncHistoryTable::add([
                'name' => 'order-create',
                'operation' => 'import',
                'result' => 'error',
                'msg' => 'Отсутствует клиент с логином ['.$arData['КодКлиента'].']',
            ]);

            return false;
        }

        // проверим наличие товаров
        $arXmlIds = [];
        $ar1CProds = [];
        foreach ($arData["Товары"] as $arProd) {
            $arXmlIds[] = $arProd['ИД'];
            $ar1CProds[$arProd['ИД']] = $arProd;
        }

        $arOrder = [];
        $arFilter = [
            'IBLOCK_ID' => IBID_CATALOG,
            'XML_ID' => $arXmlIds,
        ];
        $arSelect = ['IBLOCK_ID', 'ID', 'XML_ID', 'NAME'];
        $dbRes = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

        $arProds = [];
        while($arRes = $dbRes->GetNext()) {
//            Debug::dump($arRes);
            $arProds[] = $arRes;
        }

        if(count($arProds) <= 0) {
//            Debug::dump($arFilter);
//            Debug::dump("Товары не найдены");
            Tables\SyncHistoryTable::add([
                'name' => 'order-create',
                'operation' => 'import',
                'result' => 'error',
                'msg' => 'Отсутствуют товары в заказе',
            ]);
            return false;
        }

        // создаем заказ
        $currencyCode = CurrencyManager::getBaseCurrency();
        $userId = $arUser['ID'];

        $order = Order::create($this->siteId, $userId);
        $order->setPersonTypeId(2); // юр.лицо
        $order->setField('CURRENCY', $currencyCode);
        $order->setField('XML_ID', $arData['ИД']);
        if ($arData['Комментарий']) {
            $order->setField('USER_DESCRIPTION', $arData['Комментарий']); // Устанавливаем поля комментария покупателя
        }

        // создаем корзину и наполняем ее товарами
        $basket = Basket::create($this->siteId);

        foreach($arProds as $arProd) {
            $item = $basket->createItem('catalog', $arProd['ID']);

            $item->setFields(array(
                'QUANTITY' => $ar1CProds[$arProd['XML_ID']]['Количество'],
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => $this->siteId,
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                'CUSTOM_PRICE' => 'Y',
                'PRICE' => $ar1CProds[$arProd['XML_ID']]['Цена'],
                'PRODUCT_XML_ID' => $arProd['XML_ID'],
            ));
        }

        // привязываем корзину к заказу
        $order->setBasket($basket);
        $order->getDiscount()->calculate();

        // Создаём одну отгрузку
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();

        // устанавливаем способ доставки
        $deliveryType = 1;

        $service = Delivery\Services\Manager::getById($deliveryType);
        $shipment->setFields(array(
            'DELIVERY_ID' => $service['ID'],
            'DELIVERY_NAME' => $service['NAME'],
        ));

        // создаем список элементов отгрузки
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        $basketItems = $basket->getBasketItems();
        foreach($basketItems as $basketItem) {
            $shipmentItem = $shipmentItemCollection->createItem($basketItem);
            $shipmentItem->setQuantity($basketItem->getQuantity());
        }

        // Создаём оплату со способом #1
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem();

        $paySystemId = 1;
        $paySystemService = PaySystem\Manager::getObjectById($paySystemId);
        $payment->setFields(array(
            'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
            'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
        ));

        //\Bitrix\Main\Diag\Debug::dump($this->_arFields);
        // Устанавливаем свойства
        $propertyCollection = $order->getPropertyCollection();
        $phoneProp = $propertyCollection->getPhone();
        $phoneProp->setValue($arUser['PERSONAL_PHONE']);
        $nameProp = $propertyCollection->getPayerName();
        $nameProp->setValue($arUser['NAME']);
        $emailProp = $propertyCollection->getUserEmail();
        $emailProp->setValue($arUser['EMAIL']);

        // Сохраняем
        $order->doFinalAction(true);
        $result = $order->save();

        if(!$result->isSuccess()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
//                Debug::dump($errorMessage);

                Tables\SyncHistoryTable::add([
                    'name' => 'order-create',
                    'operation' => 'import',
                    'result' => 'error',
                    'msg' => $errorMessage,
                ]);
            }

            return false;
        }

        return true;

    }

    private function _updateOrderStatus(Order $order, array $arData) {

        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

        $propertyCollection = $order->getPropertyCollection();

        foreach ($propertyCollection as $property) {
            /**
             * @var $property PropertyValue
             */

            $arProp = $property->getProperty();

            switch($arProp['CODE']) {
                case 'EDIT_RESPONS_DT':
                case 'EDIT_RESPONS_DT_UR':
                    $property->setValue($expDate);
                    break;

                case 'EXPORT_DO':
                case 'EXPORT_DO_UR':
                    $property->setValue('NNN');
                    break;

                case 'EXT_STATUS':
                case 'EXT_STATUS_UR':
                    $property->setValue($arData['Статус']);
                    Debug::dump($arProp['CODE'] . ' = ' . $arData['Статус']);
                    break;

                default:
                    continue;
            }
        }

        $res = $order->save();

        unset($propertyCollection);
        unset($order);

        if(!$res->isSuccess()) {
//            Debug::dump($res->getErrorMessages());
            Tables\SyncHistoryTable::add([
                'name' => 'order-updatestatus',
                'operation' => 'import',
                'result' => 'error',
                'msg' => $res->getErrorMessages(),
            ]);
            return false;
        }

        return true;
    }

    private function _updateOrderFull(Order $order, array $arData)
    {
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');
        $context = \Bitrix\Main\Application::getInstance()->getContext();
        $siteId = $context->getSite();


//        Debug::dump($arData);
//        Debug::dump(ord(' '));

        // обновляем общие данные заказа
        $order->setField('XML_ID', $arData['ИД']);
        $order->setField('USER_DESCRIPTION', $arData['Комментарий']);

        $propertyCollection = $order->getPropertyCollection();

        foreach ($propertyCollection as $property) {
            /**
             * @var $property PropertyValue
             */

            $arProp = $property->getProperty();

            switch($arProp['CODE']) {
                case 'EDIT_RESPONS_DT':
                case 'EDIT_RESPONS_DT_UR':
                    $property->setValue($expDate);
                    break;

                case 'EXPORT_DO':
                case 'EXPORT_DO_UR':
                    $property->setValue('NNN');
                    break;

                case 'EXT_STATUS':
                case 'EXT_STATUS_UR':
                    $property->setValue($arData['Статус']);
                    break;

                default:
                    continue;
            }
        }

        // обновляем данные товаров
        $basket = $order->getBasket();

        $basketItems = $basket->getBasketItems();

        // получим текущие товары
        $arItems = [];
        foreach ($basketItems as $basketItem) {
            $arCurrentItems[$basketItem->getField('PRODUCT_XML_ID')] = $basketItem;
        }

        unset($basketItem);

        // сравним то, что пришло из 1С с текущими товарами
        foreach ($arData['Товары'] as $ar1CProd) {
            $xmlId = $ar1CProd['ИД'];

            Debug::dump($xmlId);
            Debug::dump($ar1CProd['Название']);

            if (isset($arCurrentItems[$xmlId])) {
                Debug::dump('Update');
                Debug::dump($ar1CProd['Количество']);
                Debug::dump($ar1CProd['Цена']);

                $arFields = [
                    'QUANTITY' => $ar1CProd['Количество'],
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => $siteId,
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    'CUSTOM_PRICE' => 'Y',
                    'PRICE' => $ar1CProd['Цена'],
                    'PRODUCT_XML_ID' => $xmlId,
                ];

                $arCurrentItems[$xmlId]->setFields($arFields);

                $basketPropertyCollection = $arCurrentItems[$xmlId]->getPropertyCollection();

                // создание/обновление свойства товара
                $basketPropertyCollection->setProperty([
                    [
                        'NAME' => 'Статус из 1С',
                        'CODE' => 'STATUS_1C',
                        'VALUE' => $ar1CProd['Статус'],
                        'SORT' => 100,
                    ]
                ]);

                unset($arCurrentItems[$xmlId]);

            } else {
                Debug::dump('New');

                // получим товар, если его нет, то все...
                $arOrder = [];
                $arFilter = [
                    'XML_ID' => $xmlId,
                ];
                $arSelect = [];
                $dbRes = \CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);

                $arRes = $dbRes->GetNext();

                if(!$arRes) {
                    Debug::dump('no product on site');
                    Tables\SyncHistoryTable::add([
                        'name' => 'order-updatestatus',
                        'operation' => 'import',
                        'result' => 'error',
                        'msg' => 'товар не добавлен в заказ, т.к. отсутствует на сайте ['.$ar1CProd['Название'].'] ['.$xmlId.']',
                    ]);
                    continue;
                }

                $item = $basket->createItem('catalog', $arRes['ID']);

                $item->setFields([
                    'NAME' => $ar1CProd['Название'],
                    'QUANTITY' => $ar1CProd['Количество'],
                    'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                    'LID' => $siteId,
                    'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
                    'CUSTOM_PRICE' => 'Y',
                    'PRICE' => $ar1CProd['Цена'],
                    'PRODUCT_XML_ID' => $xmlId,
                ]);

                $basketPropertyCollection = $item->getPropertyCollection();

                // создание/обновление свойства товара
                $basketPropertyCollection->setProperty([
                    [
                        'NAME' => 'Статус из 1С',
                        'CODE' => 'STATUS_1C',
                        'VALUE' => $ar1CProd['Статус'],
                        'SORT' => 100,
                    ]
                ]);
            }
        }

        // оставшиеся товары из заказа удаляем, т.к. они не пришли из 1С
        foreach ($arCurrentItems as $obCurrentItem) {
            Debug::dump($obCurrentItem->getField('PRODUCT_XML_ID'));
            Debug::dump($obCurrentItem->getField('NAME'));
            Debug::dump('Delete');
            $obCurrentItem->delete();
        }

        $res = $order->save();

        unset($propertyCollection);
        unset($order);

        if(!$res->isSuccess()) {
            Debug::dump($res->getErrorMessages());
            Tables\SyncHistoryTable::add([
                'name' => 'order-updatefull',
                'operation' => 'import',
                'result' => 'error',
                'msg' => $res->getErrorMessages(),
            ]);
            return false;
        }

        return true;
    }

    private function _update($arData)
    {
        $order = Order::loadByAccountNumber($arData['Номер']);

        if( !$order instanceof Order) {
            return false;
        }

        // если нет товаров, то обновляем только статус заказа
        if ( count($arData['Товары']) <= 0 ) {
            return $this->_updateOrderStatus($order, $arData);
        }

        // если ЕСТЬ товары, то обновляем и данные заказа и товаров в нем
        return $this->_updateOrderFull($order, $arData);
    }

    public function import(FtpClient $ftpClient)
    {

        // проверим что нет ранее запущенного импорта
        $fileFlagPath = $_SERVER["DOCUMENT_ROOT"] . '/IS_IMPORT_ORDERS';

        if (file_exists($fileFlagPath)) {
            return;
        }

        // создадим файл-флаг текущей выгрузки
        file_put_contents($fileFlagPath, date('Y.d.m H:i:s'));

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

        // получаем существующие на сайте заказы по ACCOUNT_NUMBER
        $arExistedOrders = [];
        $arOrder = [];
        $arFilter = ["ACCOUNT_NUMBER" => $arData['CODES']];
        $arSelect = [];

        $dbRes = \CSaleOrder::GetList($arOrder, $arFilter, false, false, $arSelect);

        while ($arRes = $dbRes->GetNext()) {
            $arExistedOrders[$arRes['ACCOUNT_NUMBER']] = $arRes;
        }


        foreach ($arData['OBJECTS'] as $key => $arObj) {
            $arResult['CNT']++;

            if (isset($arExistedOrders[$key])) {
//                Debug::dump('Update');
                $res = $this->_update($arObj);

                if(!$res) {
                    $arResult['CNT_ERROR']++;
                } else {
                    $arResult['CNT_UPD']++;
                }
            } else {
//                Debug::dump('New');
                $res = $this->_create($arObj);

                if(!$res) {
                    $arResult['CNT_ERROR']++;
                } else {
                    $arResult['CNT_INS']++;
                }
            }
        }

        // Удаляем файл на FTP
        $ftpClient->rmFtpImportFile();

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

        // удалим файл-флаг статуса выгрузки
        unlink($fileFlagPath);

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
        Loader::includeModule('sale');
        
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
//            'FINISH_DATE',
//            'PREPAY',
//            'OSTATOK',

            'EXCH_STATUS_UR',
            'EXPORT_DO_UR',
            'IS_IMPORTED_UR',
            'EDIT_REQUEST_DT_UR',
            'EDIT_RESPONS_DT_UR',
            'EXT_STATUS_UR',
//            'FINISH_DATE_UR',
//            'PREPAY_UR',
//            'OSTATOK_UR',

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