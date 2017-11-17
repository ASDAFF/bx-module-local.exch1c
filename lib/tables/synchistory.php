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

            // dt
            new Entity\DatetimeField('dt', [
                'required' => true,
                'default_value' => new Type\DateTime(),
            ]),

            // name
            new Entity\StringField('name', [
                'required' => true
            ]),

            // operation
            new Entity\StringField('operation', [
                'required' => true
            ]),

            // result
            new Entity\StringField('result', [
                'required' => false
            ]),

            // msg
            new Entity\StringField('msg', [
                'required' => false
            ]),
        ];
    }
}