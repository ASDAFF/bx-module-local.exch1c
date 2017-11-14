<?php

namespace Local\Exch1c;

interface ISyncer
{
    public function import(Array $arData);
    public function export(FtpClient $ftpClient);
}