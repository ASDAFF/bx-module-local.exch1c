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

    private $_arUserData;

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

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->_request = Application::getInstance()->getContext()->getRequest();

        $arSystemInfo = [
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
        ];

        $arTmpSection = $this->_arParams['SECTIONS'];
        $arTmpSection[] = $arSystemInfo;

        $this->setSections($arTmpSection);
        $this->fieldsPrepare();

        if($this->_request->isPost() && $this->_request['formId'] == 'UserDataForm') {
            $this->_app()->restartBuffer();
            //\Bitrix\Main\Diag\Debug::dump($this->_request['REGISTER']);
            $this->_setDataForChange();
            die();
        }

        if ($this->getUser()->IsAuthorized()) {
            $this->includeComponentTemplate();
        } else {
            //echo "Авторизуйтесь";
            LocalRedirect('/');
        }

    }

    protected function setSections($arTmpSection) {

        $this->_arSections = [];

        foreach ($arTmpSection as $secKey => $arSection) {

            $arFields = [];

            foreach ($arSection['FIELDS'] as $arField) {
                $arFields[$arField['CODE']] = $arField;
                $arFields[$arField['CODE']]['SECTION_KEY'] = $secKey;

                $tmpFieldCode = trim($arField['TMP_FIELD']);

                if($tmpFieldCode) {
                    $arFields[$tmpFieldCode] = [
                        'CODE' => $tmpFieldCode,
                        'NAME' => 'TMP_'.$arField['NAME'],
                        'TYPE' => 'tmp',
                        'VALUE' => '',
                        'READONLY' => 'Y',
                        'TMP_FIELD' => '',
                        'SECTION_KEY' => $secKey,
                    ];
                }
            }

            $arSection['FIELDS'] = $arFields;

            $this->_arSections[] = $arSection;

            unset($arFields);
            unset($arSection);
        }

    }

    protected function getFieldByTypes() {
        $arFieldsAll = [];
        $arFieldsTmp = [];
        $arFieldsEditable = [];
        $arFilterFields = [];
        $arFilterUFs = [];

        foreach ($this->_arSections as $arSection) {

            foreach ($arSection['FIELDS'] as $key => $arField) {

                $arFieldsAll[$key] = $arField;

                if($arField['READONLY'] !== 'Y') {
                    $arFieldsEditable[$key] = $arField;
                }

                if ($arField['TYPE'] === 'tmp') {
                    $arFieldsTmp[$key] = $arField;
                }

                if (strpos($arField['CODE'], 'UF_') === 0) {
                    $arFilterUFs[$key] = $arField;
                } else {
                    $arFilterFields[$key] = $arField;
                }

            }
        }

        return [
            'ALL' => $arFieldsAll,
            'EDITABLE' => $arFieldsEditable,
            'TMP' => $arFieldsTmp,
            'FILTER_FIELDS' => $arFilterFields,
            'FILTER_UFS' => $arFilterUFs,
        ];
    }

    /**
     * получение данных пользователя по нужным полям
     */
    protected function fieldsGetByUser() {
        // соберем поля для получения данных о пользователе
        $arFields = $this->getFieldByTypes();

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

        $this->_arUserData = $dbRes->GetNext();
    }

    /**
     * Обновление полей на основе данных пользователя
     */
    protected function setSectionsData() {

        if (!is_array($this->_arUserData) || count($this->_arUserData) < 1 ) {
            throw new Exception('Ошибочные данные в $this->_arUserData');
        }

        if (!is_array($this->_arSections) || count($this->_arSections) < 1 ) {
            throw new Exception('Ошибочные данные в $this->_arUserData');
        }

        foreach ($this->_arSections as $key => &$arSection) {

            $arFields = $arSection['FIELDS'];

            foreach ($arFields as $key => $arField) {
                $arSection['FIELDS'][$key]['VALUE'] = $this->_arUserData[$arField['CODE']];
            }

            unset($arFields);
        }
    }

    /**
     * Подготовка полей для отображения
     */
    protected function fieldsPrepare() {

        // инициирует $this->_arUserData данными пользователя
        $this->fieldsGetByUser();

        // наполняет поля данных $this->_arSections данными на основе $this->_arUserData
        $this->setSectionsData();

        //\Bitrix\Main\Diag\Debug::dump($this->_arSections);

        $this->arResult['USER_SECTIONS'] = $this->_arSections;

        //\Bitrix\Main\Diag\Debug::dump($arUserData);
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

        // проверим что данные подтверждены в 1С и это не повторная отправка
        if(	$this->_arUserData['UF_NEED_CONFIRM'] === 'Y' ) {
            $arResult['msg'] = "Спасибо за регистрацию! Наш менеджер обязательно свяжется с Вами для уточнения деталей.";
            echo json_encode($arResult);
            die();
        }

        $valid = true;
        if(!check_bitrix_sessid()) {
            $valid = false;
        }

        // TODO: валидация полей

        if(	!$valid ) {
            $arResult['msg'] = "Данные ведены не верно или не все обязательные поля заполнены.";
            echo json_encode($arResult);
            die();
        }

        // получим массив допустимых для изменения полей
        $arFieldByTypes = $this->getFieldByTypes();
        $arFieldsEditable = $arFieldByTypes['EDITABLE'];

        // проверим наличие данных
        $obData = $this->_request->getPostList();

        $arFieldsForEdit = [];

        foreach ($obData as $key => $data) {

            $dataPrep = htmlspecialcharsbx(trim($data));

            if ( !isset($arFieldsEditable[$key])
                // отказ от этих условий из-за необходимости удалять данные, поэтому полностью передаем все служебные поля
                //|| empty($dataPrep)
                //|| $dataPrep === $this->_arSections[$arFieldsEditable[$key]['SECTION_KEY']]["FIELDS"][$key]['VALUE']
            ) {
                continue;
            }

            $arFieldsForEdit[$arFieldsEditable[$key]['TMP_FIELD']] = $dataPrep;

        }

        if(	count($arFieldsForEdit) < 1 ) {
            $arResult['msg'] = "Нет измененных данных.";
            echo json_encode($arResult);
            die();
        }

        $user = new CUser();

        $arFieldsForEdit['UF_EXPORT_DO'] = 'Y';
        $arFieldsForEdit['UF_NEED_CONFIRM'] = 'Y';
        $arFieldsForEdit['UF_EDIT_REQUEST_DT'] = (new DateTime())->format('d.m.Y H:i:s');
        $arFieldsForEdit['UF_EDIT_RESPONS_DT'] = '';

        $user->Update(self::_user()->GetID(), $arFieldsForEdit);

        $arResult = array(
            'hasError' => false,
            'msg' => str_replace('#EMAIL#', $user->GetEmail(), $this->arParams["MSG_SUCCESS"]),
        );
        echo json_encode($arResult);

        // шлем почту
        $arMsgData = $arFieldsForEdit;
        $tmplName = \Bitrix\Main\Config\Option::get('local.exch1c', 'LOCALEXCH1C_EDITREQUEST');
        CEvent::Send($tmplName, SITE_ID, $arMsgData);

    }
}