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

    static public function clearStr($str, $placeholder = ' ') {
        $str = preg_replace('/\s+/', $placeholder, trim($str));

        $arBadChars = [
            chr(182) => '',
            chr(194) => '',
            chr(160) => '',
        ];
        $str = strtr($str, $arBadChars);

        return $str;
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
            $kod = self::clearStr((string) $xmlClient->Код, '');
            $arClientsKods[] = $kod;

            $arClient = [
                'Код' => $kod,
                'ВидКонтрагента' => self::clearStr((string) $xmlClient->ВидКонтрагента),
                'НаименованиеЮр' => self::clearStr((string) $xmlClient->НаименованиеЮр),
                'НаименованиеРабочее' => self::clearStr((string) $xmlClient->НаименованиеРабочее),
                'ФИОДиректора' => self::clearStr((string) $xmlClient->ФИОДиректора),
                'Регион' => self::clearStr((string) $xmlClient->Регион),
                'Город' => self::clearStr((string) $xmlClient->Город),
                'ЮрАдрес' => self::clearStr((string) $xmlClient->ЮрАдрес),
                'КонтактноеЛицо' => [
                    'ИД' => self::clearStr((string) $xmlClient->КонтактноеЛицо->ИД, ''),
                    'ФИО' => self::clearStr((string) $xmlClient->КонтактноеЛицо->ФИО),
                ],
                'Телефон' => self::clearStr((string) $xmlClient->Телефон, ''),
                'ЭлектроннаяПочта' => self::clearStr((string) $xmlClient->ЭлектроннаяПочта, ''),
                'АдресДоставки' => self::clearStr((string) $xmlClient->АдресДоставки),
                'Вконтакте' => self::clearStr((string) $xmlClient->Вконтакте, ''),
                'Instagram' => self::clearStr((string) $xmlClient->Instagram, ''),
                'Facebook' => self::clearStr((string) $xmlClient->Facebook, ''),
                'Скидка' => self::clearStr((string) $xmlClient->Скидка, ''),
                'СкидкаНаВходныеДвери' => self::clearStr((string) $xmlClient->СкидкаНаВходныеДвери, ''),
                'СкидкаНаМежкомнатныеДвери' => self::clearStr((string) $xmlClient->СкидкаНаМежкомнатныеДвери, ''),
                'СкидкаНаНапольныеПокрытия' => self::clearStr((string) $xmlClient->СкидкаНаНапольныеПокрытия, ''),
                'СкидкаНаФурнитуру' => self::clearStr((string) $xmlClient->СкидкаНаФурнитуру, ''),
                'ОтсрочкаДней' => self::clearStr((string) $xmlClient->ОтсрочкаДней, ''),
                'ОтсрочкаРублей' => self::clearStr((string) $xmlClient->ОтсрочкаРублей, ''),
                'ВитринВсего' => self::clearStr((string) $xmlClient->ВитринВсего, ''),
                'Статус' => self::clearStr((string) $xmlClient->Статус),
                'РегиональныйМенеджер' => [
                    'ИД' => self::clearStr((string) $xmlClient->РегиональныйМенеджер->ИД, ''),
                    'ФИО' => self::clearStr((string) $xmlClient->РегиональныйМенеджер->ФИО),
                    'Телефон' => self::clearStr((string) $xmlClient->РегиональныйМенеджер->Телефон, ''),
                    'ЭлектроннаяПочта' => self::clearStr((string) $xmlClient->РегиональныйМенеджер->ЭлектроннаяПочта, ''),
                ],
                'ОтветственныйМенеджер' => [
                    'ИД' => self::clearStr((string) $xmlClient->ОтветственныйМенеджер->ИД, ''),
                    'ФИО' => self::clearStr((string) $xmlClient->ОтветственныйМенеджер->ФИО),
                    'Телефон' => self::clearStr((string) $xmlClient->ОтветственныйМенеджер->Телефон, ''),
                    'ЭлектроннаяПочта' => self::clearStr((string) $xmlClient->ОтветственныйМенеджер->ЭлектроннаяПочта, ''),
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