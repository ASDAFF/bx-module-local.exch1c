<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserOrder implements IParser
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
            $orderStatus = $arRow['PROPS']['EXT_STATUS']
                ? $arRow['PROPS']['EXT_STATUS']
                : $arRow['PROPS']['EXT_STATUS_UR'];

            $orderNode = $rootNode->addChild('v8:DocumentObject.ЗаказКлиента', null, $NS['v8']);
            $orderNode->addChild('v8:ИД', $arRow['XML_ID']);
            $orderNode->addChild('v8:ИДСайт', $arRow['ID']);
            $orderNode->addChild('v8:Номер', $arRow['ACCOUNT_NUMBER']);
            $orderNode->addChild('v8:КодКлиента', $arRow['USER_LOGIN']);
            $orderNode->addChild('v8:ДатаСоздания', $arRow['DATE_INSERT']);
            $orderNode->addChild('v8:Сумма', $arRow['PRICE']);
            $orderNode->addChild('v8:Комментарий', $arRow['COMMENTS']);
            $orderNode->addChild('v8:Статус', $orderStatus);

            $prodsNode = $orderNode->addChild('v8:Товары', null);
            foreach($arRow['ITEMS'] as $arProd) {
                $prodNode = $prodsNode->addChild('v8:Товар', null);

                $sum = (float)$arProd['PRICE'] * (float)$arProd['QUANTITY'];

                $prodNode->addChild('v8:ИД', $arProd['PRODUCT_XML_ID']);
                $prodNode->addChild('v8:ИДСайт', $arProd['ID']);
                $prodNode->addChild('v8:Название', $arProd['NAME']);
                $prodNode->addChild('v8:Количество', $arProd['QUANTITY']);
                $prodNode->addChild('v8:Цена', $arProd['PRICE']);
                $prodNode->addChild('v8:Сумма', $sum);
                $prodNode->addChild('v8:Статус', $arProd['EXCH_STATUS']);
            }
        }

        return $xml;
    }
}