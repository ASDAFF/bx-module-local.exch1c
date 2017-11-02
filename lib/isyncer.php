<?php

namespace Local\Exch1c;

interface ISyncer
{
    public function import($arData);
    public function export();
}