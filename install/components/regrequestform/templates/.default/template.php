<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($USER->IsAuthorized()): ?>
    <h3>Вы успешно авторизованы на сайте</h3>
<? else: ?>
    <div id="reg-area">

        <form method="post" action="" name="jsSendFormRegRequest">
            <input type="hidden" name="formId" value="SendFormRegRequest">
            <?=bitrix_sessid_post();?>

            <div class="row">
                <div class="col-md-8 col-md-offset-2 text-center">
                    <div class="login-block-container">
                        <h3>Заявка на регистрацию</h3>

                        <?//_p($_POST);?>
                        <span>ФИО</span>
                        <input type="text" name="REGISTER[FIO]" value="<?=$_POST['REGISTER']['FIO']?>">
                        <span>Телефон</span>
                        <input type="text" name="REGISTER[PHONE]" value="<?=$_POST['REGISTER']['PHONE']?>" class="masked-phone" required>


                        <div class="text-center">
                            <p class="errors-container errortext"></p>
                        </div>

                        <input type="submit" name="submit" value="Отправить заявку" class="login-block-container__btn">
                    </div>
                </div>
            </div>
        </form>

    </div>
<? endif ?>
