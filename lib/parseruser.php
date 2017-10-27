<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;

class ParserUser implements IParser
{

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

    public function getArray()
    {
        $fileData = file_get_contents($this->_filePrefix . $this->_fileName);
        return  $this->_filePrefix . $this->_fileName;
    }
}