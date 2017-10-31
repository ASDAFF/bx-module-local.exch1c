<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class SyncerUser implements ISyncer
{

    private $_arData;

    public function __construct($arData)
    {
        $this->_arData = $arData;
    }

    public function run() {
        if (!$this->_arData) {
            throw new \Exception('wrong arData');
        }

        foreach ($this->_arData['OBJECTS'] as $arObj) {
            // получить пользователя по логину
            $dbUser = \CUser::GetByLogin($arObj["Код"]);
            $arUser = $dbUser->GetNext();

            Debug::dump($arUser);

            // обновить

            // создать
        }
    }

}