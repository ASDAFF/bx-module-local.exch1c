<?php
namespace Local\Exch1c\Tables;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class SyncHistoryTable extends Entity\DataManager
{
    private static $modConf = [];

    public static function getTableName() {
        self::$modConf = include __DIR__ . '/../../mod_conf.php';

        return self::$modConf['prefix'] . '_sync_history';
    }

    public static function getMap() {
        return [
            // ID
            new Entity\IntegerField('id', [
                'primary' => true,
                'autocomplete' => true
            ]),

            // name
            new Entity\StringField('name', [
                'required' => true
            ]),

            // dtstart
            new Entity\DatetimeField('dtstart', [
                'required' => true
            ]),

            // dtsync
            new Entity\DatetimeField('dtsync', [
                'required' => true
            ]),
        ];
    }
}