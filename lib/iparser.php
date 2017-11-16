<?php

namespace Local\Exch1c;

interface IParser
{
    public function __construct($fileName, $filePrefixImport, $filePrefixExport);

    public function getFileNameImport();

    public function getFileNameExport();

    public function setDir($dir);

    public function getArray();

    public function makeXml($arData);

}