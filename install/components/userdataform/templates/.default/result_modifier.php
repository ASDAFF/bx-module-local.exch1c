<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//\Bitrix\Main\Diag\Debug::dump($USER);

/** @var array $arResult */

$arResult['CSTM']['IMG']['SRC'] = '';
$arResult['CSTM']['FIO'] = '';
$arResult['CSTM']['STATUS'] = [
    'NAME' => '',
    'CLASS' => '',
];

foreach ($arResult['USER_SECTIONS'] as $arUserSection) {
    foreach ($arUserSection['FIELDS'] as $arUserField) {
        switch ($arUserField['CODE']) {
            case 'PERSONAL_PHOTO':
                if($arUserField['VALUE']['ID']) {
                    $arFile = CFile::ResizeImageGet($arUserField['VALUE']['ID'], ['width' => 88, 'height' => 88], BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    $arResult['CSTM']['IMG']['SRC'] = $arFile['src'];
                }
            break;

            case 'UF_KONT_LITSO_FIO':
                $arResult['CSTM']['UF_KONT_LITSO_FIO'] = $arUserField['VALUE'];
            break;

            case 'UF_STATUS':
                $arResult['CSTM']['STATUS']['NAME'] = $arUserField['VALUE'];

                switch(strtoupper($arUserField['VALUE'])) {
                    case 'GOLD':
                        $arResult['CSTM']['STATUS']['CLASS'] = 'b-user--gold-status';
                    break;

                    default:
                        continue;
                }
            break;

            default:
                continue;
        }
    }
}

if(!$arResult['CSTM']['IMG']['SRC']) {
    $arResult['CSTM']['IMG']['SRC'] = SITE_TEMPLATE_PATH . '/img/ava.jpg';
}