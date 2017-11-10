<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class LocalExch1CUserDataForm extends CBitrixComponent {

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

//        $el = new CIBlockElement();
//
//        $PROP = [
//            'FIO' => $name,
//            'PHONE' => $phone,
//        ];
//
//        $arLoadProductArray = Array(
//            "IBLOCK_SECTION_ID" => false,      // элемент лежит в корне раздела
//            "IBLOCK_ID"      => $this->_arParams['IBLOCK_ID'],
//            "PROPERTY_VALUES"=> $PROP,
//            "NAME"           => "Заявка на регистрацию",
//            "ACTIVE"         => "Y",            // активен
//        );
//
//        // создаем элемент
//        $ITEM_ID = $el->Add($arLoadProductArray);
//
//        if (!$ITEM_ID) {
//            $arResult['msg'] = "Ошибка отправки данных. " . $el->LAST_ERROR;
//            echo json_encode($arResult);
//            die();
//        }

        $arResult = array(
            'hasError' => false,
            'msg' => $this->arParams["MSG_SUCCESS"],
        );
        echo json_encode($arResult);

        // шлем почту
//        $tmplName = \Bitrix\Main\Config\Option::get('local.exch1c', 'LOCAL.EXCH1C_EMAIL_TMPL_REGREQUEST');
//
//        CEvent::Send($tmplName, SITE_ID, $arMsgData);

    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->_request = Application::getInstance()->getContext()->getRequest();

        $this->arResult['USER_SECTIONS'] = [
            [
                'NAME' => 'Служебная информация',
                'FIELDS' => [
                    [
                        'CODE' => 'Фото',
                        'NAME' => 'Фото',
                        'TYPE' => 'file',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                ]
            ],

            [
                'NAME' => 'Общая информация',
                'FIELDS' => [
                    [
                        'CODE' => 'LOGIN',
                        'NAME' => 'Код клиента',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'WORK_PROFILE',
                        'NAME' => 'Тип контрагента',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'WORK_COMPANY',
                        'NAME' => 'Название компании',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'NAME',
                        'NAME' => 'Название магазина',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_FIO_DIR',
                        'NAME' => 'ФИО директора',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],

                    [
                        'CODE' => 'UF_UR_ADR',
                        'NAME' => 'Юридический Адрес',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],

                ]
            ],

            [
                'NAME' => 'Контактная информация',
                'FIELDS' => [
                    [
                        'CODE' => 'PERSONAL_STATE',
                        'NAME' => 'Край',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
//                    [
//                        'CODE' => '',
//                        'NAME' => 'Район',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
                    [
                        'CODE' => 'PERSONAL_CITY',
                        'NAME' => 'Город',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
//                    [
//                        'CODE' => 'КонтактноеЛицоИД',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
                    [
                        'CODE' => 'UF_KONT_LITSO_FIO',
                        'NAME' => 'ФИО контактного лица',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'PERSONAL_PHONE',
                        'NAME' => 'Телефон',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'EMAIL',
                        'NAME' => 'Электронная почта',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'PERSONAL_STREET',
                        'NAME' => 'Адрес доставки',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_VK_OTHER',
                        'NAME' => 'Вконтакте',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_INST_OTHER',
                        'NAME' => 'Instagram',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_FB_OTHER',
                        'NAME' => 'Facebook',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                ]
            ],

            [
                'NAME' => 'Условия сотрудничества',
                'FIELDS' => [
                    [
                        'CODE' => 'UF_DISCOUNT_COMMON',
                        'NAME' => 'Основная скидка',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_VHD',
                        'NAME' => 'Скидка на входные двери',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_MKD',
                        'NAME' => 'Скидка на межкомнатные двери',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_POL',
                        'NAME' => 'Скидка на напольные покрытия',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_FUR',
                        'NAME' => 'Скидка на фурнитуру',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_OTSROCHKA_DAY',
                        'NAME' => 'Отсрочка дней',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_OTSROCHKA_RUB',
                        'NAME' => 'Отсрочка рублей',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
                    [
                        'CODE' => 'UF_VITR_ALL',
                        'NAME' => 'Витрин всего',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
//                    [
//                        'CODE' => 'РегиональныйМенеджерИД',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
                    [
                        'CODE' => 'UF_REGMAN_FIO',
                        'NAME' => 'Региональный менеджер',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
//                    [
//                        'CODE' => 'РегиональныйМенеджерТелефон',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
//                    [
//                        'CODE' => 'РегиональныйМенеджерЭлектроннаяПочта',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
//                    [
//                        'CODE' => 'ОтветственныйМенеджерИД',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
                    [
                        'CODE' => 'UF_LOCMAN_FIO',
                        'NAME' => 'Ответственный менеджер',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                    ],
//                    [
//                        'CODE' => 'ОтветственныйМенеджерТелефон',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
//                    [
//                        'CODE' => 'ОтветственныйМенеджерЭлектроннаяПочта',
//                        'NAME' => '',
//                        'TYPE' => 'text',
//                        'VALUE' => '',
//                        'READONLY' => '',
//                    ],
                ]
            ],
        ];

        if($this->_request->isPost() && $this->_request['formId'] == 'UserDataForm') {
            $this->_app()->restartBuffer();
            //\Bitrix\Main\Diag\Debug::dump($this->_request['REGISTER']);
            $this->_regRequest();
            die();
        }

        $this->includeComponentTemplate();
    }
}