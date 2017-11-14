<?php

namespace Local\Exch1c;

interface IParser
{
    public function __construct($fileName, $filePrefixImport, $filePrefixExport);

    public function getFileName();

    public function setDir($dir);

    public function getArray();
}