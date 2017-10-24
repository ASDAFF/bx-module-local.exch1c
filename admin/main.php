<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

$arModConf = include __DIR__ . '/../mod_conf.php';
// нужна для управления правами модуля
$module_id = strtolower($arModConf['name']);

define('ADMIN_MODULE_NAME', $module_id);

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/install/index.php");
Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage($arModConf['name'] . '_MODULE_NAME'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if(!Loader::includeModule($module_id))
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    ShowError('Ошибка подключения модуля');
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}
?>

<?
$aTabs = [];
$aTabs[] = array("DIV" => $module_id."_users", "TAB" => 'Обмен контрагентами', "TITLE" => 'Тестирование обмена контрагентами', 'INC' => 'users.php');
$aTabs[] = array("DIV" => $module_id."_stores", "TAB" => 'Обмен остатками', "TITLE" => 'Тестирование обмена остатками', 'INC' => 'stores.php');
$aTabs[] = array("DIV" => $module_id."_orders", "TAB" => 'Обмен заказами', "TITLE" => 'Тестирование обмена заказами', 'INC' => 'orders.php');
$tabControl = new \CAdminTabControl("exch1cTabControl", $aTabs, true, true);

$tabControl->Begin();

foreach ($aTabs as $aTab) {
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td colspan="2">
            <?include $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$module_id."/admin/inc/" . $aTab['INC'];?>
        </td>
    </tr>
    <?
}
$tabControl->End(); ?>

<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>