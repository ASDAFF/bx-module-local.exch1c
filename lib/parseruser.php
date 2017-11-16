<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserUser implements IParser
{

    private $_fileDir;
    private $_fileName;
    private $_filePrefixImport;
    private $_filePrefixExport;

    public function __construct($fileName, $filePrefixImport = '', $filePrefixExport = '')
    {
        $this->_fileName = $fileName;
        $this->_filePrefixImport = $filePrefixImport;
        $this->_filePrefixExport = $filePrefixExport;
    }

    public function getFileNameImport()
    {
        return  $this->_filePrefixImport . $this->_fileName;
    }

    public function getFileNameExport()
    {
        return  $this->_filePrefixExport . $this->_fileName;
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
        $filePath = $this->_fileDir . $this->_filePrefixImport . $this->_fileName;

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

    public function makeXml($arData) {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><V8Exch:_1CV8DtUD xmlns:V8Exch="http://www.1c.ru/V8/1CV8DtUD/" xmlns:core="http://v8.1c.ru/data" xmlns:v8="http://v8.1c.ru/8.1/data/enterprise/current-config" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>');
        $NS = array(
            'V8Exch' => 'http://www.1c.ru/V8/1CV8DtUD/',
            'v8' => 'http://v8.1c.ru/8.1/data/enterprise/current-config',
            // whatever other namespaces you want
        );

        // now register them all in the root
        foreach ($NS as $prefix => $name) {
            $xml->registerXPathNamespace($prefix, $name);
        }

        $rootNode = $xml->addChild('V8Exch:Data', null, $NS['V8Exch']);
        foreach ($arData as $arRow) {
            $userNode = $rootNode->addChild('v8:CatalogObject.Контрагенты', null, $NS['v8']);
            $userNode->addChild('v8:Код', $arRow['LOGIN']);
            $userNode->addChild('v8:НаименованиеЮр', $arRow['UF_2_WORK_COMPANY']);
            $userNode->addChild('v8:НаименованиеРабочее', $arRow['UF_2_NAME']);
            $userNode->addChild('v8:ФИОДиректора', $arRow['UF_2_FIO_DIR']);
            $userNode->addChild('v8:Регион', $arRow['UF_2_PERSONAL_STATE']);
            $userNode->addChild('v8:Район', $arRow['UF_2_RAION']);
            $userNode->addChild('v8:Город', $arRow['UF_2_PERSONAL_CITY']);
            $userNode->addChild('v8:ФИОКонтактноеЛицо', $arRow['UF_2_KONT_LITSO_FIO']);
            $userNode->addChild('v8:Телефон', $arRow['UF_2_PERSONAL_PHONE']);
            $userNode->addChild('v8:ЭлектроннаяПочта', $arRow['UF_2_EMAIL']);
            $userNode->addChild('v8:АдресДоставки', $arRow['UF_2_PERSONAL_STREET']);
            $userNode->addChild('v8:Вконтакте', $arRow['UF_2_VK_OTHER']);
            $userNode->addChild('v8:Instagram', $arRow['UF_2_INST_OTHER']);
            $userNode->addChild('v8:Facebook', $arRow['UF_2_FB_OTHER']);
        }

        return $xml;
    }
}