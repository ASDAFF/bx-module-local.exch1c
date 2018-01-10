<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($USER->IsAuthorized()): ?>
    <h3>Вы успешно авторизованы на сайте</h3>
<? else: ?>
<div class="b-login b-login__container js-b-login fp-gray-bg">
    <div class="b-login__inner silver-wood-bg">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-section__tnd-header helper helper__mb-75px">
                        <h2 class="tnd-section-title tnd-section-title--align-center">Заявка на регистрацию</h2>
                    </div>
                    <div class="b-login__close js-login-block-close">Закрыть</div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">

                    <div id="reg-area">

                        <form class="b-login__form" method="post" action="" name="jsSendFormRegRequest">
                            <input type="hidden" name="formId" value="SendFormRegRequest">
                            <?=bitrix_sessid_post();?>

                            <div class="b-login__form-inner">
                                <div class="b-login__form-group">
                                    <?//_p($_POST);?>
                                    <span class="input-label">ФИО</span>
                                    <input type="text" name="REGISTER[FIO]" value="<?=$_POST['REGISTER']['FIO']?>" class="pc-input pc-input--lg pc-input--fw">
                                </div>

                                <div class="b-login__form-group">
                                    <span class="input-label">Телефон</span>
                                    <input type="text" name="REGISTER[PHONE]" value="<?=$_POST['REGISTER']['PHONE']?>" class="pc-input pc-input--lg pc-input--fw masked-phone" required>
                                </div>

                                <div class="b-login__submit">
                                    <div class="text-center">
                                        <p class="errors-container errortext"></p>
                                    </div>

                                    <input type="submit" name="submit" value="Отправить заявку" class="tnd-btn tnd-btn--sm-size tnd-btn--sm-radius tnd-btn__uppercase tnd-btn--black tnd-btn--lg-fs tnd-btn--padding-fit">
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? endif ?>
