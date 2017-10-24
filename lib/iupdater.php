<?php

namespace Local\Exch1c;

interface IUpdater
{
    public function __construct($ibId, $arData);

    public function sync();
}