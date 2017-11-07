<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = [
	"NAME" => Loc::getMessage("LOCAL_EXCH1C_REGREQUESTFORM_COMPONENT"),
	"DESCRIPTION" => Loc::getMessage("LOCAL_EXCH1C_REGREQUESTFORM_COMPONENT_DESCRIPTION"),
	"COMPLEX" => "N",
    "CACHE_PATH" => "Y",
	"PATH" => [
        "ID" => Loc::getMessage("LOCAL_EXCH1C_REGREQUESTFORM_COMPONENT_PATH_ID"),
        "NAME" => Loc::getMessage("ELOCAL_EXCH1C_REGREQUESTFORM_COMPONENT_PATH_NAME"),
		"CHILD" => [
			"ID" => Loc::getMessage("LOCAL_EXCH1C_REGREQUESTFORM_COMPONENT_CHILD_PATH_ID"),
			"NAME" => GetMessage("LOCAL_EXCH1C_REGREQUESTFORM")
		]
	],
];
?>