<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\UserTable;
use Local\Tp\TicketCategoryTable;
use Bitrix\Main\Type;

class XmlParserClient
    implements IXmlParser
{
    private $_filePath = '';

    public function __construct($filePath) {
        $this->_filePath = $_SERVER['DOCUMENT_ROOT'] . $filePath;
    }

    public function getData() {

        if(!file_exists($this->_filePath)) {
            throw new \Exception('Не верный путь к файлу ' . $this->_filePath);
        }

        $fileContent = file_get_contents($this->_filePath);

        $xml = new \SimpleXMLElement($fileContent);

        $expDate = \DateTime::createFromFormat('d.m.Y H:i:s', (string)$xml["ДатаВремя"]);

        $arClients = [];
        $arClientsKods = [];
        foreach ($xml->КлиентСайта as $xmlClient) {
            $kod = trim((string) $xmlClient->Код);
            $arClientsKods[] = $kod;

            $arClient = [
                'Код' => $kod,
                'ВидКонтрагента' => trim((string) $xmlClient->ВидКонтрагента),
                'НаименованиеЮр' => trim((string) $xmlClient->НаименованиеЮр),
                'НаименованиеРабочее' => trim((string) $xmlClient->НаименованиеРабочее),
                'ФИОДиректора' => trim((string) $xmlClient->ФИОДиректора),
                'Регион' => trim((string) $xmlClient->Регион),
                'Город' => trim((string) $xmlClient->Город),
                'ЮрАдрес' => trim((string) $xmlClient->ЮрАдрес),
                'КонтактноеЛицо' => [
                    'ИД' => trim((string) $xmlClient->КонтактноеЛицо->ID),
                    'ФИО' => trim((string) $xmlClient->КонтактноеЛицо->ФИО),
                ],
                'Телефон' => trim((string) $xmlClient->Телефон),
                'ЭлектроннаяПочта' => trim((string) $xmlClient->ЭлектроннаяПочта),
                'АдресДоставки' => trim((string) $xmlClient->АдресДоставки),
                'Вконтакте' => trim((string) $xmlClient->Вконтакте),
                'Instagram' => trim((string) $xmlClient->Instagram),
                'Facebook' => trim((string) $xmlClient->Facebook),
                'Скидка' => trim((string) $xmlClient->Скидка),
                'СкидкаНаВходныеДвери' => trim((string) $xmlClient->СкидкаНаВходныеДвери),
                'СкидкаНаМежкомнатныеДвери' => trim((string) $xmlClient->СкидкаНаМежкомнатныеДвери),
                'СкидкаНаНапольныеПокрытия' => trim((string) $xmlClient->СкидкаНаНапольныеПокрытия),
                'СкидкаНаФурнитуру' => trim((string) $xmlClient->СкидкаНаФурнитуру),
                'ОтсрочкаДней' => trim((string) $xmlClient->ОтсрочкаДней),
                'ОтсрочкаРублей' => trim((string) $xmlClient->ОтсрочкаРублей),
                'ВитринВсего' => trim((string) $xmlClient->ВитринВсего),
                'РегиональныйМенеджер' => [
                    'ИД' => trim((string) $xmlClient->РегиональныйМенеджер->ID),
                    'ФИО' => trim((string) $xmlClient->РегиональныйМенеджер->ФИО),
                    'Телефон' => trim((string) $xmlClient->РегиональныйМенеджер->Телефон),
                    'ЭлектроннаяПочта' => trim((string) $xmlClient->РегиональныйМенеджер->ЭлектроннаяПочта),
                ],
                'ОтветственныйМенеджер' => [
                    'ИД' => trim((string) $xmlClient->ОтветственныйМенеджер->ID),
                    'ФИО' => trim((string) $xmlClient->ОтветственныйМенеджер->ФИО),
                    'Телефон' => trim((string) $xmlClient->ОтветственныйМенеджер->Телефон),
                    'ЭлектроннаяПочта' => trim((string) $xmlClient->ОтветственныйМенеджер->ЭлектроннаяПочта),
                ],
            ];

            $arClients[$kod] = $arClient;
        }

        $arResult = [
            'DATE' => $expDate,
            'CLIENT_CODES' => $arClientsKods,
            'CLIENTS' => $arClients,
        ];

        return $arResult;
    }
}