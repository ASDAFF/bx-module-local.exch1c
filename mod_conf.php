<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arCstmProps
 * пользовательские поля
 * элемент массива ["объект для добавления поля", "UF_код", "тип", "название"]
 */
$arCstmProps = [
    // кастомные поля пользователя
    ['USER', 'UF_FIO_DIR', 'string', 'ФИО Директора'],
    ['USER', 'UF_UR_ADR', 'string', 'Юридический адрес'],
    ['USER', 'UF_VK_OTHER', 'string', 'Вконтакте'],
    ['USER', 'UF_INST_OTHER', 'string', 'Instagram'],
    ['USER', 'UF_FB_OTHER', 'string', 'Facebook'],
    ['USER', 'UF_DISCOUNT_COMMON', 'string', 'Скидка'],
    ['USER', 'UF_DISCOUNT_VHD', 'string', 'Скидка на входные двери'],
    ['USER', 'UF_DISCOUNT_MKD', 'string', 'Скидка на межкомнатные двери'],
    ['USER', 'UF_DISCOUNT_POL', 'string', 'Скидка на напольные покрытия'],
    ['USER', 'UF_DISCOUNT_FUR', 'string', 'Скидка на фурнитуру'],
    ['USER', 'UF_OTSROCHKA_DAY', 'string', 'Отсрочка дней'],
    ['USER', 'UF_OTSROCHKA_RUB', 'string', 'Отсрочка рублей'],
    ['USER', 'UF_VITR_ALL', 'string', 'Витрин всего'],
    ['USER', 'UF_KONT_LITSO_FIO', 'string', 'Контактное лицо'],
    // поля пользователя для запроса изменений
    ['USER', 'UF_2_WORK_COMPANY', 'string', 'Служебное Название компании'],
    ['USER', 'UF_2_NAME', 'string', 'Служебное Название магазина'],
    ['USER', 'UF_2_FIO_DIR', 'string', 'Служебное ФИО Директора'],
    ['USER', 'UF_2_PERSONAL_STATE', 'string', 'Служебное Регион'],
    ['USER', 'UF_2_PERSONAL_CITY', 'string', 'Служебное Город'],
    ['USER', 'UF_2_UR_ADR', 'string', 'Служебное Юридический адрес'],
    ['USER', 'UF_2_PERSONAL_PHONE', 'string', 'Служебное Телефон'],
    ['USER', 'UF_2_EMAIL', 'string', 'Служебное Электронная почта'],
    ['USER', 'UF_2_PERSONAL_STREET', 'string', 'Служебное АдресДоставки'],
    ['USER', 'UF_2_VK_OTHER', 'string', 'Служебное Вконтакте'],
    ['USER', 'UF_2_INST_OTHER', 'string', 'Служебное Instagram'],
    ['USER', 'UF_2_FB_OTHER', 'string', 'Служебное Facebook'],
    ['USER', 'UF_2_KONT_LITSO_FIO', 'string', 'Служебное Контактное лицо'],
    // флаги для обмена с 1с
    ['USER', 'UF_EXPORT_DO', 'string', 'Служебное Требуется передать в 1С'],
    ['USER', 'UF_IS_NEW', 'string', 'Служебное новый клиент'],
    ['USER', 'UF_NEED_CONFIRM', 'string', 'Служебное ждет подтверждения из 1с'],
    ['USER', 'UF_EDIT_REQUEST_DT', 'string', 'Служебное дата запроса'],
    ['USER', 'UF_EDIT_RESPONS_DT', 'string', 'Служебное дата подтверждения'],
];

/**
 * @var array $arTables
 * ORM таблицы
 * Элемент массива - "имя класса таблицы", сама таблица должна быть описана в /lib/tables/
 */
$arTables = [
    'SyncHistory',
];

/**
 * @var array $arIndexes
 * индексы ORM таблиц
 * элемент массива - ["имя класса таблицы", "имя поля таблицы"]
 */
$arIndexes = [
    ['SyncHistory', 'dtsync'],
];

/**
 * @var array $arIblockTypes
 * типы инфоблоков
 */
$arIblockTypes = [
    'FORMS' => [
        'SECTIONS' => 'N',
        'SORT' => '100',
        'LANG' => [
            'ru' => [
                'NAME'=>'Формы модуля обмена',
//                'SECTION_NAME'=>'Sections',
                'ELEMENT_NAME'=>'Формы'
            ]
        ]
    ],
];

/**
 * @var array $arIblocks
 * инфоблоки
 */
$arIblocks = [
    'REGREQUESTS' => [
        'TYPE' => 'FORMS',
    ],
];

$arConfig = [
    'name' => 'LOCAL.EXCH1C',
    'ns' => 'Local\Exch1c',
    'nsTables' => 'Local\Exch1c\Tables',
    'prefix' => 'local_exch1c',
    'arCstmProps' => $arCstmProps,
    'arTables' => $arTables,
    'arIndexes' => $arIndexes,
    'arIblockTypes' => $arIblockTypes,
    'arIblocks' => $arIblocks,
];

return $arConfig;