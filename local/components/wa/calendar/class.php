<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Diag\Debug;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class WACalendar extends CBitrixComponent implements Controllerable
{

    public function configureActions()
    {
        $this->errorCollection = new ErrorCollection();

        return [
            'getMonth' => [
                'prefilters' => [],
            ],

        ];
    }

    public function getMonthAction(): AjaxJson
    {
        $post = $this->request->getPostList()->toArray();
        if (isset($post['MONTH']) && isset($post['YEAR']) && isset($post['ACTION']) && isset($post['ID'])) {
            $newCalendarHtml = false;
            $curDateValue = false;
            switch ($post['ACTION']) {
                case 'NEXT':
                    $curDateValue = $this->checkNextMonth(intval($post['MONTH']) + 1);
                    break;
                case 'PREV':
                    $curDateValue = $this->checkNextMonth(intval($post['MONTH']) - 1);
                    break;
            }
            if ($curDateValue) {
                if ($curDateValue['IS_NEXT_YEAR']) {
                    $curYear = $post['YEAR'];
                    $date = '01' . $curDateValue['MONTH'] . $curYear;
                    $events = $this->getObjectBookedDates($post['ID'], $date);
                    if ($events) {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], date($curYear + 1), $events);
                    } else {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], date($curYear + 1));
                    }
                } elseif ($curDateValue['IS_PREV_YEAR']) {
                    $curYear = $post['YEAR'];
                    $date = '01' . $curDateValue['MONTH'] . $curYear;
                    $events = $this->getObjectBookedDates($post['ID'], $date);
                    if ($events) {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], date($curYear - 1), $events);
                    } else {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], date($curYear - 1));
                    }
                } else {
                    $date = '01' . $curDateValue['MONTH'] . $post['YEAR'];
                    $events = $this->getObjectBookedDates($post['ID'], $date);
                    if ($events) {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], $post['YEAR'], $events);
                    } else {
                        $newCalendarHtml = $this->getMonth($curDateValue['MONTH'] . '.' . $post['YEAR'], $post['YEAR']);
                    }
                }
            }
            if ($newCalendarHtml) {
                return AjaxJson::createSuccess([
                    'html' => $newCalendarHtml
                ]);
            } else {
                return AjaxJson::createError(null, 'нет значений!');
            }
        } else {
            return AjaxJson::createError(null, 'нет значений!');
        }
    }

    public function getMonth($month, $year, $events = [])
    {
        $months = array(
            1 => 'Январь',
            2 => 'Февраль',
            3 => 'Март',
            4 => 'Апрель',
            5 => 'Май',
            6 => 'Июнь',
            7 => 'Июль',
            8 => 'Август',
            9 => 'Сентябрь',
            10 => 'Октябрь',
            11 => 'Ноябрь',
            12 => 'Декабрь'
        );
        $month = intval($month);
        $currentDate = [
            'month' => date('n'),
            'year' => date('Y'),
        ];
        $out = '
        <div class="c-month">
            <div class="c-month_head">';
        if ($month == $currentDate['month'] && $year == $currentDate['year']) {
        } else {
            $out .= '<button class="c-month_arrow" type="button" onclick="getPrevMonth(' . $month . ',' . $year . ')">
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 13L1 7L7 1" stroke="black" stroke-width="1.54984" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>';
        }
        $out .= '<span>' . $months[$month] . ' ' . $year . '</span>
                <button class="c-month_arrow" type="button" onclick="getNextMonth(' . $month . ',' . $year . ')">
                    <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 13L8 7L1 1" stroke="black" stroke-width="1.54984" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
			<table class="c-month_body">
                <tr>
                    <th>Пн</th>
                    <th>Вт</th>
                    <th>Ср</th>
                    <th>Чт</th>
                    <th>Пт</th>
                    <th>Сб</th>
                    <th>Вс</th>
                </tr>';
        $day_week = date('N', mktime(0, 0, 0, $month, 1, $year));
        $day_week--;
        $out .= '<tr>';
        for ($x = 0; $x < $day_week; $x++) {
            $out .= '<td></td>';
        }
        $days_counter = 0;
        $days_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        for ($day = 1; $day <= $days_month; $day++) {
            $date_attribute = 'data-date="' . date("d.m.Y", strtotime($day . '.' . $month . '.' . $year)) . '"';
            $booked_day = false;
            $first_half = false;
            $second_half = false;
            $time_param = false;
            if (!empty($events)) {
                if (isset($events['booked']) && !empty($events['booked'])) {
                    foreach ($events['booked'] as $date) {
                        $date = explode('.', $date);
                        if (count($date) == 3) {
                            $y = explode(' ', $date[2]);
                            if (count($y) == 2) {
                                $date[2] = $y[0];
                            }
                            if ($day == intval($date[0]) && $month == intval($date[1]) && $year == $date[2]) {
                                $booked_day = true;
                            }
                        } elseif (count($date) == 2) {
                            if ($day == intval($date[0]) && $month == intval($date[1])) {
                                $booked_day = true;
                            }
                        } elseif ($day == intval($date[0])) {
                            $booked_day = true;
                        }
                    }
                    unset($date);
                }
                if (isset($events['second_half']) && !empty($events['second_half'])) {
                    foreach ($events['second_half'] as $date) {
                        $timeTemp = explode(':', $date['time']);
                        $time = $timeTemp[0] . ':' . $timeTemp[1];
                        $date = explode('.', $date['date']);
                        if (count($date) == 3) {
                            $y = explode(' ', $date[2]);
                            if (count($y) == 2) {
                                $date[2] = $y[0];
                            }
                            if ($day == intval($date[0]) && $month == intval($date[1]) && $year == $date[2]) {
                                $second_half = true;
                                $time_param = $time;
                            }
                        } elseif (count($date) == 2) {
                            if ($day == intval($date[0]) && $month == intval($date[1])) {
                                $second_half = true;
                                $time_param = $time;
                            }
                        } elseif ($day == intval($date[0])) {
                            $second_half = true;
                            $time_param = $time;
                        }
                    }
                    unset($date);
                }
                if (isset($events['first_half']) && !empty($events['first_half'])) {
                    foreach ($events['first_half'] as $date) {
                        $timeTemp = explode(':', $date['time']);
                        $time = $timeTemp[0] . ':' . $timeTemp[1];
                        $date = explode('.', $date['date']);
                        if (count($date) == 3) {
                            $y = explode(' ', $date[2]);
                            if (count($y) == 2) {
                                $date[2] = $y[0];
                            }
                            if ($day == intval($date[0]) && $month == intval($date[1]) && $year == $date[2]) {
                                $first_half = true;
                                $time_param = $time;
                            }
                        } elseif (count($date) == 2) {
                            if ($day == intval($date[0]) && $month == intval($date[1])) {
                                $first_half = true;
                                $time_param = $time;
                            }
                        } elseif ($day == intval($date[0])) {
                            $first_half = true;
                            $time_param = $time;
                        }
                    }
                    unset($date);
                }

            }
            if ($booked_day) {
                $out .= '<td><span ' . $date_attribute . '>' . $day . '</span></td>';
            } elseif ($first_half) {
                $out .= '<td><span class="half-day-free_top" ' . $date_attribute . '>' . $day . '</span><div class="c-month_time">' . $time_param . '</div></td>';
            } elseif ($second_half) {
                $out .= '<td><span class="half-day-free_bottom" ' . $date_attribute . '>' . $day . '</span><div class="c-month_time">' . $time_param . '</div></td>';
            } else {
                $date = new DateTime($day . '.' . $month . '.' . $year);
                $now = new DateTime();
                if ($date < $now && $date->diff($now)->format('%a') != 0) {
                    $out .= '<td><span>' . $day . '</span></td>';
                } else {
                    $out .= '<td><span class="day-free" ' . $date_attribute . '>' . $day . '</span></td>';
                }
            }
            if ($day_week == 6) {
                $out .= '</tr>';
                if (($days_counter + 1) != $days_month) {
                    $out .= '<tr>';
                }
                $day_week = -1;
            }
            $day_week++;
            $days_counter++;
        }
        $out .= '</tr></table></div>';
        return $out;
    }

    public function checkNextMonth($monthNumber)
    {
        $nextYear = $prevYear = false;
        if ($monthNumber == 13) {
            $cur_month = 1;
            $nextYear = true;
        } elseif ($monthNumber == 0) {
            $cur_month = 12;
            $prevYear = true;
        } else {
            $cur_month = $monthNumber;
        }
        $cur_month = sprintf("%02d", $cur_month);
        return [
            'MONTH' => $cur_month,
            'IS_NEXT_YEAR' => $nextYear,
            'IS_PREV_YEAR' => $prevYear
        ];
    }

    public function getObjectBookedDates($elementId, $filterDate)
    {
        if ($elementId) {
            if (Loader::includeModule("highloadblock")) {
                $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();
                $data = $entity_data_class::getList(array(
                    "select" => array("*"),
                    "order" => array("ID" => "DESC"),
                    "filter" => ["UF_OBJECT_ID" => $elementId, '>=UF_ARRIVAL_DATE' => $filterDate],
                ));
                $booked_dates = [];
                while ($arData = $data->Fetch()) {
                    $arDates = _get_dates($arData['UF_ARRIVAL_DATE'], $arData['UF_DEPARTURE_DATE']);
                    foreach ($arDates as $date) {
                        $booked_dates[] = $date;
                    }
                }
                if (!empty($booked_dates)) {
                    $firstHalf = [];
                    $secondHalf = [];
                    $booked = [];
                    foreach ($booked_dates as $arBookedPeriod) {
                        if ($arBookedPeriod['ar_date']['time'] != '08:00:00' || $arBookedPeriod['ar_date']['time'] != '09:00:00') {
                            $firstHalf[] = [
                                'date' => $arBookedPeriod['ar_date']['date'],
                                'time' => $arBookedPeriod['ar_date']['time'],
                            ];
                        }
                        foreach ($arBookedPeriod['bet_date'] as $bet) {
                            $booked[] = $bet['date'];
                        }
                        $secondHalf[] = [
                            'date' => $arBookedPeriod['dep_date']['date'],
                            'time' => $arBookedPeriod['dep_date']['time'],
                        ];
                    }
                    return [
                        'booked' => $booked,
                        'second_half' => $secondHalf,
                        'first_half' => $firstHalf,
                    ];
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function executeComponent()
    {
        $this->arResult['ELEMENT_ID'] = $this->arParams['ELEMENT_ID'];
        $events = $this->getObjectBookedDates($this->arParams['ELEMENT_ID'], date('d.m.Y H:i:s'));
        if ($events) {
            $this->arResult['CALENDAR_HTML'] = $this->getMonth(date('m.Y'), date('Y'), $events);
        } else {
            $this->arResult['CALENDAR_HTML'] = $this->getMonth(date('m.Y'), date('Y'));
        }
        $this->includeComponentTemplate();
    }
}