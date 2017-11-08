<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class LocalExch1CRegRequestForm extends CBitrixComponent {

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
        if ( !Loader::includeModule('iblock') ) {
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

    private function _regRequest() {

        $valid = true;
        if(!check_bitrix_sessid()) {
            $valid = false;
        }

        $arMsgData = array();

        $arResult = array(
            'hasError' => true,
            'msg' => "Ошибка отправки сообщения"
        );

        // проверим, что пришел POST
        if(!$this->_request->isPost()) {
            echo json_encode($arResult);
            die();
        }

        // подключим модуль инфоблоков
        if(!CModule::IncludeModule('iblock')) {
            echo json_encode($arResult);
            die();
        }

        // проверим наличие данных
        $arRegData = $this->_request->getPost('REGISTER');
        $name = htmlspecialcharsEx($arRegData['FIO']);
        $phone = htmlspecialcharsEx($arRegData['PHONE']);

        $arMsgData = array(
            'FIO' => $name,
            'PHONE' => $phone,
        );

        if(	   !$valid
            || !$phone )
        {
            $arResult['msg'] = "Заполните все поля.";
            echo json_encode($arResult);
            die();
        }

        $el = new CIBlockElement();

        $PROP = [
            'FIO' => $name,
            'PHONE' => $phone,
        ];

        $arLoadProductArray = Array(
            "IBLOCK_SECTION_ID" => false,      // элемент лежит в корне раздела
            "IBLOCK_ID"      => $this->_arParams['IBLOCK_ID'],
            "PROPERTY_VALUES"=> $PROP,
            "NAME"           => "Заявка на регистрацию",
            "ACTIVE"         => "Y",            // активен
        );

        // создаем элемент
        $ITEM_ID = $el->Add($arLoadProductArray);

        if (!$ITEM_ID) {
            $arResult['msg'] = "Ошибка отправки данных. " . $el->LAST_ERROR;
            echo json_encode($arResult);
            die();
        }

        $arResult = array(
            'hasError' => false,
            'msg' => $this->arParams["MSG_SUCCESS"],
        );
        echo json_encode($arResult);

        // шлем почту
        $tmplName = \Bitrix\Main\Config\Option::get('local.exch1c', 'LOCAL.EXCH1C_EMAIL_TMPL_REGREQUEST');

        CEvent::Send($tmplName, SITE_ID, $arMsgData);

    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->_request = Application::getInstance()->getContext()->getRequest();

        $this->arResult['FIO'] = '';
        $this->arResult['PHONE'] = '';

        if($this->_request->isPost() && $this->_request['formId'] == 'SendFormRegRequest') {
            $this->_app()->restartBuffer();
            //\Bitrix\Main\Diag\Debug::dump($this->_request['REGISTER']);
            $this->_regRequest();
            die();
        }

        $this->includeComponentTemplate();
    }
}