<?php

namespace Local\Exch1c;

interface IParser
{
    public function __construct($fileName, $filePrefix);

    public function getFileName();

    public function setDir($dir);

    public function getArray();
}