<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class LocalExch1CUserDataForm extends CBitrixComponent {

    /**
     * @var Bitrix\Main\Request
     */
    private $_request;

    private $_arParams;

    private $_arSections;

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

    private function getUser() {
        global $USER;
        return $USER;
    }

    private function _setDataForChange() {

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

        $valid = true;
        if(!check_bitrix_sessid()) {
            $valid = false;
        }

        $arMsgData = array();


        // получим массив допустимых для изменения полей
        $arFields = $this->getFields();
        $arFields = $arFields['EDITABLE'];

        // проверим наличие данных
        $obData = $this->_request->getPostList();
        $arFieldTypes = $this->getFields();
        $arFieldsAll = $arFieldTypes['ALL'];
        $arFieldsEditable = $arFieldTypes['EDITABLE'];

        $arFieldsForEdit = [];

        foreach ($obData as $key => $data) {

            if ( !isset($arFieldsEditable[$key]) ) {
                continue;
            }

            $arFieldsForEdit[$arFieldsEditable[$key]['TMP_FIELD']] = $obData[$key];

        }

        $user = new CUser();

        $user->Update(self::_user()->GetID(), $arFieldsForEdit);

        if(	!$valid )
        {
            $arResult['msg'] = "Заполните все поля.";
            echo json_encode($arResult);
            die();
        }

        $arResult = array(
            'hasError' => false,
            'msg' => $this->arParams["MSG_SUCCESS"],
        );
        echo json_encode($arResult);

        // шлем почту
        $arMsgData = $arFieldsForEdit;
        $tmplName = \Bitrix\Main\Config\Option::get('local.exch1c', 'LOCALEXCH1C_EDITREQUEST');
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

        $this->_arSections = [
            [
                'NAME' => 'Служебная информация',
                'FIELDS' => [
                    [
                        'CODE' => 'UF_EXPORT_DO',
                        'NAME' => 'Служебное Требуется передать в 1С',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_IS_NEW',
                        'NAME' => 'Служебное новый клиент',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_NEED_CONFIRM',
                        'NAME' => 'Служебное ждет подтверждения из 1с',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_EDIT_REQUEST_DT',
                        'NAME' => 'Служебное дата запроса',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_EDIT_RESPONS_DT',
                        'NAME' => 'Служебное дата подтверждения',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                ]
            ],

            [
                'NAME' => 'Общая информация',
                'FIELDS' => [
                    [
                        'CODE' => 'Фото',
                        'NAME' => 'Фото',
                        'TYPE' => 'file',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'LOGIN',
                        'NAME' => 'Код клиента',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'WORK_PROFILE',
                        'NAME' => 'Тип контрагента',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'WORK_COMPANY',
                        'NAME' => 'Название компании',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_WORK_COMPANY',
                    ],
                    [
                        'CODE' => 'NAME',
                        'NAME' => 'Название магазина',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_NAME',
                    ],
                    [
                        'CODE' => 'UF_FIO_DIR',
                        'NAME' => 'ФИО директора',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_FIO_DIR',
                    ],

                    [
                        'CODE' => 'UF_UR_ADR',
                        'NAME' => 'Юридический Адрес',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_UR_ADR',
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
                        'TMP_FIELD' => 'UF_2_PERSONAL_STATE',
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
                        'TMP_FIELD' => 'UF_2_PERSONAL_CITY',
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
                        'TMP_FIELD' => 'UF_2_KONT_LITSO_FIO',
                    ],
                    [
                        'CODE' => 'PERSONAL_PHONE',
                        'NAME' => 'Телефон',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_PERSONAL_PHONE',
                    ],
                    [
                        'CODE' => 'EMAIL',
                        'NAME' => 'Электронная почта',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_EMAIL',
                    ],
                    [
                        'CODE' => 'PERSONAL_STREET',
                        'NAME' => 'Адрес доставки',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_PERSONAL_STREET',
                    ],
                    [
                        'CODE' => 'UF_VK_OTHER',
                        'NAME' => 'Вконтакте',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_VK_OTHER',
                    ],
                    [
                        'CODE' => 'UF_INST_OTHER',
                        'NAME' => 'Instagram',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_INST_OTHER',
                    ],
                    [
                        'CODE' => 'UF_FB_OTHER',
                        'NAME' => 'Facebook',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => '',
                        'TMP_FIELD' => 'UF_2_FB_OTHER',
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
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_VHD',
                        'NAME' => 'Скидка на входные двери',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_MKD',
                        'NAME' => 'Скидка на межкомнатные двери',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_POL',
                        'NAME' => 'Скидка на напольные покрытия',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_DISCOUNT_FUR',
                        'NAME' => 'Скидка на фурнитуру',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_OTSROCHKA_DAY',
                        'NAME' => 'Отсрочка дней',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_OTSROCHKA_RUB',
                        'NAME' => 'Отсрочка рублей',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ],
                    [
                        'CODE' => 'UF_VITR_ALL',
                        'NAME' => 'Витрин всего',
                        'TYPE' => 'text',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
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
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
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
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
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

        $this->fieldsPrepare();

        if($this->_request->isPost() && $this->_request['formId'] == 'UserDataForm') {
            $this->_app()->restartBuffer();
            //\Bitrix\Main\Diag\Debug::dump($this->_request['REGISTER']);
            $this->_setDataForChange();
            die();
        }

        $this->includeComponentTemplate();
    }

    protected function getFields() {
        $arFieldsAll = [];
        $arFieldsEditable = [];
        $arFilterFields = [];
        $arFilterUFs = [];

        foreach ($this->_arSections as $arSection) {

            foreach ($arSection['FIELDS'] as $arField) {

                $arFieldsAll[$arField['CODE']] = $arField;

                if($arField['READONLY'] !== 'Y') {
                    $arFieldsEditable[$arField['CODE']] = $arField;
                }

                if ($arField['TMP_FIELD']) {
                    $arFieldsAll[$arField['TMP_FIELD']] = [
                        'CODE' => $arField['TMP_FIELD'],
                        'NAME' => 'TMP_'.$arField['NAME'],
                        'TYPE' => 'tmp',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ];

                    if (strpos($arField['TMP_FIELD'], 'UF_') === 0) {
                        $arFilterUFs[$arField['TMP_FIELD']] = [
                            'CODE' => $arField['TMP_FIELD'],
                            'NAME' => 'TMP_'.$arField['NAME'],
                            'TYPE' => 'tmp',
                            'VALUE' => '',
                            'READONLY' => 'Y',
                            'TMP_FIELD' => '',
                        ];
                    } else {
                        $arFilterFields[$arField['TMP_FIELD']] = [
                            'CODE' => $arField['TMP_FIELD'],
                            'NAME' => 'TMP_'.$arField['NAME'],
                            'TYPE' => 'tmp',
                            'VALUE' => '',
                            'READONLY' => 'Y',
                            'TMP_FIELD' => '',
                        ];
                    }
                }

                if (strpos($arField['CODE'], 'UF_') === 0) {
                    $arFilterUFs[$arField['CODE']] = $arField;
                } else {
                    $arFilterFields[$arField['CODE']] = $arField;
                }

            }
        }

        return [
            'ALL' => $arFieldsAll,
            'EDITABLE' => $arFieldsEditable,
            'FILTER_FIELDS' => $arFilterFields,
            'FILTER_UFS' => $arFilterUFs,
        ];
    }

    /**
     * получение данных пользователя по нужным полям
     */
    protected function fieldsGetByUser() {
        // соберем поля для получения данных о пользователе
        $arFields = $this->getFields();
        $arFilterFields = array_keys($arFields['FILTER_FIELDS']);
        $arFilterUFs = array_keys($arFields['FILTER_UFS']);

        $user = $this->getUser();

        $arFilter = [
            'ID' => $user->GetID(),
        ];
        $arParams = [
            'FIELDS' => $arFilterFields,
            'SELECT' => $arFilterUFs,
        ];

        $dbRes = $user::GetList(
            $by = "timestamp_x",
            $order = "desc",
            $arFilter,
            $arParams);

        $arRes = $dbRes->GetNext();

        return $arRes;
    }

    /**
     * Обновление полей на основе данных пользователя
     */
    protected function setSectionsData($arUserData) {

        foreach ($this->_arSections as &$arSection) {

            $arFields = $arSection['FIELDS'];

            foreach ($arFields as $key => $arField) {

//                if(!isset($arUserData[$arField['CODE']])) {
//                    continue;
//                }

                $arSection['FIELDS'][$arField['CODE']]['VALUE'] = $arUserData[$arField['CODE']];

                if ($arField['TMP_FIELD']) {

                    $arSection['FIELDS'][$arField['TMP_FIELD']] = [
                        'CODE' => $arField['TMP_FIELD'],
                        'NAME' => 'TMP_'.$arField['NAME'],
                        'TYPE' => 'tmp',
                        'VALUE' => $arUserData[$arField['TMP_FIELD']],
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                    ];

                }

            }

            unset($arFields);
        }

//        \Bitrix\Main\Diag\Debug::dump($this->_arSections);
//        \Bitrix\Main\Diag\Debug::dump($arUserData);

    }

    /**
     * Подготовка полей для отображения
     */
    protected function fieldsPrepare() {
        $arUserData = $this->fieldsGetByUser();
        $this->setSectionsData($arUserData);

        $this->arResult['USER_SECTIONS'] = $this->_arSections;

        //\Bitrix\Main\Diag\Debug::dump($arUserData);
    }
}