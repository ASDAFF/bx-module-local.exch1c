<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserUser implements IParser
{

    private $_fileDir;
    private $_fileName;
    private $_filePrefix;

    public function __construct($fileName, $filePrefix = '')
    {
        $this->_fileName = $fileName;
        $this->_filePrefix = $filePrefix;
    }

    public function getFileName()
    {
        return  $this->_filePrefix . $this->_fileName;
    }

    public function setDir($dir)
    {
        $this->_fileDir = $dir;
    }

    public function getArray()
    {
        $filePath = $this->_fileDir . $this->_filePrefix . $this->_fileName;

        if(!file_exists($filePath)) {
            throw new \Exception('Не верный путь к файлу ' . $filePath);
        }

        $fileData = file_get_contents($filePath);
        $xml = new \SimpleXMLElement($fileData);

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
            'CODES' => $arClientsKods,
            'OBJECTS' => $arClients,
        ];

        return $arResult;
    }
}