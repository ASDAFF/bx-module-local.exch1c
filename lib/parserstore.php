<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserStore implements IParser
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

        $arObjs = [];
        $arObjKods = [];

        foreach ($xml->Номенклатура as $xmlObj) {
            $kod = self::clearStr((string) $xmlObj->ИД, '');
            $arObjKods[] = $kod;

            $arObj = [
                'Код' => $kod,
                'ИД' => self::clearStr((string) $xmlObj->ИД),
                'СкладИД' => self::clearStr((string) $xmlObj->СкладИД),
                'Доступно' => self::clearStr((string) $xmlObj->Доступно),
            ];

            $arObjs[$kod] = $arObj;
        }

        $arResult = [
            'DATE' => $expDate,
            'CODES' => $arObjKods,
            'OBJECTS' => $arObjs,
        ];

        return $arResult;
    }

    public function makeXml($arData) {
        throw new \Exception('Экспорт остатков не предполагается');

/*        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><V8Exch:_1CV8DtUD xmlns:V8Exch="http://www.1c.ru/V8/1CV8DtUD/" xmlns:core="http://v8.1c.ru/data" xmlns:v8="http://v8.1c.ru/8.1/data/enterprise/current-config" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>');
        $NS = array(
            'V8Exch' => 'http://www.1c.ru/V8/1CV8DtUD/',
            'v8' => 'http://v8.1c.ru/8.1/data/enterprise/current-config',
            // whatever other namespaces you want
        );

        // now register them all in the root
        foreach ($NS as $prefix => $name) {
            $xml->registerXPathNamespace($prefix, $name);
        }

        return $xml;
*/
    }
}