<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arConfCommon = [
    'name' => 'LOCAL.EXCH1C',
    'ns' => 'Local\Exch1c',
    'nsTables' => 'Local\Exch1c\Tables',
    'prefix' => 'local_exch1c',
];

/**
 * @var array $arCstmProps
 * пользовательские поля
 * элемент массива ["объект для добавления поля", "UF_код", "тип", "название"]
 */
$arCstmProps = [
    // кастомные поля пользователя
    ['USER', 'UF_RAION', 'string', 'Район'],
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
    ['USER', 'UF_KONT_LITSO_ID', 'string', 'Контактное лицо ИД'],
    ['USER', 'UF_KONT_LITSO_FIO', 'string', 'Контактное лицо ФИО'],

    ['USER', 'UF_REGMAN_ID', 'string', 'Региональный менеджер ИД'],
    ['USER', 'UF_REGMAN_FIO', 'string', 'Региональный менеджер ФИО'],
    ['USER', 'UF_REGMAN_PHONE', 'string', 'Региональный менеджер Телефон'],
    ['USER', 'UF_REGMAN_EMAIL', 'string', 'Региональный менеджер Email'],

    ['USER', 'UF_LOCMAN_ID', 'string', 'Ответственный менеджер ИД'],
    ['USER', 'UF_LOCMAN_FIO', 'string', 'Ответственный менеджер ФИО'],
    ['USER', 'UF_LOCMAN_PHONE', 'string', 'Ответственный менеджер Телефон'],
    ['USER', 'UF_LOCMAN_EMAIL', 'string', 'Ответственный менеджер Email'],
    ['USER', 'UF_STATUS', 'string', 'Статус'],

    // поля пользователя для запроса изменений
    ['USER', 'UF_2_WORK_COMPANY', 'string', 'Служебное Название компании'],
    ['USER', 'UF_2_NAME', 'string', 'Служебное Название магазина'],
    ['USER', 'UF_2_FIO_DIR', 'string', 'Служебное ФИО Директора'],
    ['USER', 'UF_2_PERSONAL_STATE', 'string', 'Служебное Регион'],
    ['USER', 'UF_2_RAION', 'string', 'Служебное Район'],
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
    ['USER', 'UF_IS_IMPORTED', 'string', 'Служебное Получен из 1С'],
    //['USER', 'UF_IMPORT_DT', 'string', 'Служебное Дата последнего обновления из 1С'],
    ['USER', 'UF_EXPORT_DO', 'string', 'Служебное Требуется передать в 1С'],
    ['USER', 'UF_NEED_CONFIRM', 'string', 'Служебное ждет подтверждения из 1с'],
    ['USER', 'UF_EDIT_REQUEST_DT', 'string', 'Служебное дата запроса'],
    ['USER', 'UF_EDIT_RESPONS_DT', 'string', 'Служебное дата подтверждения'],

    // сгенерированный при создании пароль
    ['USER', 'UF_START_PASS', 'string', 'Служебное Сгенерированный при создании пароль'],
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
    ['SyncHistory', 'dt'],
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
        'NAME' => 'Запросы на регистрацию',

        'PROPS' => [
            ['NAME' => 'ФИО', 'CODE' => 'FIO'],
            ['NAME' => 'Телефон', 'CODE' => 'PHONE'],
        ]
    ],
];

/**
 * @var array $arEmailTypes
 * почтовые события
 */
$arEmailTypes = [
    [
        "EVENT_NAME"  => "LOCALEXCH1C_REGCONFIRM",
        "NAME"        => "Подтверждение регистрации",
        "LID"         => "ru",
        "SORT"        => 100,
        "DESCRIPTION" => "
#LOGIN# - Логин
#PASSWORD# - Пароль
        "
    ],

    [
        "EVENT_NAME"  => "LOCALEXCH1C_REGREQUEST",
        "NAME"        => "Запрос на регистрацию",
        "LID"         => "ru",
        "SORT"        => 100,
        "DESCRIPTION" => "
#FIO# - ФИО
#PHONE# - Телефон
        "
    ],

    [
        "EVENT_NAME"  => "LOCALEXCH1C_EDITREQUEST",
        "NAME"        => "Запрос на изменение персональных данных",
        "LID"         => "ru",
        "SORT"        => 100,
        "DESCRIPTION" => "
#FIO# - ФИО Пользователя
#LINK# - Ссылка
        "
    ],
];

/**
 * @var array $arEmailTypes
 * почтовые шаблоны
 */
