<?php define("NEED_AUTH", true);?>
<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="page-content">

    <form method="post" name="jsSendUserDataForm" action="" enctype="multipart/form-data">
        <input type="hidden" name="formId" value="UserDataForm"/>
        <?=bitrix_sessid_post();?>

        <? foreach ($arResult['USER_SECTIONS'] as $arUserSection) : ?>

            <h3><?=$arUserSection['NAME']?></h3>

            <? foreach ($arUserSection['FIELDS'] as $arUserField) : ?>
                <div class="input-group">
                    <? if ($arUserField['TYPE'] !== 'tmp') :?>

                        <div class="input-group__name"><?=$arUserField['NAME']?></div>
                        <input type="<?=$arUserField['TYPE']?>"
                               name="<?=$arUserField['CODE']?>"
                               value="<?=$arUserField['VALUE']?>"
                               class="input-group__item"
                               <?=($arUserField['READONLY'] == 'Y') ? 'readonly' : ''?>>

                    <? elseif($arUserField['VALUE']) :?>
                        <div class="editreqfild"><span>Запрос на изменение:</span> <?=$arUserField['VALUE']?></div>
                    <? endif; ?>

                </div>

            <? endforeach; ?>
        <? endforeach; ?>

        <div class="text-center">
            <p class="errors-container errortext"></p>
        </div>

        <input type="submit" name="save" class="submit" value="Отправить">
    </form>
</div>