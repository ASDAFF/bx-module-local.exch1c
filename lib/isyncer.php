<?php

namespace Local\Exch1c;

interface ISyncer
{
    public function import(FtpClient $ftpClient);
    public function export(FtpClient $ftpClient);
    public function unCheckExported($arData);
}