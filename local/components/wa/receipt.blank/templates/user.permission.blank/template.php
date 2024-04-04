<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if ($arResult['BLANK']): ?>
    <? $APPLICATION->SetTitle($arResult['BLANK']['NAME']); ?>
    <style>
        @media print {
            #panel, #print-blank {
                display: none;
            }
        }
    </style>
    <section class="lk">
        <div class="lk_content">
            <div class="i-blank">
                <div class="i-blank_left">
                    <h3 class="i-title"><?= $arResult['BLANK']['NAME'] ?></h3>
                    <span class="i-subtitle"><?= $arResult['BLANK']['PAYMENT_STRING'] ?></span>
                    <table class="blank-table mb40">
                        <tr>
                            <th>ФИО</th>
                            <td><?= $arResult['BLANK']['FIO'] ?></td>
                        </tr>
                        <tr>
                            <th>Сроки пребывания</th>
                            <td><?= $arResult['BLANK']['DATE_INTERVAL'] ?></td>
                        </tr>
                        <? if ($arResult['BLANK']['ROUTE']): ?>
                            <tr>
                                <th>Маршрут</th>
                                <td><?= $arResult['BLANK']['ROUTE'] ?></td>
                            </tr>
                        <? endif; ?>
                        <tr>
                            <th>Состав группы</th>
                            <td>Общее число — <?= $arResult['BLANK']['VISITORS']['COUNT'] ?> человека</td>
                        </tr>
                        <? if ($arResult['BLANK']['VISITORS']['CATEGORIES']): ?>
                            <? foreach ($arResult['BLANK']['VISITORS']['CATEGORIES'] as $categoryName => $category): ?>
                                <tr>
                                    <th><?= $categoryName ?></th>
                                    <td><?= count($category) ?> человек <br>
                                        <? foreach ($category as $prefVisitor): ?>
                                            <? if ($prefVisitor['PREFERENTIAL_CATEGORY'] && $prefVisitor['PREF_DOC_NUMBER']): ?>
                                                <?=
                                                $prefVisitor['LAST_NAME'] . ' ' . $prefVisitor['NAME'] . ' ' . $prefVisitor['SECOND_NAME'] . ' - ' . $prefVisitor['PREFERENTIAL_CATEGORY'] . ' - ' . $prefVisitor['PREF_DOC_NUMBER'];
                                                ?>
                                            <? else: ?>
                                                <?=
                                                $prefVisitor['LAST_NAME'] . ' ' . $prefVisitor['NAME'] . ' ' . $prefVisitor['SECOND_NAME'];
                                                ?>
                                            <? endif; ?>
                                        <? endforeach; ?>
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        <? endif; ?>
                        <? if ($arResult['BLANK']['VISITORS']['LIST']): ?>
                            <? foreach ($arResult['BLANK']['VISITORS']['LIST'] as $visitor): ?>
                                <tr>
                                    <th></th>
                                    <td><?=
                                        $visitor['LAST_NAME'] . ' ' . $visitor['NAME'] . ' ' . $visitor['SECOND_NAME'];
                                        ?></td>
                                </tr>
                            <? endforeach; ?>
                        <? endif; ?>
                    </table>
                    <!--<div class="blank-map">
                        <img src="<? /*= ASSETS */ ?>images/map.jpeg" alt="">
                    </div>-->
                </div>
                <div class="i-blank_right">
                    <img src="<?= ASSETS ?>images/i-blank-logo.svg" alt="" class="blank_logo">

                    <div class="i-blank-description">

                        <span class="color-red">ЗАПРЕЩАЕТСЯ</span> нахождение в заповедной
                        зоне!
                        при себе
                        иметь данное разрешение, <span class="color-red">а также документ
              удостоверяющий личность и документ
              удостоверяющий льготу если имеется
              таковая.</span> Предъявить по требованию
                        государственного инспектора.
                    </div>
                    <? if ($arResult['BLANK']['QR_CODE']): ?>
                        <div class="qr">
                            <img src="<?= $arResult['BLANK']['QR_CODE'] ?>" alt="">
                        </div>
                    <? endif; ?>
                </div>
            </div>
            <div class="group-btn">
                <button class="primary-btn group-btn_item" type="button" id="print-blank">Печать бланка</button>
            </div>
        </div>
    </section>
    <!--    <script>
        let page_map = new PageMap(<? /*= CUtil::PhpToJSObject($arResult['MAP_JSON'])*/ ?>);
        page_map.init();
    </script>-->
<? endif; ?>