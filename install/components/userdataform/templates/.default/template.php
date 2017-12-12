<?php define("NEED_AUTH", true);?>
<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?/*?>
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
<?//*/?>
<div id="formTopMarker"></div>

<div class="page-section page-section--white-bg helper helper__pb-52px jsShowUserDataForm">
    <div class="pc-acdn">
        <? $isfirst = true; foreach ($arResult['USER_SECTIONS'] as $arUserSection) :
            if(isset($arUserSection['HIDDEN']) && $arUserSection['HIDDEN'] == 'Y') {
                continue;
            }?>
        <div class="pc-acdn__item">
            <div class="pc-acdn__header js-slide-trigger">
                <div class="pc-acdn__header-text"><?=$arUserSection['NAME']?></div>
            </div>

            <div class="pc-acdn__content js-slide-content <?echo !$isfirst ? 'd-none' : ''; $isfirst=false;?>">
                <ul class="profile-list">

                    <? foreach ($arUserSection['FIELDS'] as $arUserField) :?>
                        <? if(is_array($arUserField['VALUE'])) { continue; } ?>

                        <li class="profile-list__item" data-code="<?=$arUserField['CODE']?>" >
                            <? if ($arUserField['TYPE'] !== 'tmp') :?>
                                <div class="profile-list__item-title">
                                    <span class="profile-list__item-name"><?=$arUserField['NAME']?></span>
                                    <span class="dots"></span>
                                </div>
                                <div class="profile-list__item-value"><?=$arUserField['VALUE']?></div>
                            <? elseif($arUserField['VALUE']) : /*?>
                                <div class="editreqfild"><span>Запрос на изменение:</span> <?=$arUserField['VALUE']?></div>
                            <?*/ endif; ?>
                        </li>

                        <?/*?>
                        <div class="input-group">
                            <? if ($arUserField['TYPE'] !== 'tmp') :?>

                                <div class="input-group__name"><?=$arUserField['NAME']?></div>
                                <input type="<?=$arUserField['TYPE']?>"
                                       name="<?=$arUserField['CODE']?>"
                                       value="<?=$arUserField['VALUE']?>"
                                       class="input-group__item"
                                    <?=($arUserField['READONLY'] == 'Y') ? 'readonly' : ''?>>

                            <? elseif($arUserField['VALUE']) : ?>
                                <div class="editreqfild"><span>Запрос на изменение:</span> <?=$arUserField['VALUE']?></div>
                            <? endif; ?>

                        </div>
                        <?*/?>

                    <? endforeach; ?>
                </ul>
            </div>
        </div>

        <? endforeach; ?>
    </div>

    <a href="#" class="tnd-btn tnd-btn--sm-radius tnd-btn__uppercase tnd-btn--black tnd-btn--lg-fs tnd-btn--padding-fit tnd-btn__edit-profile jsDoEditUserDataForm">редактировать</a>
</div> <!-- /.page-section -->

<form method="post" name="jsSendUserDataForm" action="" enctype="multipart/form-data" style="display:none;" class="jsSendUserDataForm">
    <input type="hidden" name="formId" value="UserDataForm"/>
    <?/*?>
    <input type="file" class="u-invisible" id="upload-avatar">
    <?//*/?>
    <?=bitrix_sessid_post();?>

    <div class="page-section page-section--white-bg helper helper__pb-52px page-section__edit-profile">
        <div class="page-section__pc-header">Режим редактирования</div>
        <div class="pc-acdn">

            <? foreach ($arResult['USER_SECTIONS'] as $arUserSection) : ?>

                <div class="pc-acdn__item">
                    <div class="pc-acdn__header">
                        <div class="pc-acdn__header-text pc-acdn__header-text--sm"><?=$arUserSection['NAME']?></div>
                    </div>
                    <div class="pc-acdn__content">
                        <? foreach ($arUserSection['FIELDS'] as $arUserField) : ?>
                            <? if ($arUserField['TYPE'] !== 'tmp') :?>
                                <?if($arUserField['CODE'] == 'PERSONAL_PHOTO'):?>
                                    <input type="<?=$arUserField['TYPE']?>"
                                           name="<?=$arUserField['CODE']?>"
                                           value="<?=$arUserField['VALUE']?>"
                                           id="upload-avatar"
                                           class="u-invisible"
                                    >
                                <?else:?>
                                    <div class="pc-acdn__input-group">
                                        <span class="input-label"><?=$arUserField['NAME']?></span>
                                        <input type="<?=$arUserField['TYPE']?>"
                                               name="<?=$arUserField['CODE']?>"
                                               value="<?=$arUserField['VALUE']?>"
                                               <?=($arUserField['CODE'] == 'PERSONAL_PHOTO') ? 'id="upload-avatar"' : ''; ?>
                                               class="pc-input pc-input--lg pc-input--edit
                                                      <?=($arUserField['CODE'] == 'PERSONAL_PHONE') ? 'masked-phone' : ''; ?>
                                                      <?=($arUserField['CODE'] == 'PERSONAL_PHOTO') ? 'u-invisible' : ''; ?>
                                                      "
                                            <?=($arUserField['READONLY'] == 'Y') ? 'readonly' : ''?>>
                                    </div>
                                <? endif; ?>
                            <? elseif($arUserField['VALUE']) : /*?>
                                <div class="editreqfild"><span>Запрос на изменение:</span> <?=$arUserField['VALUE']?></div>
                            <?*/ endif; ?>

                        <? endforeach; ?>
                    </div>
                </div>

            <? endforeach; ?>
        </div>

        <div class="text-center pc-acdn__content">

            <p class="errors-container errortext"></p>
        </div>

        <input type="submit" name="save" class="tnd-btn tnd-btn--sm-radius tnd-btn__uppercase tnd-btn--black tnd-btn--lg-fs tnd-btn--padding-fit tnd-btn__edit-profile" value="отправить на редактирование">
        <input type="reset" class="tnd-btn tnd-btn--sm-radius tnd-btn__uppercase tnd-btn--black tnd-btn--lg-fs tnd-btn--padding-fit tnd-btn__edit-profile jsResetUserDataForm" value="отмена">
    </div> <!-- /.page-section -->

</form>