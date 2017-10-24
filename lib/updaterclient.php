<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Local\Tp\TicketCategoryTable;
use Bitrix\Main\Type;

class UpdaterClient
    implements IUpdater
{
    private $_arData = '';
    private $_ibId;

    public function __construct($ibId, $arData) {
        $this->_arData = $arData;
        $this->_ibId = $ibId;
    }

    public function sync() {
        Loader::includeModule('iblock');

        $by = "ID";

        $order = "ASC";

        $strAdmins = implode(' | ', $this->_arData['CLIENT_CODES']);

        $arFilter = [
            'ACTIVE' => 'Y',
            'LOGIN_EQUAL' => $strAdmins,
        ];

        $arParams = [
            'FIELDS' => ['LOGIN', 'NAME'],
            'SELECT' => ['UF_*'],
        ];

        $dbRes = \CUser::GetList($by, $order, $arFilter, $arParams);

        $arUsers = [];

        while ($arUser = $dbRes->Fetch()) {
            $arUsers[$arUser['LOGIN']] = $arUser;
        }

        foreach ($this->_arData['CLIENT_CODES'] as $login) {
            if (isset($arUsers[$login])) {
                // обновление
                Debug::dump('update');
            } else {
                // создание
                Debug::dump('insert');
            }
        }


    }
}