<?php

namespace Local\Exch1c;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;

class SyncerUser implements ISyncer
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
        $pass = randString(7);

        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

        $arFields = [
            'LOGIN' => $arUser['Код'],
            'EMAIL' => $arUser['ЭлектроннаяПочта'],
            'PASSWORD' => $pass,
            'CONFIRM_PASSWORD' => $pass,
            'GROUP_ID' => [USER_GROUP_REGED_ID, USER_GROUP_OPT_ID],
            'ACTIVE' => 'Y',
            'LID' => $this->siteId,
            'WORK_PROFILE' => $arUser['ВидКонтрагента'],
            'WORK_COMPANY' => $arUser['НаименованиеЮр'],
            'NAME' => $arUser['НаименованиеРабочее'],
            'UF_FIO_DIR' => $arUser['ФИОДиректора'],
            'PERSONAL_STATE' => $arUser['Регион'],
            'UF_RAION' => $arUser['Район'],
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
            'UF_STATUS' => $arUser['Статус'],

            'UF_KONT_LITSO_ID' => $arUser['КонтактноеЛицо']['ИД'],
            'UF_KONT_LITSO_FIO' => $arUser['КонтактноеЛицо']['ФИО'],

            'UF_REGMAN_ID' => $arUser['РегиональныйМенеджер']['ИД'],
            'UF_REGMAN_FIO' => $arUser['РегиональныйМенеджер']['ФИО'],
            'UF_REGMAN_PHONE' => $arUser['РегиональныйМенеджер']['Телефон'],
            'UF_REGMAN_EMAIL' => $arUser['РегиональныйМенеджер']['ЭлектроннаяПочта'],

            'UF_LOCMAN_ID' => $arUser['ОтветственныйМенеджер']['ИД'],
            'UF_LOCMAN_FIO' => $arUser['ОтветственныйМенеджер']['ФИО'],
            'UF_LOCMAN_PHONE' => $arUser['ОтветственныйМенеджер']['Телефон'],
            'UF_LOCMAN_EMAIL' => $arUser['ОтветственныйМенеджер']['ЭлектроннаяПочта'],

            'UF_START_PASS' => $pass,
            'UF_IS_IMPORTED' => 'Y',
            'UF_IMPORT_DT' => $expDate,
        ];

        //КонтактноеЛицо

        $user = new \CUser();

        $userID = $user->Add($arFields);

        if (!$userID) {
//            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
            Debug::dump($user->LAST_ERROR);
            return false;
        }

        return [
            'ID' => $userID,
            'LOGIN' => $arFields['LOGIN'],
            'EMAIL' => $arFields['EMAIL'],
            'PASSWORD' => $arFields['PASSWORD'],
        ];
    }

    private function _update($arUser)
    {
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

        $arFields = [
            'LOGIN' => $arUser['Код'],
            'EMAIL' => $arUser['ЭлектроннаяПочта'],
//            'PASSWORD' => $pass,
//            'CONFIRM_PASSWORD' => $pass,
            'GROUP_ID' => [USER_GROUP_REGED_ID, USER_GROUP_OPT_ID],
            'ACTIVE' => 'Y',
            'LID' => $this->siteId,
            'WORK_PROFILE' => $arUser['ВидКонтрагента'],
            'WORK_COMPANY' => $arUser['НаименованиеЮр'],
            'NAME' => $arUser['НаименованиеРабочее'],
            'UF_FIO_DIR' => $arUser['ФИОДиректора'],
            'PERSONAL_STATE' => $arUser['Регион'],
            'UF_RAION' => $arUser['Район'],
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
            'UF_STATUS' => $arUser['Статус'],

            'UF_KONT_LITSO_ID' => $arUser['КонтактноеЛицо']['ИД'],
            'UF_KONT_LITSO_FIO' => $arUser['КонтактноеЛицо']['ФИО'],

            'UF_REGMAN_ID' => $arUser['РегиональныйМенеджер']['ИД'],
            'UF_REGMAN_FIO' => $arUser['РегиональныйМенеджер']['ФИО'],
            'UF_REGMAN_PHONE' => $arUser['РегиональныйМенеджер']['Телефон'],
            'UF_REGMAN_EMAIL' => $arUser['РегиональныйМенеджер']['ЭлектроннаяПочта'],

            'UF_LOCMAN_ID' => $arUser['ОтветственныйМенеджер']['ИД'],
            'UF_LOCMAN_FIO' => $arUser['ОтветственныйМенеджер']['ФИО'],
            'UF_LOCMAN_PHONE' => $arUser['ОтветственныйМенеджер']['Телефон'],
            'UF_LOCMAN_EMAIL' => $arUser['ОтветственныйМенеджер']['ЭлектроннаяПочта'],

            'UF_EDIT_RESPONS_DT' => $expDate,
            'UF_NEED_CONFIRM' => 'N',

//            'UF_START_PASS' => $pass,
        ];

        //КонтактноеЛицо

        $user = new \CUser();

        $userID = $user->Update($arUser['ID'], $arFields);

        if (!$userID) {
//            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
            Debug::dump($user->LAST_ERROR);
            return false;
        }

        return [
            'ID' => $userID,
            'LOGIN' => $arFields['LOGIN'],
            'EMAIL' => $arFields['EMAIL'],
            'PASSWORD' => $arFields['PASSWORD'],
        ];
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

        $arNewUsers = [];

        foreach ($arData['OBJECTS'] as $arObj) {
            $arResult['CNT']++;
            // получить пользователя по логину
            $dbUser = \CUser::GetByLogin($arObj["Код"]);
            $arUser = $dbUser->GetNext();

            //Debug::dump($arUser);

            $arTmpUser = false;

            if (!$arUser) {
                // создать
                $arTmpUser = $this->_create($arObj);

                if($arTmpUser) {
                    $arNewUsers[] = $arTmpUser;
                    $arResult['CNT_INS']++;
                } else {
                    $arResult['CNT_ERROR']++;
                }

            } else {
                $arObj['ID'] = $arUser['ID'];
                // обновить
                $arTmpUser = $this->_update($arObj);

                if($arTmpUser) {
                    $arResult['CNT_UPD']++;
                } else {
                    $arResult['CNT_ERROR']++;
                }
            }
        }

        // Удаляем файл на FTP
        $ftpClient->rmFtpImportFile();

        // отправить уведомления новым пользователям
        $this->sendEmailForNewUsers($arNewUsers);

        // лог
        $arResultMsg['msg'] = 'Всего записей: ' . $arResult['CNT']
            . '; создано: ' . $arResult['CNT_INS']
            . '; обновлено: ' . $arResult['CNT_UPD']
            . '; с ошибками: ' . $arResult['CNT_ERROR'] . ';';

        Tables\SyncHistoryTable::add([
            'name' => 'user',
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

        // Собираем всех пользователей для передачи
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
            'name' => 'user',
            'operation' => 'export',
            'result' => $arResultMsg['type'],
            'msg' => $arResultMsg['msg'],
        ]);

        return [
            'CNT' => count($arData),
        ];
    }

    protected function getDataForExport()
    {
        $arFieldsUF = [

            'UF_2_WORK_COMPANY', //'Служебное Название компании'
            'UF_2_NAME', //'Служебное Название магазина'
            'UF_FIO_DIR', //'ФИО Директора'
            'UF_2_FIO_DIR', //'Служебное ФИО Директора'
            'UF_2_PERSONAL_STATE', //'Служебное Регион'
            'UF_RAION', //'Район'
            'UF_2_RAION', //'Служебное Район'
            'UF_2_PERSONAL_CITY', //'Служебное Город'
            'UF_UR_ADR', //'Юридический адрес'
            'UF_2_UR_ADR', //'Служебное Юридический адрес'
            'UF_2_PERSONAL_PHONE', //'Служебное Телефон'
            'UF_2_EMAIL', //'Служебное Электронная почта'
            'UF_2_PERSONAL_STREET', //'Служебное АдресДоставки'
            'UF_VK_OTHER', //'Вконтакте'
            'UF_2_VK_OTHER', //'Служебное Вконтакте'
            'UF_INST_OTHER', //'Instagram'
            'UF_2_INST_OTHER', //'Служебное Instagram'
            'UF_FB_OTHER', //'Facebook'
            'UF_2_FB_OTHER', //'Служебное Facebook'
            'UF_KONT_LITSO_FIO', //'Контактное лицо'
            'UF_2_KONT_LITSO_FIO', //'Служебное Контактное лицо'
        ];

        $arFieldsSTANDART = [
            'ID',
            'LOGIN',
            'WORK_COMPANY', //'Название компании'
            'NAME', //'Название магазина'
            'PERSONAL_STATE', //'Регион'
            'PERSONAL_CITY', //'Город'
            'PERSONAL_PHONE', //'Телефон'
            'EMAIL', //'Электронная почта'
            'PERSONAL_STREET', //'АдресДоставки'
        ];

        $arFilter = [
            'UF_EXPORT_DO' => 'Y',
        ];

        $arParams = [
            'FIELDS' => $arFieldsSTANDART,
            'SELECT' => $arFieldsUF,
        ];

        $dbRes = \CUser::GetList(
            $by = "timestamp_x",
            $order = "desc",
            $arFilter,
            $arParams);

        $arData = [];
        while ($arRow = $dbRes->GetNext()) {
            $arData[] = $arRow;
        }

        return $arData;
    }

    public function unCheckExported($arData) {
        $expDate = (new \DateTime())->format('d.m.Y H:i:s');

        foreach ($arData as $arUser) {

            $arFields = [
                'UF_EXPORT_DO' => 'N',
                'UF_EDIT_REQUEST_DT' => $expDate,
            ];

            $user = new \CUser();

            $userID = $user->Update($arUser['ID'], $arFields);

            if (!$userID) {
//            throw new \Exception("Ошибка создания пользователя: " . $user->LAST_ERROR);
                Debug::dump($user->LAST_ERROR);
                return false;
            }
        }
    }

    protected function sendEmailForNewUsers($arUsers) {

        $arModConf = include __DIR__ . '/../mod_conf.php';
        Debug::dump(__DIR__ . '/../mod_conf.php');
        Debug::dump($arModConf);

        foreach ($arUsers as $arUser) {
//            Debug::dump($arUser);

            $tmplName = \Bitrix\Main\Config\Option::get(strtolower($arModConf['name']), $arModConf['name'].'_EMAIL_TMPL_REGCONFIRM');
            \CEvent::Send($tmplName, $this->siteId, $arUser);
        }
    }

}