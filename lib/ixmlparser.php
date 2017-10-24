<?php

namespace Local\Exch1c;

interface IXmlParser
{
    public function __construct($filePath);

    public function getData();
}