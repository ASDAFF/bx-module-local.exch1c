<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class SyncerUser implements ISyncer
{

    private function _create($arUser)
    {
        Debug::dump($arUser);

        $pass = randString(7);

        $arFields = [
            'LOGIN' => $arUser['Код'],
            'EMAIL' => $arUser['ЭлектроннаяПочта'],
            'PASSWORD' => $pass,
            'CONFIRM_PASSWORD' => $pass,
            'GROUP_ID' => [USER_GROUP_OPT_ID],
            'ACTIVE' => 'Y',
            'LID' => SITE_ID,
            'WORK_PROFILE' => $arUser['ВидКонтрагента'],
            'WORK_COMPANY' => $arUser['НаименованиеЮр'],
            'NAME' => $arUser['НаименованиеРабочее'],
            'UF_FIO_DIR' => $arUser['ФИОДиректора'],
            'PERSONAL_STATE' => $arUser['Регион'],
            'PERSONAL_CITY' => $arUser['Город'],
            'UF_UR_ADR' => $arUser['ЮрАдрес'],
            'PERSONAL_PHONE' => $arUser['Телефон'],
            'PERSONAL_STREET' => $arUser['АдресДоставки'],
            'UF_VK_OTHER' => $arUser['Вконтакте'],
            'UF_INST_OTHER' => $arUser['Instagram'],
            'UF_FB_OTHER' => $arUser['Facebook'],
            'UF_DISCOUNT_COMMON' => $arUser['Скидка'],
            'UF_DISCOUNT_VHD' => $arUser['СкидкаНаВходныеДвери'],
            'UF_DISCOUNT_MKD' => $arUser['СкидкаНаМежкомнатныеДвери'],
            'UF_DISCOUNT_POL' => $arUser['СкидкаНаНапольныеПокрытия'],
            'UF_DISCOUNT_FUR' => $arUser['СкидкаНаФурнитуру'],
            'UF_OTSROCHKA_DAY' => $arUser['ОтсрочкаДней'],
            'UF_OTSROCHKA_RUB' => $arUser['ОтсрочкаРублей'],
            'UF_VITR_ALL' => $arUser['ВитринВсего'],
            'UF_KONT_LITSO_FIO' => $arUser['КонтактноеЛицо']['ФИО'],
        ];

        //КонтактноеЛицо
        //РегиональныйМенеджер
        //ОтветственныйМенеджер

        $user = new \CUser();

        $userID = $user->Add($arFields);

        if (!$userID) {
            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
        }

        return [
            'ID' => $userID,
            'LOGIN' => $arFields['LOGIN'],
            'EMAIL' => $arFields['EMAIL'],
            'PASSWORD' => $arFields['PASSWORD'],
        ];
    }

    public function import($arData)
    {
        if (!$arData) {
            throw new \Exception('wrong arData');
        }

        $arNewUsers = [];

        foreach ($arData['OBJECTS'] as $arObj) {
            // получить пользователя по логину
            $dbUser = \CUser::GetByLogin($arObj["Код"]);
            $arUser = $dbUser->GetNext();

            Debug::dump($arUser);

            if (!$arUser) {
                // создать
                $arNewUsers[] = $this->_create($arObj);
            } else {
                // обновить

            }
        }

        // отправить уведомления новым пользователям
        Debug::dump($arNewUsers);
    }

    public function export()
    {
        // TODO: Implement export() method.
    }

}