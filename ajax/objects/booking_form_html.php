<? require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

$form_type = $values['form_type'];
$time_limit_value = $values['time_limit_value'];
$object_id = $values['object_id'];
$default_time_select = '<div class="custom-select_item" data-id="8:00">8:00</div>
                                            <div class="custom-select_item" data-id="9:00">9:00</div>
                                            <div class="custom-select_item" data-id="10:00">10:00</div>
                                            <div class="custom-select_item" data-id="11:00">11:00</div>
                                            <div class="custom-select_item" data-id="12:00">12:00</div>
                                            <div class="custom-select_item" data-id="13:00">13:00</div>
                                            <div class="custom-select_item" data-id="14:00">14:00</div>
                                            <div class="custom-select_item" data-id="15:00">15:00</div>
                                            <div class="custom-select_item" data-id="16:00">16:00</div>
                                            <div class="custom-select_item" data-id="17:00">17:00</div>
                                            <div class="custom-select_item" data-id="18:00">18:00</div>
                                            <div class="custom-select_item" data-id="19:00">19:00</div>
                                            <div class="custom-select_item" data-id="20:00">20:00</div>
                                            <div class="custom-select_item" data-id="21:00">21:00</div>
                                            <div class="custom-select_item" data-id="22:00">22:00</div>
                                            <div class="custom-select_item" data-id="23:00">23:00</div>';

