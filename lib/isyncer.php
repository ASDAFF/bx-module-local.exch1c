<?php

namespace Local\Exch1c;

interface ISyncer
{
    public function __construct($arData);
    public function run();
}