$arEmailTmpls = [
    [
        "ACTIVE" => "Y",
        "EVENT_NAME" => "LOCALEXCH1C_REGCONFIRM",
        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
        "EMAIL_TO" => "#EMAIL#",
        "BCC" => "",
        "SUBJECT" => "Подтверждение регистрации на сайте #SITE_NAME#",
        "BODY_TYPE" => "html",
        "MESSAGE" => "
Добрый день.<br><br>
Ваши данные усешно прошли проверку на сайте \"#SITE_NAME#\".<br><br>

Для входа на сайт используйте данные:<br><br>
Логин: #LOGIN#<br>
Пароль: #PASSWORD#
",
    ],

    [
        "ACTIVE" => "Y",
        "EVENT_NAME" => "LOCALEXCH1C_REGREQUEST",
        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
        "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
        "BCC" => "",
        "SUBJECT" => "Запрос на регистрацию с сайта #SITE_NAME#",
        "BODY_TYPE" => "text",
        "MESSAGE" => "
ФИО: #FIO#
Телефон: #PHONE#
",
    ],

    [
        "ACTIVE" => "Y",
        "EVENT_NAME" => "LOCALEXCH1C_EDITREQUEST",
        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
        "EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
        "BCC" => "",
        "SUBJECT" => "Запрос на изменение персональных данных с сайта #SITE_NAME#",
        "BODY_TYPE" => "html",
        "MESSAGE" => "
Пользователь <b>#FIO#</b> отправил запрос на изменение информации. Ссылка для просмотра данных: <a href='#LINK#'>#LINK#</a>
",
    ],
];

/**
 * @var array $arSalePersonTypes
 * типы плательщиков
 */
$arSalePersonTypes = [
    ['NAME' => 'Тестовый тип плательщика',],
];

/**
 * @var array $arSaleOrderPropsGroups
 * группы свойств заказа
 */
$arSaleOrderPropsGroups = [
    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        'NAME' => 'Служебные',
    ],
];

/**
 * @var array $arSaleOrderProps
 * свойства заказа
 * Допустимые ключи:

    NAME - название свойства (тип плательщика зависит от сайта, а сайт - от языка; название должно быть на соответствующем языке);
    CODE - символьный код свойства.
    TYPE - тип свойства. Допустимые значения:
    CHECKBOX - флаг;
    TEXT - строка текста;
    SELECT - выпадающий список значений;
    MULTISELECT - список со множественным выбором;
    TEXTAREA - многострочный текст;
    LOCATION - местоположение;
    RADIO - переключатель.
    REQUIRED - флаг (Y/N) обязательное ли поле;
    DEFAULT_VALUE - значение по умолчанию;
    SORT - индекс сортировки;
    USER_PROPS - флаг (Y/N) входит ли это свойство в профиль покупателя;
    IS_LOCATION - флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта стоимости доставки (только для свойств типа LOCATION);
    IS_EMAIL - флаг (Y/N) использовать ли значение свойства как E-Mail покупателя;
    IS_PROFILE_NAME - флаг (Y/N) использовать ли значение свойства как название профиля покупателя;
    IS_PAYER - флаг (Y/N) использовать ли значение свойства как имя плательщика;
    IS_LOCATION4TAX - флаг (Y/N) использовать ли значение свойства как местоположение покупателя для расчёта налогов (только для свойств типа LOCATION);

    IS_FILTERED - свойство доступно в фильтре по заказам. С версии 10.0.
    IS_ZIP - использовать как почтовый индекс. С версии 10.0.
    IS_PHONE
    IS_ADDRESS

    DESCRIPTION - описание свойства;
    MULTIPLE

    UTIL - позволяет использовать свойство только в административной части. С версии 11.0.
 */
$arSaleOrderProps = [
    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное Требуется передать в 1С",
        "TYPE" => "TEXT",
        "CODE" => "EXPORT_DO",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное Получен из 1С",
        "TYPE" => "TEXT",
        "CODE" => "IS_IMPORTED",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное дата запроса",
        "TYPE" => "TEXT",
        "CODE" => "EDIT_REQUEST_DT",
    ],

    [
        "PERSON_TYPE_NAME" => 'Физическое лицо',
        "PROPS_GROUP_NAME" => 'Служебные',
        "NAME" => "Служебное дата подтверждения",
        "TYPE" => "TEXT",
        "CODE" => "EDIT_RESPONS_DT",
    ],
];

$arConfig = array_merge($arConfCommon, [
    'arCstmProps' => $arCstmProps,
    'arTables' => $arTables,
    'arIndexes' => $arIndexes,
    'arIblockTypes' => $arIblockTypes,
    'arIblocks' => $arIblocks,
    'arEmailTypes' => $arEmailTypes,
    'arEmailTmpls' => $arEmailTmpls,
    'arSalePersonTypes' => $arSalePersonTypes,
    'arSaleOrderPropsGroups' => $arSaleOrderPropsGroups,
    'arSaleOrderProps' => $arSaleOrderProps,
]);

return $arConfig;