if (isset($form_type) && isset($time_limit_value)) {
    $now = new DateTime();

    if (isset($object_id)) {
        Loader::includeModule("iblock");
        $res = CIBlockElement::GetList(array(), ["IBLOCK_ID" => IB_LOCATIONS, "ID" => $object_id], false, [], ["PROPERTY_START_TIME", "PROPERTY_END_TIME"]);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $object_start_time = $arFields['PROPERTY_START_TIME_VALUE'];
            $object_end_time = $arFields['PROPERTY_END_TIME_VALUE'];
        }
        $service_cost_values = [];
        $res = CIBlockElement::GetProperty(IB_LOCATIONS, $object_id, array("sort" => "asc"), array("CODE" => "SERVICE_COST"));
        while ($ob = $res->GetNext()) {
            $service_cost_values[] = $ob['VALUE_ENUM'];
        }
        if (isset($object_start_time) && isset($object_end_time)) {
            $ar_full_day = ["8:00", "9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00"];
            $a = array_slice($ar_full_day, array_keys($ar_full_day, $object_start_time)[0]);
            $b = array_slice($a, 0, array_keys($a, $object_end_time)[0] + 1);
            $time_select_html = '';
            foreach ($b as $time_value) {
                $time_select_html .= '<div class="custom-select_item" data-id="' . $time_value . '">' . $time_value . '</div>';
            }
        }
        $arObjectGroups = [];
        $isRoute = false;
        $objectGroups = CIBlockElement::GetElementGroups($object_id, true);
        while ($arGroup = $objectGroups->Fetch()) {
            $arObjectGroups[] = [
                'ID' => $arGroup['ID'],
                'NAME' => $arGroup['NAME'],
                'DEPTH_LEVEL' => $arGroup['DEPTH_LEVEL'],
            ];
        }
        if (!empty($arObjectGroups)) {
            foreach ($arObjectGroups as $objectGroup) {
                if ($objectGroup['ID'] == '98') {
                    $isRoute = true;
                }
            }
        }
    }

    switch ($form_type) {
        case 'admin_booking_new':
            if ($isRoute):
                ?>
                <div class="form-block form-block--mb30">
                    <h3 class="form-block_title">Временной интервал</h3>
                    <div id="date-time-select-block">
                        <div class="input-group">
                            <div class="input input--sm">
                                <label for="" class="input-label">Дата оформления <span
                                            class="color-red">*</span></label>
                                <input type="text" value="<?= $now->format('d.m.Y') ?>" class="input-date"
                                       required readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="m-input-dates m-input-dates--md js-input-date-group">
                                <div class="m-input-date-block">
                                    <label for="" class="input-label">Дата заезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                           size="25"
                                           required
                                           autocomplete="off">
                                </div>
                                <div class="m-input-date-block">
                                    <label for="" class="input-label">Дата выезда <span
                                                class="color-red">*</span></label>
                                    <input type="text" class="input-date second-range-input"
                                           name="PROPERTY[12][0][VALUE]"
                                           size="25" required autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?
            else:
                if ($time_limit_value == 'N'):
                    ?>
                    <div class="form-block form-block--mb30">
                        <h3 class="form-block_title">Временной интервал</h3>
                        <div id="date-time-select-block">
                            <div class="input-group">
                                <div class="radio-group" id="time-select-radio">
                                    <div class="radio" id="time-select-couple">
                                        <input type="radio" id="radio_07" data-period="couple" name="radio" checked>
                                        <label for="radio_07">
                                            <div class="radio_text">На несколько суток</div>
                                        </label>
                                    </div>
                                    <div class="radio" id="time-select-day">
                                        <input type="radio" id="radio_08" data-period="day" name="radio">
                                        <label for="radio_08">
                                            <div class="radio_text">Дневное пребывание</div>
                                        </label>
                                    </div>
                                </div>
                                <div class="input input--sm">
                                    <label for="" class="input-label">Дата оформления <span
                                                class="color-red">*</span></label>
                                    <input type="text" value="<?= $now->format('d.m.Y') ?>" class="input-date"
                                           required readonly>
                                </div>
                            </div>
                            <div class="input-group">
                                <div class="m-input-dates m-input-dates--md js-input-date-group">
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Дата заезда <span
                                                    class="color-red">*</span></label>
                                        <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                               size="25"
                                               required
                                               autocomplete="off">
                                    </div>
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Дата выезда <span
                                                    class="color-red">*</span></label>
                                        <input type="text" class="input-date second-range-input"
                                               name="PROPERTY[12][0][VALUE]"
                                               size="25" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="m-input-dates m-input-dates--md">
                                    <div class="m-input-date-block">
                                        <label for="">Время заезда <span class="color-red">*</span></label>
                                        <div class="custom-select custom-select--sm" id="arrival-time-select">
                                            <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда">Время заезда</span>
                                                <svg class="custom-select_icon" width="14" height="8"
                                                     viewBox="0 0 14 8"
                                                     fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                                </svg>
                                            </div>
                                            <div class="custom-select_body">
                                                <div class="custom-select_item" data-id="8:00">8:00</div>
                                                <div class="custom-select_item" data-id="9:00">9:00</div>
                                                <div class="custom-select_item" data-id="10:00">10:00</div>
                                                <div class="custom-select_item" data-id="11:00">11:00</div>
                                                <div class="custom-select_item" data-id="12:00">12:00</div>
                                                <div class="custom-select_item" data-id="13:00">13:00</div>
                                                <div class="custom-select_item" data-id="14:00">14:00</div>
                                                <div class="custom-select_item" data-id="15:00">15:00</div>
                                                <div class="custom-select_item" data-id="16:00">16:00</div>
                                                <div class="custom-select_item" data-id="17:00">17:00</div>
                                                <div class="custom-select_item" data-id="18:00">18:00</div>
                                                <div class="custom-select_item" data-id="19:00">19:00</div>
                                                <div class="custom-select_item" data-id="20:00">20:00</div>
                                                <div class="custom-select_item" data-id="21:00">21:00</div>
                                                <div class="custom-select_item" data-id="22:00">22:00</div>
                                                <div class="custom-select_item" data-id="23:00">23:00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-input-date-block">
                                        <label for="">Время выезда <span class="color-red">*</span></label>
                                        <div class="custom-select custom-select--sm" id="departure-time-select">
                                            <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда">Время выезда</span>
                                                <svg class="custom-select_icon" width="14" height="8"
                                                     viewBox="0 0 14 8"
                                                     fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                                </svg>
                                            </div>
                                            <div class="custom-select_body">
                                                <div class="custom-select_item" data-id="8:00">8:00</div>
                                                <div class="custom-select_item" data-id="9:00">9:00</div>
                                                <div class="custom-select_item" data-id="10:00">10:00</div>
                                                <div class="custom-select_item" data-id="11:00">11:00</div>
                                                <div class="custom-select_item" data-id="12:00">12:00</div>
                                                <div class="custom-select_item" data-id="13:00">13:00</div>
                                                <div class="custom-select_item" data-id="14:00">14:00</div>
                                                <div class="custom-select_item" data-id="15:00">15:00</div>
                                                <div class="custom-select_item" data-id="16:00">16:00</div>
                                                <div class="custom-select_item" data-id="17:00">17:00</div>
                                                <div class="custom-select_item" data-id="18:00">18:00</div>
                                                <div class="custom-select_item" data-id="19:00">19:00</div>
                                                <div class="custom-select_item" data-id="20:00">20:00</div>
                                                <div class="custom-select_item" data-id="21:00">21:00</div>
                                                <div class="custom-select_item" data-id="22:00">22:00</div>
                                                <div class="custom-select_item" data-id="23:00">23:00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? else: ?>
                    <div class="form-block form-block--mb30">
                        <h3 class="form-block_title">Дата и время заезда</h3>
                        <div id="date-time-select-block">
                            <div class="input-group">
                                <div class="input">
                                    <label for="" class="input-label">Дата оформления <span
                                                class="color-red">*</span></label>
                                    <input type="text" value="<?= $now->format('d.m.Y') ?>" class="input-date"
                                           required readonly>
                                </div>
                                <div class="m-input-dates m-input-dates--md">
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Дата заезда</label>
                                        <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                                               size="25"
                                               required
                                               autocomplete="off">
                                    </div>
                                    <div class="m-input-date-block">
                                        <label for="" class="input-label">Время заезда</label>
                                        <div class="custom-select custom-select--sm" id="time-select">
                                            <div class="custom-select_head">
                                                    <span class="custom-select_title"
                                                          data-default-value="Время заезда">
                                                        Время заезда
                                                    </span>
                                                <svg class="custom-select_icon" width="14" height="8"
                                                     viewBox="0 0 14 8"
                                                     fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                                </svg>
                                            </div>
                                            <div class="custom-select_body">
                                                <? if (isset($time_select_html) && !empty($time_select_html)): ?>
                                                    <?= $time_select_html ?>
                                                <? else: ?>
                                                    <?= $default_time_select ?>
                                                <? endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? if (isset($object_start_time) && isset($object_end_time)): ?>
                        <input type="hidden" id="time-start" value="<?= $object_start_time ?>">
                        <input type="hidden" id="time-end" value="<?= $object_end_time ?>">
                    <? endif; ?>
                <?
                endif;
            endif;
            break;
        case 'bron_object_booking':
            if ($time_limit_value == 'N'):
                ?>
                <div class="radio-group" id="service-cost" style="display: none">
                    <? if (count($service_cost_values) == 2): ?>
                        <div class="radio">
                            <input type="radio" id="radio_07" data-period="couple" name="radio" checked>
                            <label for="radio_07">
                                <div class="radio_text">На несколько суток</div>
                            </label></div>
                        <div class="radio">
                            <input type="radio" id="radio_08" data-period="day" name="radio">
                            <label for="radio_08">
                                <div class="radio_text">Дневное пребывание</div>
                            </label></div>
                    <? else: ?>
                        <? if ($service_cost_values[0] == "Дневное пребывание до определенного времени"): ?>
                            <input type="hidden" id="radio_08" data-period="day" name="radio">
                        <? else: ?>
                            <input type="hidden" id="radio_07" data-period="couple" name="radio">
                        <? endif; ?>
                    <? endif; ?>
                </div>
                <div class="m-input-dates m-input-dates--md js-input-date-group">
                    <div class="m-input-date-block">
                        <label for="">Дата заезда <span class="color-red">*</span></label>
                        <input type="text" name="PROPERTY[11][0][VALUE]"
                               class="input-date" required autocomplete="off">
                    </div>
                    <div class="m-input-date-block">
                        <label for="">Дата выезда <span class="color-red">*</span></label>
                        <input type="text" name="PROPERTY[12][0][VALUE]"
                               class="input-date second-range-input" required autocomplete="off">
                    </div>
                </div>

                <div class="m-input-dates m-input-dates--md">
                    <div class="m-input-date-block">
                        <label for="">Время заезда <span class="color-red">*</span></label>
                        <div class="custom-select custom-select--sm" id="arrival-time-select">
                            <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время заезда">Время заезда</span>
                                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                </svg>
                            </div>
                            <div class="custom-select_body">
                                <div class="custom-select_item" data-id="8:00">8:00</div>
                                <div class="custom-select_item" data-id="9:00">9:00</div>
                                <div class="custom-select_item" data-id="10:00">10:00</div>
                                <div class="custom-select_item" data-id="11:00">11:00</div>
                                <div class="custom-select_item" data-id="12:00">12:00</div>
                                <div class="custom-select_item" data-id="13:00">13:00</div>
                                <div class="custom-select_item" data-id="14:00">14:00</div>
                                <div class="custom-select_item" data-id="15:00">15:00</div>
                                <div class="custom-select_item" data-id="16:00">16:00</div>
                                <div class="custom-select_item" data-id="17:00">17:00</div>
                                <div class="custom-select_item" data-id="18:00">18:00</div>
                                <div class="custom-select_item" data-id="19:00">19:00</div>
                                <div class="custom-select_item" data-id="20:00">20:00</div>
                                <div class="custom-select_item" data-id="21:00">21:00</div>
                                <div class="custom-select_item" data-id="22:00">22:00</div>
                                <div class="custom-select_item" data-id="23:00">23:00</div>
                            </div>
                        </div>
                    </div>
                    <div class="m-input-date-block">
                        <label for="">Время выезда <span class="color-red">*</span></label>
                        <div class="custom-select custom-select--sm" id="departure-time-select">
                            <div class="custom-select_head">
                                    <span class="custom-select_title"
                                          data-default-value="Время выезда">Время выезда</span>
                                <svg class="custom-select_icon" width="14" height="8" viewBox="0 0 14 8"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                </svg>
                            </div>
                            <div class="custom-select_body">
                                <div class="custom-select_item" data-id="8:00">8:00</div>
                                <div class="custom-select_item" data-id="9:00">9:00</div>
                                <div class="custom-select_item" data-id="10:00">10:00</div>
                                <div class="custom-select_item" data-id="11:00">11:00</div>
                                <div class="custom-select_item" data-id="12:00">12:00</div>
                                <div class="custom-select_item" data-id="13:00">13:00</div>
                                <div class="custom-select_item" data-id="14:00">14:00</div>
                                <div class="custom-select_item" data-id="15:00">15:00</div>
                                <div class="custom-select_item" data-id="16:00">16:00</div>
                                <div class="custom-select_item" data-id="17:00">17:00</div>
                                <div class="custom-select_item" data-id="18:00">18:00</div>
                                <div class="custom-select_item" data-id="19:00">19:00</div>
                                <div class="custom-select_item" data-id="20:00">20:00</div>
                                <div class="custom-select_item" data-id="21:00">21:00</div>
                                <div class="custom-select_item" data-id="22:00">22:00</div>
                                <div class="custom-select_item" data-id="23:00">23:00</div>
                            </div>
                        </div>
                    </div>
                </div>
            <? else: ?>
                <div class="radio-group" id="service-cost" style="display: none"></div>
                <div class="m-input-dates m-input-dates--md">
                    <div class="m-input-date-block">
                        <label for="" class="input-label">Дата заезда</label>
                        <input type="text" class="input-date" name="PROPERTY[11][0][VALUE]"
                               size="25"
                               required
                               autocomplete="off">
                    </div>
                    <div class="m-input-date-block">
                        <label for="" class="input-label">Время заезда</label>
                        <div class="custom-select custom-select--sm" id="time-select">
                            <div class="custom-select_head">
                                                    <span class="custom-select_title"
                                                          data-default-value="Время заезда">
                                                        Время заезда
                                                    </span>
                                <svg class="custom-select_icon" width="14" height="8"
                                     viewBox="0 0 14 8"
                                     fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L7 7L13 1" stroke="#000"/>
                                </svg>
                            </div>
                            <div class="custom-select_body">
                                <? if (isset($time_select_html) && !empty($time_select_html)): ?>
                                    <?= $time_select_html ?>
                                <? else: ?>
                                    <?= $default_time_select ?>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <? if (isset($object_start_time) && isset($object_end_time)): ?>
                    <input type="hidden" id="time-start" value="<?= $object_start_time ?>">
                    <input type="hidden" id="time-end" value="<?= $object_end_time ?>">
                <? endif; ?>
            <?
            endif;
            break;
    }
} else {
    echo '<div>Ошибка! - Отсутствуют обязательные значения: тип формы или ограничение по времени</div>';
}