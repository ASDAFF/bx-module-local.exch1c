<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class LocalExch1CShowStatistic extends CBitrixComponent {

    /**
     * @var Bitrix\Main\Request
     */
    private $_request;

    private $_arParams;


    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
    private function _checkModules() {
        if (
            !Loader::includeModule('iblock')
            || !Loader::includeModule('sale')
        ) {
            throw new \Exception('Не загружены модули необходимые для работы модуля');
        }

        return true;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllMain|CMain
     */
    private function _app() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllUser|CUser
     */
    private function _user() {
        global $USER;
        return $USER;
    }

    /**
     * Подготовка параметров компонента
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams) {
        $this->_arParams = $arParams;
        // тут пишем логику обработки параметров, дополнение параметрами по умолчанию
        // и прочие нужные вещи
        return $arParams;
    }

    protected function getOrders($arFilter, $limit, $offset) {
        $arOrders = [];

        $arFilterFinal = [
            'USER_ID' => $this->_user()->GetID(),
        ];

        $arSelect = [
            'ID',
            'ACCOUNT_NUMBER',
            'DATE_INSERT',
            'PRICE',
//            'EXT_STATUS_UR',
//            'FINISH_DATE_UR',
//            'PREPAY',
//            'OSTATOK',
        ];

        $arOrder = [
            'DATE_INSERT' => 'DESC',
        ];

        foreach ($arFilter as $filterField => $filterData) {
            switch ($filterField) {
                case 'date-start':
                    $objDateTime = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($filterData));
                    $arFilterFinal[] = [
                        'DATE_INSERT' => $objDateTime->toString(),
                    ];
                    break;
                case 'date-finish':
                    $objDateTime = Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime($filterData));
                    $arFilterFinal[] = [
                        'FINISH_DATE_UR' => $objDateTime->toString(),
                    ];
                    break;
                case 'status':
                    $arFilterFinal[] = [
                        'EXT_STATUS_UR' => $filterData,
                    ];
                    break;
                case 'sum':
                    $price = floatval(str_replace(',', '.', $filterData));
                    $arFilterFinal[] = [
                        'PRICE' => $price,
                    ];
                    break;
                case 'prepay':
                    $price = floatval(str_replace(',', '.', $filterData));
                    $arFilterFinal[] = [
                        'PREPAY' => $price,
                    ];
                    break;
                case 'ostatok':
                    $price = floatval(str_replace(',', '.', $filterData));
                    $arFilterFinal[] = [
                        'OSTATOK' => $price,
                    ];
                    break;
                default:
                    continue;
            }
        }

        $getListParams = array(
            'filter' => $arFilterFinal,
            'select' => $arSelect,
            'order' => $arOrder,
            'limit' => $limit,
            'offset' => $offset,
        );

        $dbRes = Bitrix\Sale\Order::getList($getListParams);

        $orderIdList = [];
        $needsProps = [
            'EXT_STATUS_UR',
            'FINISH_DATE_UR',
            'PREPAY_UR',
            'OSTATOK_UR',
        ];
        while ($arRes = $dbRes->fetch()) {
            $arRes['PRICE_FORMATED'] = number_format ( $arRes['PRICE'], 2, ',', ' ' );
            $arOrders[$arRes['ID']] = $arRes;
            $orderIdList[] = $arRes['ID'];

            $order = Bitrix\Sale\Order::load($arRes['ID']);
            $propertyCollection = $order->getPropertyCollection();

            /** @var Bitrix\Sale\PropertyValue $obProperty */
            foreach ($propertyCollection as $obProperty) {
                $arProp = $obProperty->getProperty();

                if (in_array($arProp['CODE'], $needsProps)) {
                    $arOrders[$arRes['ID']]['PROPS'][$arProp['CODE']] = $obProperty->getValue();
                }
            }
        }

        // получим товары в заказе
        $listBaskets = Bitrix\Sale\Basket::getList(array(
            'select' => array("*"),
            'filter' => array("ORDER_ID" => $orderIdList),
            'order' => array('NAME' => 'asc')
        ));

        $listOrderBasket = [];
        while ($basket = $listBaskets->fetch())
        {
            if (CSaleBasketHelper::isSetItem($basket))
                continue;

            $listOrderBasket[$basket['ORDER_ID']][$basket['ID']] = $basket;
        }

        // объединим массив заказов и товаров
        foreach($arOrders as &$arOrder) {
            $arOrder['ITEMS'] = $listOrderBasket[$arOrder['ID']];
        }

        return $arOrders;
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->_request = Application::getInstance()->getContext()->getRequest();

        if($this->_request->isPost() && $this->_request['formId'] == 'UserDataForm') {
            $this->_app()->restartBuffer();
            //\Bitrix\Main\Diag\Debug::dump($this->_request['REGISTER']);
            die();
        }

        if (!$this->_user()->IsAuthorized()) {
            //echo "Авторизуйтесь";
            LocalRedirect('/');
        }

        // Получим список заказов
        $arSort = [

        ];

        $limit = $this->arParams['SHOW_CNT'];
        $offset = 0;
        $this->arResult['ORDERS'] = $this->getOrders($arSort, $limit, $offset);

        $this->includeComponentTemplate();

    }

}