<?php define("NEED_AUTH", true);?>
<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
//\Bitrix\Main\Diag\Debug::dump($arResult);
?>

<div class="l-tnd-pc-grid">
    <div class="l-tnd-pc-grid__body l-tnd-pc-grid__body--normal">
        <br>
        <?/*?>
        <form class="c-data-filter">
            <div class="c-data-filter__label">ФИЛЬТР</div>
            <div class="c-data-filter__input-group">
                <input type="text" class="c-data-filter__input-field dd-field js-set-date" placeholder="Дата заказа">
                <select class="c-data-filter__input-field dd-field pc-cab-select js-select">
                    <option data-display="Статус заказа">Не выбрано</option>
                    <option value="1">Some option</option>
                    <option value="2">Another option</option>
                    <option value="3" disabled>A disabled option</option>
                    <option value="4">Potato</option>
                </select>
                <input type="text" class="c-data-filter__input-field dd-field js-set-date" placeholder="Дата исполнения">
                <input type="text" class="c-data-filter__input-field" placeholder="Сумма, руб.">
                <input type="text" class="c-data-filter__input-field" placeholder="Предоплата">
                <input type="text" class="c-data-filter__input-field" placeholder="Остаток">
            </div>
        </form>
        <?//*/?>

        <table class="c-table">
            <tbody class="c-table__body">
            <tr class="c-table__table-row c-table__table-row--header">
                <th class="c-table__table-cell c-table__table-cell--header">№</th>
                <th class="c-table__table-cell c-table__table-cell--header" width="32%;">Дата заказа</th>
                <th class="c-table__table-cell c-table__table-cell--header" width="32%;">Статус заказа</th>
                <th class="c-table__table-cell c-table__table-cell--header" width="25%;">Сумма, руб.</th>
                <?/*?>
                <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Дата исполнения</th>
                <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Предоплата</th>
                <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Остаток</th>
                <?*/?>
            </tr>


            <? foreach ($arResult['ORDERS'] as $arOrder): ?>
                <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                    <td class="c-table__table-cell"><?=$arOrder['ACCOUNT_NUMBER']?></td>
                    <td class="c-table__table-cell"><?=$arOrder['DATE_INSERT']->toString()?></td>
                    <td class="c-table__table-cell"><?=$arOrder['PROPS']['EXT_STATUS_UR']?></td>
                    <td class="c-table__table-cell"><?=$arOrder['PRICE_FORMATED']?></td>
                    <?/*?>
                    <td class="c-table__table-cell"><?=$arOrder['PROPS']['FINISH_DATE_UR']?></td>
                    <td class="c-table__table-cell"><?=$arOrder['PROPS']['PREPAY_UR']?></td>
                    <td class="c-table__table-cell"><?=$arOrder['PROPS']['OSTATOK_UR']?></td>
                    <?*/?>
                </tr>

                <? if (count($arOrder['ITEMS']) > 0) :?>

                    <tr class="c-table__table-row c-table__sub-table-wrp">
                        <td colspan="4">
                            <table class="c-table">
                                <tbody class="c-table__body">
                                <tr class="c-table__table-row c-table__table-row--header">
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">№</th>
                                    <?/*?>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Категория продукта</th>
                                    <?*/?>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="61.44%;">Наименование</th>
                                    <?/*?>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Статус заказа</th>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Дата исполнения</th>
                                    <?*/?>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Количество,шт.</th>
                                    <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Сумма, руб.</th>
                                </tr>
                                <? $j = 0; foreach ($arOrder['ITEMS'] as $arItem): $j++; ?>
                                    <? //\Bitrix\Main\Diag\Debug::dump($arItem) ?>
                                    <tr class="c-table__table-row c-table__table-row--hover c-table__table-row--sub-row">
                                        <td class="c-table__table-cell"><?=$j;?></td>
                                        <?/*?>
                                        <td class="c-table__table-cell">
                                            <div class="line-clamp-outer">
                                                <span class="inner-line-clamp">Двери межкомнатные</span>
                                            </div>
                                        </td>
                                        <?*/?>
                                        <td class="c-table__table-cell">
                                            <div class="line-clamp-outer">
                                                <span class="inner-line-clamp"><?=$arItem['NAME']?></span>
                                            </div>
                                        </td>
                                        <?/*?>
                                        <td class="c-table__table-cell">STATUS</td>
                                        <td class="c-table__table-cell">FINISH_DATE</td>
                                        <?*/?>
                                        <td class="c-table__table-cell"><?=number_format ( $arItem['QUANTITY'], 2, ',', ' ' )?></td>
                                        <td class="c-table__table-cell"><?=number_format ( $arItem['QUANTITY'] * $arItem['PRICE'], 2, ',', ' ' )?></td>
                                    </tr>
                                <? endforeach; ?>
                                <?/*?>
                                <tr class="c-table__table-row">
                                    <td colspan="6" data-var-colspan="2" class="c-table__table-cell c-table__copy-link js-table-cell-no-hide">
                                        копировать заказ
                                    </td>
                                    <td colspan="1" class="c-table__table-cell c-table__table-cell--total-sum">1  309 078, 00</td>
                                </tr>
                                <?*/?>
                                <tr class="c-table__table-row">
                                    <td colspan="7" data-var-colspan="3" class="c-table__table-cell c-table__table-cell--close-btn js-table-cell-no-hide js-tableCloseBtn">скрыть</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                <? endif; ?>
            <? endforeach; ?>

            <?/*?>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">1</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">2</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">3</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">4</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <tr class="c-table__table-row c-table__sub-table-wrp">
                <td colspan="7">
                    <table class="c-table">
                        <tbody class="c-table__body">
                        <tr class="c-table__table-row c-table__table-row--header">
                            <th class="c-table__table-cell c-table__table-cell--header">№</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Категория продукта</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Наименование</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Статус заказа</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Дата исполнения</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Колличество,шт.</th>
                            <th class="c-table__table-cell c-table__table-cell--header" width="15.36%;">Сумма, руб.</th>
                        </tr>
                        <tr class="c-table__table-row c-table__table-row--hover c-table__table-row--sub-row">
                            <td class="c-table__table-cell">1</td>
                            <td class="c-table__table-cell">
                                <div class="line-clamp-outer">
                                    <span class="inner-line-clamp">Двери межкомнатные</span>
                                </div>
                            </td>
                            <td class="c-table__table-cell">
                                <div class="line-clamp-outer">
                                    <span class="inner-line-clamp">Прага лиственница</span>
                                </div>
                            </td>
                            <td class="c-table__table-cell">07.07.2017</td>
                            <td class="c-table__table-cell">999 905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                        </tr>
                        <tr class="c-table__table-row c-table__table-row--hover c-table__table-row--sub-row">
                            <td class="c-table__table-cell">1</td>
                            <td class="c-table__table-cell">07.07.2017</td>
                            <td class="c-table__table-cell">Формируется</td>
                            <td class="c-table__table-cell">07.07.2017</td>
                            <td class="c-table__table-cell">999 905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                        </tr>
                        <tr class="c-table__table-row c-table__table-row--hover c-table__table-row--sub-row">
                            <td class="c-table__table-cell">1</td>
                            <td class="c-table__table-cell">07.07.2017</td>
                            <td class="c-table__table-cell">Формируется</td>
                            <td class="c-table__table-cell">07.07.2017</td>
                            <td class="c-table__table-cell">999 905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                            <td class="c-table__table-cell">905 189, 00</td>
                        </tr>
                        <tr class="c-table__table-row">
                            <td colspan="6" data-var-colspan="2" class="c-table__table-cell c-table__copy-link js-table-cell-no-hide">
                                копировать заказ
                            </td>
                            <td colspan="1" class="c-table__table-cell c-table__table-cell--total-sum">1  309 078, 00</td>
                        </tr>
                        <tr class="c-table__table-row">
                            <td colspan="7" data-var-colspan="3" class="c-table__table-cell c-table__table-cell--close-btn js-table-cell-no-hide js-tableCloseBtn">скрыть</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">5</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <tr class="c-table__table-row c-table__table-row--hover js-tableRow">
                <td class="c-table__table-cell">6</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">Формируется</td>
                <td class="c-table__table-cell">07.07.2017</td>
                <td class="c-table__table-cell">999 905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
                <td class="c-table__table-cell">905 189, 00</td>
            </tr>
            <?*/?>
            </tbody>
        </table>

        <?/*?>
        <a href="#" class="c-table__show-more-btn">Показать еще</a>
        <?*/?>

        <?/*?>
        <div class="statistic-chart-container">
            <div id="chart-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
        <?*/?>

        <?/*?>
                            <div class="l-link-center-pos">
                                <a href="#" class="tnd-btn tnd-btn--sm-radius tnd-btn__uppercase tnd-btn--black tnd-btn--lg-fs tnd-btn--padding-fit">вернуться назад</a>
                            </div>
                            <?//*/?>
    </div> <!-- .l-tnd-pc-grid__body -->
</div><!-- .l-tnd-pc-grid -->