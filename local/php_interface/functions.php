<?php

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;

function createCalendarDatesForUserForm($dates = [])
{
    //результирующий массив
    $return_arr = [];
    //массивы для проверки половины дня
    $first_half_day = [
        0 => '8:00:00',
        1 => '9:00:00',
        2 => '10:00:00',
        3 => '11:00:00',
        4 => '12:00:00',
        5 => '13:00:00',
        6 => '14:00:00',
    ];
    $second_half_day = [
        7 => '15:00:00',
        8 => '16:00:00',
        9 => '17:00:00',
        10 => '18:00:00',
        11 => '19:00:00',
        12 => '20:00:00',
        13 => '21:00:00',
        14 => '22:00:00',
        15 => '23:00:00',
    ];
    $res = createSelectedDateArr($dates);
    $now = new DateTime();
    foreach ($res as $date => $date_time) {
        if (count($date_time['time']) == 15 || count($date_time['time']) > 13) {
            $return_arr[$date] = [
                'date' => $date,
                'status' => 'disabled',
                'class' => 'flatpickr-disabled',
            ];
        } else {
            if (count(array_intersect($first_half_day, $date_time['time'])) >= 1 && count($date_time['time']) <= 7) {
                $return_arr[$date] = [
                    'date' => $date,
                    'status' => 'available',
                    'class' => 'second-half-day',
                ];
            } else {
                if (count(array_intersect($second_half_day, $date_time['time'])) >= 1 && count($date_time['time']) <= 8) {
                    $return_arr[$date] = [
                        'date' => $date,
                        'status' => 'available',
                        'class' => 'first-half-day',
                    ];
                } else {
                    if (count(array_intersect($first_half_day, $date_time['time'])) >= 6) {
                        $return_arr[$date] = [
                            'date' => $date,
                            'status' => 'available',
                            'class' => 'second-half-day',
                        ];
                    } else {
                        if (count(array_intersect($second_half_day, $date_time['time'])) >= 6) {
                            $return_arr[$date] = [
                                'date' => $date,
                                'status' => 'available',
                                'class' => 'first-half-day',
                            ];
                        }

                    }
                }
            }
        }
        $ar_date = explode('.', $date);
        $day = $ar_date[0];
        $next_date = (intval($day) + 1) . '.' . $ar_date[1] . '.' . $ar_date[2];
        $prev_date = (intval($day) - 1) . '.' . $ar_date[1] . '.' . $ar_date[2];
        if (isset($res[$next_date])) {
            if (count($res[$next_date]['time']) < 15) {
                if (!in_array('23:00:00', $date_time['time'])) {
                    if (!in_array('8:00:00', $res[$next_date]['time'])) {
                        if (isset($res[$prev_date])) {
                            if (count($res[$prev_date]['time']) < 15) {
                                if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                                    if (!in_array('8:00:00', $date_time['time'])) {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'available',
                                            'class' => 'first-half-day',
                                        ];
                                    }
                                } else {
                                    if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                }
                            }
                        } else {
                            if (DateTime::createFromFormat('d.m.Y', $prev_date)->format('d.m.Y') != $now->format('d.m.Y')) {
                                if (!in_array('8:00:00', $date_time['time'])) {
                                    if (count($date_time['time']) > 2) {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'available',
                                            'class' => 'first-half-day',
                                        ];
                                    } else {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                } else {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'disabled',
                                        'class' => 'flatpickr-disabled',
                                    ];
                                }
                            } else {
                                $return_arr[$date] = [
                                    'date' => $date,
                                    'status' => 'disabled',
                                    'class' => 'flatpickr-disabled',
                                ];
                            }
                        }
                    } else {
                        $return_arr[$date] = [
                            'date' => $date,
                            'status' => 'disabled',
                            'class' => 'flatpickr-disabled',
                        ];
                    }
                } else {
                    if (isset($res[$prev_date])) {
                        if (count($res[$prev_date]['time']) < 15) {
                            if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                                if (!in_array('8:00:00', $date_time['time'])) {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'available',
                                        'class' => 'first-half-day',
                                    ];
                                }
                            }
                        } else {
                            $return_arr[$date] = [
                                'date' => $date,
                                'status' => 'disabled',
                                'class' => 'flatpickr-disabled',
                            ];
                        }
                    } else {
                        if (DateTime::createFromFormat('d.m.Y', $prev_date)->format('d.m.Y') != $now->format('d.m.Y')) {
                            if (!in_array('8:00:00', $date_time['time'])) {
                                if (count($date_time['time']) > 2) {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'available',
                                        'class' => 'first-half-day',
                                    ];
                                } else {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'disabled',
                                        'class' => 'flatpickr-disabled',
                                    ];
                                }
                            } else {
                                $return_arr[$date] = [
                                    'date' => $date,
                                    'status' => 'disabled',
                                    'class' => 'flatpickr-disabled',
                                ];
                            }
                        } else {
                            $return_arr[$date] = [
                                'date' => $date,
                                'status' => 'disabled',
                                'class' => 'flatpickr-disabled',
                            ];
                        }
                    }
                }
            } else {
                if (isset($res[$prev_date])) {
                    if (count($res[$prev_date]['time']) < 15) {
                        if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                            if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                $return_arr[$date] = [
                                    'date' => $date,
                                    'status' => 'disabled',
                                    'class' => 'flatpickr-disabled',
                                ];
                            }
                        } else {
                            if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                $return_arr[$date] = [
                                    'date' => $date,
                                    'status' => 'disabled',
                                    'class' => 'flatpickr-disabled',
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    $now_in_array = false;
    foreach ($return_arr as $date => $date_params) {
        if (DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y') == $now->format('d.m.Y')) {
            $now_in_array = true;
        }
    }
    if (!$now_in_array) {
        if ($now->format('H') >= 13) {
            $return_arr[$now->format('d.m.Y')] = [
                'date' => $now->format('d.m.Y'),
                'status' => 'available',
                'class' => 'second-half-day',
            ];
        }
    }
    return $return_arr;
}

function createSelectedDateArr($dates = [])
{
    //массивы для сортировки (лень было придумывать названия)
    $arr_1 = [];
    $arr_2 = [];
    $arr_3 = [];
    $arr_4 = [];
    foreach ($dates as $booked_date) {
        if ($booked_date['ar_date']['date'] == $booked_date['dep_date']['date']) {
            if (!empty($arr_1)) {
                foreach ($arr_1 as $date => $date_time) {
                    if (DateTime::createFromFormat('d.m.Y', $booked_date['ar_date']['date'])->format('d.m.Y') == DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y')) {
                        $time = setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H'));
                        $arr_1[$date]['time'] = array_unique(array_merge($arr_1[$booked_date['ar_date']['date']]['time'], $time));
                    }
                }
                if (!empty($arr_1[$booked_date['ar_date']['date']])) {
                    $arr_1[$booked_date['ar_date']['date']]['time'] = array_merge($arr_1[$booked_date['ar_date']['date']]['time'], setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H')));
                } else {
                    $arr_1[$booked_date['ar_date']['date']] = [
                        'day' => $booked_date['ar_date']['date'],
                        'time' => setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H'))
                    ];
                }
            } else {

                $arr_1[$booked_date['ar_date']['date']] = [
                    'day' => $booked_date['ar_date']['date'],
                    'time' => setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H'))
                ];

            }
        } else {
            if (isset($arr_2[$booked_date['ar_date']['date']])) {
                $arr_2[$booked_date['ar_date']['date']]['time'] = array_merge($arr_2[$booked_date['ar_date']['date']]['time'], setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), 23));
            } else {
                $arr_2[$booked_date['ar_date']['date']] = [
                    'day' => $booked_date['ar_date']['date'],
                    'time' => setTimePeriod(DateTime::createFromFormat('H:i:s', $booked_date['ar_date']['time'])->format('H'), 23)
                ];
            }
            if (isset($arr_2[$booked_date['dep_date']['date']])) {
                $arr_2[$booked_date['dep_date']['date']]['time'] = array_merge($arr_2[$booked_date['dep_date']['date']]['time'], setTimePeriod(8, DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H')));
            } else {
                $arr_2[$booked_date['dep_date']['date']] = [
                    'day' => $booked_date['dep_date']['date'],
                    'time' => setTimePeriod(8, DateTime::createFromFormat('H:i:s', $booked_date['dep_date']['time'])->format('H'))
                ];
            }
        }
        if (isset($booked_date['bet_date']) && !empty($booked_date['bet_date'])) {
            foreach ($booked_date['bet_date'] as $b_day) {
                $arr_3[$b_day['date']] = [
                    'date' => $b_day['date'],
                    'time' => setTimePeriod(8, 23),
                ];
            }
        }
    }
    foreach ($arr_1 as $a1_date => $a1_date_val) {
        foreach ($arr_2 as $a2_date => $a2_date_val) {
            if ($a1_date == $a2_date) {
                $arr_4[$a1_date] = [
                    'date' => $a1_date,
                    'time' => array_merge($a2_date_val['time'], $a1_date_val['time'])
                ];

            }
        }
    }
    foreach ($arr_4 as $date4 => $date_time4) {
        foreach ($arr_1 as $date1 => $date_time1) {
            if ($date4 == $date1) {
                unset($arr_1[$date1]);
            }
        }
        foreach ($arr_2 as $date2 => $date_time2) {
            if ($date4 == $date2) {
                unset($arr_2[$date2]);
            }
        }
    }
    return array_merge($arr_1, $arr_2, $arr_3, $arr_4);
}

function createCalendarDates($dates = [], $period = '')
{
    //результирующий массив
    $return_arr = [];
    //массивы для проверки половины дня
    $first_half_day = [
        0 => '8:00:00',
        1 => '9:00:00',
        2 => '10:00:00',
        3 => '11:00:00',
        4 => '12:00:00',
        5 => '13:00:00',
        6 => '14:00:00',
    ];
    $second_half_day = [
        7 => '15:00:00',
        8 => '16:00:00',
        9 => '17:00:00',
        10 => '18:00:00',
        11 => '19:00:00',
        12 => '20:00:00',
        13 => '21:00:00',
        14 => '22:00:00',
        15 => '23:00:00',
    ];
    $end_day = [
        13 => '21:00:00',
        14 => '22:00:00',
        15 => '23:00:00'
    ];
    $full_day = array_merge($first_half_day, $second_half_day);
    $res = createSelectedDateArr($dates);
    $now = new DateTime();
    foreach ($res as $date => $date_time) {
        if (count($date_time['time']) >= 14) {
            $return_arr[$date] = [
                'date' => $date,
                'status' => 'disabled',
                'class' => 'flatpickr-disabled',
            ];
        } else {
            if (count(array_intersect($first_half_day, $date_time['time'])) >= 1 && count($date_time['time']) <= 7) {
                $return_arr[$date] = [
                    'date' => $date,
                    'status' => 'available',
                    'class' => 'second-half-day',
                ];
            } else {
                if (count(array_intersect($second_half_day, $date_time['time'])) >= 1 && count($date_time['time']) <= 8) {
                    $return_arr[$date] = [
                        'date' => $date,
                        'status' => 'available',
                        'class' => 'first-half-day',
                    ];
                } else {
                    if (count(array_intersect($first_half_day, $date_time['time'])) >= 6) {
                        $return_arr[$date] = [
                            'date' => $date,
                            'status' => 'available',
                            'class' => 'second-half-day',
                        ];
                    } else {
                        if (count(array_intersect($second_half_day, $date_time['time'])) >= 6) {
                            $return_arr[$date] = [
                                'date' => $date,
                                'status' => 'available',
                                'class' => 'first-half-day',
                            ];
                        }

                    }
                }
            }
        }
        if ($period != '') {
            if ($period == 'day') {
                $ar_diff = array_diff($full_day, $date_time['time']);
                if (count(array_intersect($end_day, $ar_diff)) == 3 && count($ar_diff) <= 3) {
                    $return_arr[$date] = [
                        'date' => $date,
                        'status' => 'disabled',
                        'class' => 'flatpickr-disabled',
                    ];
                }
            } elseif ($period == 'couple') {
                $ar_date = explode('.', $date);
                $day = $ar_date[0];
                $next_date = (intval($day) + 1) . '.' . $ar_date[1] . '.' . $ar_date[2];
                $prev_date = (intval($day) - 1) . '.' . $ar_date[1] . '.' . $ar_date[2];

                if (isset($res[$next_date])) {
                    if (count($res[$next_date]['time']) < 15) {
                        if (!in_array('23:00:00', $date_time['time'])) {
                            if (!in_array('8:00:00', $res[$next_date]['time'])) {
                                if (isset($res[$prev_date])) {
                                    if (count($res[$prev_date]['time']) < 15) {
                                        if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                                            if (!in_array('8:00:00', $date_time['time'])) {
                                                $return_arr[$date] = [
                                                    'date' => $date,
                                                    'status' => 'available',
                                                    'class' => 'first-half-day',
                                                ];
                                            }
                                        } else {
                                            if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                                $return_arr[$date] = [
                                                    'date' => $date,
                                                    'status' => 'disabled',
                                                    'class' => 'flatpickr-disabled',
                                                ];
                                            }
                                        }
                                    }
                                } else {
                                    if (DateTime::createFromFormat('d.m.Y', $prev_date)->format('d.m.Y') != $now->format('d.m.Y')) {
                                        if (!in_array('8:00:00', $date_time['time'])) {
                                            if (count($date_time['time']) > 2) {
                                                $return_arr[$date] = [
                                                    'date' => $date,
                                                    'status' => 'available',
                                                    'class' => 'first-half-day',
                                                ];
                                            } else {
                                                $return_arr[$date] = [
                                                    'date' => $date,
                                                    'status' => 'disabled',
                                                    'class' => 'flatpickr-disabled',
                                                ];
                                            }
                                        } else {
                                            $return_arr[$date] = [
                                                'date' => $date,
                                                'status' => 'disabled',
                                                'class' => 'flatpickr-disabled',
                                            ];
                                        }
                                    } else {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                }
                            } else {
                                $return_arr[$date] = [
                                    'date' => $date,
                                    'status' => 'disabled',
                                    'class' => 'flatpickr-disabled',
                                ];
                            }
                        } else {
                            if (isset($res[$prev_date])) {
                                if (count($res[$prev_date]['time']) < 15) {
                                    if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                                        if (!in_array('8:00:00', $date_time['time'])) {
                                            $return_arr[$date] = [
                                                'date' => $date,
                                                'status' => 'available',
                                                'class' => 'first-half-day',
                                            ];
                                        }
                                    }
                                } else {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'disabled',
                                        'class' => 'flatpickr-disabled',
                                    ];
                                }
                            } else {
                                if (DateTime::createFromFormat('d.m.Y', $prev_date)->format('d.m.Y') != $now->format('d.m.Y')) {
                                    if (!in_array('8:00:00', $date_time['time'])) {
                                        if (count($date_time['time']) > 2) {
                                            $return_arr[$date] = [
                                                'date' => $date,
                                                'status' => 'available',
                                                'class' => 'first-half-day',
                                            ];
                                        } else {
                                            $return_arr[$date] = [
                                                'date' => $date,
                                                'status' => 'disabled',
                                                'class' => 'flatpickr-disabled',
                                            ];
                                        }
                                    } else {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                } else {
                                    $return_arr[$date] = [
                                        'date' => $date,
                                        'status' => 'disabled',
                                        'class' => 'flatpickr-disabled',
                                    ];
                                }
                            }
                        }
                    } else {
                        if (isset($res[$prev_date])) {
                            if (count($res[$prev_date]['time']) < 15) {
                                if (!in_array('23:00:00', $res[$prev_date]['time'])) {
                                    if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                } else {
                                    if (!array_intersect(array_diff($date_time['time'], $full_day), array_diff($res[$next_date]['time'], $full_day))) {
                                        $return_arr[$date] = [
                                            'date' => $date,
                                            'status' => 'disabled',
                                            'class' => 'flatpickr-disabled',
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $now_in_array = false;
    foreach ($return_arr as $date => $date_params) {
        if (DateTime::createFromFormat('d.m.Y', $date)->format('d.m.Y') == $now->format('d.m.Y')) {
            $now_in_array = true;
        }
    }
    if (!$now_in_array) {
        if ($now->format('H') >= 13) {
            $return_arr[$now->format('d.m.Y')] = [
                'date' => $now->format('d.m.Y'),
                'status' => 'available',
                'class' => 'second-half-day',
            ];
        }
    }
    return $return_arr;
}

function setTimePeriod($start, $end)
{
    $arr = [];
    for ($i = intval($start); $i <= intval($end); $i++) {
        $arr[] = $i . ':00' . ':00';
    }
    return $arr;
}

function setTime($time_string, $action, $value)
{
    $user_time = DateTime::createFromFormat('H:i:s', $time_string);
    switch ($action) {
        case 'plus':
            $user_time = $user_time->modify("+" . $value . " hours");
            break;
        case 'minus':
            $user_time = $user_time->modify("-" . $value . " hours");
            break;
    }
    return $user_time->format("H:i:s");
}

/*
 * ar_date = arrival date = дата заезда
 * bet_date = between date = промежуточная дата
 * dep_date = department date = дата выезда
 */
function _get_dates($start, $end, $format = 'd.m.Y H:i:s')
{
    $days = [];
    $datetimeFrom = DateTime::createFromFormat($format, $start);
    $datetimeTo = DateTime::createFromFormat($format, $end);
    if ($datetimeFrom->format('H') > $datetimeTo->format('H')) {
        $max = intval($datetimeFrom->diff($datetimeTo)->format('%a')) + 1;
    } else {
        $max = intval($datetimeFrom->diff($datetimeTo)->format('%a'));
    }
    if ($max == 0) {
        $days[] = [
            'ar_date' => [
                'date' => $datetimeFrom->format('d.m.Y'),
                'time' => $datetimeFrom->format('H:i:s'),
            ],
            'dep_date' => [
                'date' => $datetimeTo->format('d.m.Y'),
                'time' => $datetimeTo->format('H:i:s'),
            ],
        ];
    } else {
        for ($i = 0; $i <= $max; $i++) {
            if ($i == 0) {
                $days[0]['ar_date'] = [
                    'date' => $datetimeFrom->format('d.m.Y'),
                    'time' => $datetimeFrom->format('H:i:s'),
                ];
            } else if ($i == $max) {
                $days[0]['dep_date'] = [
                    'date' => $datetimeTo->format('d.m.Y'),
                    'time' => $datetimeTo->format('H:i:s'),
                ];
            } else {
                $date = DateTimeImmutable::createFromFormat($format, $start);
                $new_date = new DateTime($date->format($format));
                $modif_str = '+' . $i . ' days';
                $new_date->modify($modif_str);
                $days[0]['bet_date'][] = [
                    'date' => $new_date->format('d.m.Y'),
                    'time' => $new_date->format('H:i:s'),
                ];
            }
        }
    }
    return $days;
}

function getMapPointIcon($icon_src, $element_id)
{
    Loader::includeModule("iblock");

    if (isset($icon_src) && !empty($icon_src) && $icon_src != '') {
        return $icon_src;
    } else {
        $res = CIBlockElement::GetByID($element_id);
        if ($ar_res = $res->GetNext()) {
            $section_id = $ar_res['IBLOCK_SECTION_ID'];
        }
        $arFilter = array('IBLOCK_ID' => IB_OBJECT, 'ID' => $section_id);
        $db_list = CIBlockSection::GetList(array("timestamp_x" => "DESC"), $arFilter, false, array("UF_CB_ACTIVE_SVG_ICON"));
        while ($ar_result = $db_list->GetNext()) {
            $section_icon_src = CFile::GetPath($ar_result["UF_CB_ACTIVE_SVG_ICON"]);
        }
        if (isset($section_icon_src) && $section_icon_src != '') {
            return $section_icon_src;
        } else {
            return '/local/templates/.default/assets/img/default_map_icon.svg';
        }
    }
}

function createMapPoint($arPoints)
{

    $features = [];

    foreach ($arPoints as $id => $point) {
        if (!empty($point)) {
            $features[] = ["type" => "Feature",
                "id" => strval(trim($id)),
                "geometry" => [
                    "type" => "Point",
                    "coordinates" => [$point['coordinates'][0], $point['coordinates'][1]]
                ],
                "properties" => [
                    "hintContent" => $point['hintContent']
                ],
                "options" => [
                    "iconLayout" => "default#image",
                    "iconImageHref" => $point['iconImageHref'],
                    "iconImageSize" => [48, 48],
                    "iconImageOffset" => [-24, -24],
                    "iconContentOffset" => [15, 15],
                ]
            ];
        }
    }

    return $features;
}

//функция, которая достает тип транспортного средства
function getUserVehicleType($vehicleXmlId)
{
    if ($vehicleXmlId) {
        if (Loader::includeModule("highloadblock")) {
            $hlblock = HL\HighloadBlockTable::getById(HL_VEHICLE_TYPE)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $data = $entity_data_class::getList(array(
                "select" => array("UF_TYPE"),
                "order" => array("ID" => "DESC"),
                "filter" => array("UF_XML_ID" => $vehicleXmlId)
            ));
            $returnType = false;
            while ($arData = $data->Fetch()) {
                $returnType = $arData['UF_TYPE'];
            }
            return $returnType;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getVehicleTypes()
{
    if (Loader::includeModule("highloadblock")) {
        $hlblock = HL\HighloadBlockTable::getById(HL_VEHICLE_TYPE)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array()
        ));
        $arTypes = [];
        while ($arData = $data->Fetch()) {
            $arTypes[] = $arData;
        }
        if (!empty($arTypes)) {
            return $arTypes;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//функция возвращает id пользователя по id записи пользователя в инфоблоке (IB_USERS)
function getUserIdByRecordId($recordId)
{
    if ($recordId) {
        if (Loader::includeModule("iblock")) {
            $userId = false;
            $db_props = CIBlockElement::GetProperty(IB_USERS, $recordId, array("sort" => "asc"), array("CODE" => "USER_ID"));
            if ($ar_props = $db_props->Fetch()) {
                $userId = $ar_props["VALUE"];
            }
            return $userId;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//функция достает перечень туристических маршрутов
function getRouts()
{
    if (Loader::includeModule("iblock")) {
        $arSelect = ['ID', 'NAME'];
        $arFilter = ['IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y', 'SECTION_ID' => TOURISTS_ROUTS_SECTION_ID];
        $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arRouts[] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
            ];
        }
        if (!empty($arRouts)) {
            return $arRouts;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//функция возвращает данные о транспортном средстве пользователя
function getUserVehicle($vehicleId)
{
    if ($vehicleId) {
        if (CModule::IncludeModule("iblock")) {
            $arUserVehicle = [];
            $arSelect = array("ID", "PROPERTY_VEHICLE_TYPE", "PROPERTY_MODEL");
            $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ACTIVE" => "Y", 'ID' => $vehicleId);
            $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arUserVehicle = [
                    'ID' => $arFields['ID'],
                    'VEHICLE_TYPE' => $arFields['PROPERTY_VEHICLE_TYPE_VALUE'],
                    'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                ];
            }
            if (!empty($arUserVehicle)) {
                return $arUserVehicle;
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

//функция, которая достает данные пользователя
function getUserData($userId = false)
{
    if (CModule::IncludeModule("iblock")) {
        global $USER;
        if (!$userId) {
            $userId = $USER->GetID();
        }
        $rsUser = CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();
        $arGroups = $USER->GetUserGroupArray();
        $isAdmin = false;
        $isOperator = false;
        $isReserv = false;
        $user_objects = false;
        $user_locations = false;
        foreach ($arGroups as $group_id) {
            if ($group_id == SITE_ADMIN_GROUP_ID) {
                $isAdmin = true;
            }
            if ($group_id == SITE_OPERATOR_GROUP_ID) {
                $isOperator = true;
            }
            if ($group_id == SITE_RESERV_GROUP_ID) {
                $isReserv = true;
            }
        }
        if ($isAdmin || $isOperator || $isReserv) {
            foreach ($arUser['UF_USER_LOCATIONS'] as $location_id) {
                $arLocation = CIBlockSection::GetByID($location_id);
                if ($Location = $arLocation->GetNext()) {
                    $user_locations[] = [
                        "ID" => $location_id,
                        "NAME" => $Location['NAME']
                    ];
                }
            }
            foreach ($arUser['UF_USER_OBJECTS'] as $object_id) {
                $arObject = CIBlockElement::GetByID($object_id);
                if ($Object = $arObject->GetNext()) {
                    $user_objects[] = [
                        "ID" => $object_id,
                        "NAME" => $Object['NAME']
                    ];
                }
            }

            /*TODO:УДАЛИТЬ ЭТОТ КУСОК КОДА ПОСЛЕ РАЗРАБОТКИ*/
            if (Loader::includeModule("iblock")) {
                $arUserRecordData = [];
                $arSelect = array(
                    "ID",
                    'PROPERTY_PREF_CATEGORY',
                    'PROPERTY_LOCATION',
                    'PROPERTY_PREF_DOC_NUMBER',
                    'PROPERTY_PREF_DOC_DATE',
                    'PROPERTY_PREF_DOCS',
                );
                $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arUserRecordData = [
                        'ID' => $arFields["ID"],
                        'PREF_CATEGORY' => $arFields['PROPERTY_PREF_CATEGORY_VALUE'],
                        'LOCATION' => $arFields['PROPERTY_LOCATION_VALUE'],
                        'PREF_DOC_NUMBER' => $arFields['PROPERTY_PREF_DOC_NUMBER_VALUE'],
                        'PREF_DOC_DATE' => $arFields['PROPERTY_PREF_DOC_DATE_VALUE'],
                    ];
                    if ($arFields['PROPERTY_PREF_DOCS_VALUE']) {
                        foreach ($arFields['PROPERTY_PREF_DOCS_VALUE'] as $fileId) {
                            /*$file = CFile::GetFileArray($fileId);
                            $arUserRecordData['PREF_DOCS'][] = [
                                'CONTENT_TYPE' => $file['CONTENT_TYPE'],
                                'SRC' => $file['SRC'],
                            ];*/
                            $arUserRecordData['PREF_DOCS'][] = CFile::GetFileArray($fileId);
                        }
                    }
                }
            }
            /*УДОЛИ!*/

        } else {
            if (Loader::includeModule("iblock")) {
                $arUserRecordData = [];
                $arSelect = array(
                    "ID",
                    'PROPERTY_PREF_CATEGORY',
                    'PROPERTY_LOCATION',
                    'PROPERTY_PREF_DOC_NUMBER',
                    'PROPERTY_PREF_DOC_DATE',
                    'PROPERTY_PREF_DOCS',
                );
                $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
                $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arUserRecordData = [
                        'PREF_CATEGORY' => $arFields['PROPERTY_PREF_CATEGORY_VALUE'],
                        'LOCATION' => $arFields['PROPERTY_LOCATION_VALUE'],
                        'PREF_DOC_NUMBER' => $arFields['PROPERTY_PREF_DOC_NUMBER_VALUE'],
                        'PREF_DOC_DATE' => $arFields['PROPERTY_PREF_DOC_DATE_VALUE'],
                    ];
                    if ($arFields['PROPERTY_PREF_DOCS_VALUE']) {
                        foreach ($arFields['PROPERTY_PREF_DOCS_VALUE'] as $fileId) {
                            $file = CFile::GetFileArray($fileId);
                            $arUserRecordData['PREF_DOCS'][] = [
                                'CONTENT_TYPE' => $file['CONTENT_TYPE'],
                                'SRC' => $file['SRC'],
                            ];
                        }
                    }
                }
            }
        }
        $userData = [
            'ID' => $arUser["ID"],
            "NAME" => $arUser["NAME"],
            "LAST_NAME" => $arUser["LAST_NAME"],
            "SECOND_NAME" => $arUser["SECOND_NAME"],
            "EMAIL" => $arUser["EMAIL"],
            "LOGIN" => $arUser["LOGIN"],
            "PHONE" => $arUser["WORK_PHONE"],
            "GROUPS" => $arGroups,
        ];
        if ($isAdmin) {
            $userData["IS_ADMIN"] = $isAdmin;
        }
        if ($isOperator) {
            $userData["IS_OPERATOR"] = $isOperator;
        }
        if ($isReserv) {
            $userData["IS_RESERV"] = $isReserv;
        }
        if ($user_objects) {
            $userData["USER_OBJECTS"] = $user_objects;
        }
        if ($user_locations) {
            $userData["USER_LOCATIONS"] = $user_locations;
        }
        if (isset($arUserRecordData) && !empty($arUserRecordData)) {
            $userData["USER_RECORD_DATA"] = $arUserRecordData;
        }
        return $userData;
    } else {
        return false;
    }
}

function getTimeDiff($from, $to = false)
{
    $from = new DateTime($from);
    if ($to)
        $to = new DateTime($to);
    else
        $to = new DateTime();
    $interval = $from->diff($to);
    $diff = intval($interval->format('%h'));
    return $diff;
}

function stringToHash($data_string)
{
    return hash('sha256', $data_string);
}

function checkObjectAvailability($object_id, $date_from, $date_to)
{
    if ($object_id) {
        Loader::includeModule("highloadblock");
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_BOOKING)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $now = new DateTime();
        $date_from = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($date_from));
        $date_to = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($date_to));
        $arDisabledates = [];
        $canBook = true;
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array("UF_OBJECT_ID" => $object_id, ">UF_ARRIVAL_DATE" => $now->format('d.m.Y H:i:s'))
        ));
        while ($arData = $data->Fetch()) {
            $entity_arrival_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_ARRIVAL_DATE']);
            $entity_departure_date = DateTime::createFromFormat('d.m.Y H:i:s', $arData['UF_DEPARTURE_DATE']);
            $arDisabledates[$arData['ID']] = [
                'form' => $entity_arrival_date,
                'to' => $entity_departure_date
            ];
        }
        foreach ($arDisabledates as $id => $range) {
            if ($date_from <= $range['to'] && $range['form'] <= $date_to) {
                $canBook = false;
            }
        }
        return $canBook;
    } else {
        return false;
    }
}

function getOrderDate($order_id)
{
    $arSelect = array(
        "ID",
        "IBLOCK_ID",
        "NAME",
        "PROPERTY_*",
    );
    $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $order_id);
    $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arProps = $ob->GetProperties();
    }
    //подгоняем свойства заказа под нужную структуру данных
    foreach ($arProps as $prop) {
        if ($prop['ID'] == 21 || $prop['ID'] == 11 || $prop['ID'] == 12) {
            $order_props['PROPERTY_VALUES'][$prop['ID']]['VALUE'] = $prop['VALUE'];
        } else {
            $order_props['PROPERTY_VALUES'][$prop['ID']] = $prop['VALUE'];
        }
    }
    $order_props['ID'] = $order_id;
    $order_props['IBLOCK_ID'] = IB_BOOKING_LIST;
    if ($order_props) {
        return $order_props;
    } else {
        return false;
    }
}

function addDataToStatTable($arFields)
{

    //\Bitrix\Main\Diag\Debug::dumpToFile($arFields, 'addDataToStatTable', 'functions_log.txt');

    if (isset($arFields) && !empty($arFields)) {

        if ($arFields['IBLOCK_ID'] === IB_BOOKING_LIST) {

            //достаем id локации по id объекта
            Loader::includeModule("iblock");

            $DATE_INSERT = DateTime::createFromFormat('d.m.Y H:i:s', \Bitrix\Main\Type\DateTime::createFromUserTime($arFields["DATE_ACTIVE_FROM"]))->format('d.m.Y');

            if (isset($arFields["PROPERTY_VALUES"][21]["VALUE"]) && !empty($arFields["PROPERTY_VALUES"][21]["VALUE"])) {
                $OBJECT_LOCATION_ID = getObjectSections($arFields["PROPERTY_VALUES"][21]["VALUE"])[2];
                /* $arSelect = array("ID", "NAME", "PROPERTY_LOCATION");
                 $arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => (int)$arFields["PROPERTY_VALUES"][21]["VALUE"]);
                 $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                 while ($ob = $res->GetNextElement()) {
                     $_arFields = $ob->GetFields();
                     $OBJECT_LOCATION_ID = $_arFields["PROPERTY_LOCATION_VALUE"];
                 }*/
            }

            //рассчитываем кол-во разрешений
            if (isset($arFields["PROPERTY_VALUES"][15]) && !empty($arFields["PROPERTY_VALUES"][15])) {
                //разрешение получено
                if ($arFields["PROPERTY_VALUES"][15] == 1) {
                    $PERMIT_COUNT = 0;
                    $BENEFIT_PERMIT_COUNT = 0;
                } else {
                    //кол-во людей
                    if (isset($arFields["PROPERTY_VALUES"][16]) && !empty($arFields["PROPERTY_VALUES"][16])) {
                        //кол-во льготников
                        if (isset($arFields["PROPERTY_VALUES"][17]) && !empty($arFields["PROPERTY_VALUES"][17])) {
                            if ($arFields["PROPERTY_VALUES"][17] > 0) {
                                $BENEFIT_PERMIT_COUNT = (int)$arFields["PROPERTY_VALUES"][17];
                                $PERMIT_COUNT = (int)$arFields["PROPERTY_VALUES"][16] - (int)$arFields["PROPERTY_VALUES"][17];
                            } else {
                                $BENEFIT_PERMIT_COUNT = 0;
                            }
                        } else {
                            $PERMIT_COUNT = (int)$arFields["PROPERTY_VALUES"][16];
                            $BENEFIT_PERMIT_COUNT = 0;
                        }
                    } else {
                        $PERMIT_COUNT = 0;
                        $BENEFIT_PERMIT_COUNT = 0;
                    }
                }
            }

            //достаем оператора и выявляем способ бронирования
            if (isset($arFields["PROPERTY_VALUES"][22]) && !empty($arFields["PROPERTY_VALUES"][22])) {
                if ($arFields["PROPERTY_VALUES"][22] == 'Онлайн') {
                    $BOOKING_TYPE = 'Онлайн';
                    $OPERATOR = 'С сайта';
                } else {
                    $BOOKING_TYPE = 'Оператор';
                    $OPERATOR = $arFields["PROPERTY_VALUES"][22];
                }
            }

            //вычисляем длительность пребывания на объекте
            if (isset($arFields["PROPERTY_VALUES"][11]['VALUE']) && isset($arFields["PROPERTY_VALUES"][12]['VALUE'])) {
                $datetimeFrom = DateTime::createFromFormat('d.m.Y', $arFields["PROPERTY_VALUES"][11]['VALUE']);
                $datetimeTo = DateTime::createFromFormat('d.m.Y', $arFields["PROPERTY_VALUES"][12]['VALUE']);
                $dayDif = intval($datetimeFrom->diff($datetimeTo)->format('%a'));
                if ($dayDif > 0) {
                    $STAY_DURATION = 'Сутки';
                } elseif ($dayDif == 0) {
                    $STAY_DURATION = 'День';
                }
            }

            Loader::includeModule("highloadblock");

            $hlblock = HL\HighloadBlockTable::getById(HL_STATS)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $data = array(
                "UF_ARRIVAL_DATE" => $arFields["PROPERTY_VALUES"][11]["VALUE"],
                "UF_DEPARTURE_DATE" => $arFields["PROPERTY_VALUES"][12]["VALUE"],
                "UF_OBJECT_ID" => $arFields["PROPERTY_VALUES"][21]["VALUE"],
                "UF_ORDER_SUM" => $arFields["PROPERTY_VALUES"][32],
            );
            if (isset($DATE_INSERT)) {
                $data["UF_DATE_INSERT"] = $DATE_INSERT;
            }
            if (isset($OBJECT_LOCATION_ID)) {
                $data["UF_LOCATION"] = $OBJECT_LOCATION_ID;
            }
            if (isset($PERMIT_COUNT)) {
                $data["UF_PERMIT_COUNT"] = $PERMIT_COUNT;
            }
            if (isset($BENEFIT_PERMIT_COUNT)) {
                $data["UF_BENEFIT_PERMIT_COUNT"] = $BENEFIT_PERMIT_COUNT;
            }
            if (isset($BOOKING_TYPE)) {
                $data["UF_BOOKING_TYPE"] = $BOOKING_TYPE;
            }
            if (isset($OPERATOR)) {
                $data["UF_OPERATOR"] = $OPERATOR;
            }
            if (isset($STAY_DURATION)) {
                $data["UF_STAY_DURATION"] = $STAY_DURATION;
            }

            //\Bitrix\Main\Diag\Debug::dumpToFile($data, 'addDataToStatTable', 'functions_log.txt');

            $result = $entity_data_class::add($data);
            if (!$result->isSuccess()) {
                \Bitrix\Main\Diag\Debug::dumpToFile(implode(', ', $result->getErrors()), 'addDataToStatTable', 'functions_error_log.txt');
            }
        }
    } else {
        $now = new DateTime();
        \Bitrix\Main\Diag\Debug::dumpToFile('empty $arFields ' . $now->format('d.m.Y H:i:s'), 'addDataToStatTable', 'functions_error_log.txt');
    }
}

function partnerNotification($arFields)
{
    if (isset($arFields['PROPERTY_VALUES'][21]["VALUE"]) && !empty($arFields['PROPERTY_VALUES'][21]["VALUE"])) {
        $objectId = $arFields['PROPERTY_VALUES'][21]["VALUE"];
        $arPartnersList = getObjectPartnersList($objectId);
        sendDataToPartner($arPartnersList, $arFields);
    }
}

function getObjectPartnersList($objectId)
{
    //TODO:ПОФИКСИТЬ ВЫБОРКУ ПАРТНЕРОВ ОБЪЕКТА
    if ($objectId) {
        Loader::includeModule("iblock");
        Loader::includeModule("highloadblock");
        $arObjectPartners = [];
        $res = CIBlockElement::GetList(array(), ["IBLOCK_ID" => IB_LOCATIONS, 'ID' => $objectId], false, [], ["PROPERTY_PARTNERS"]);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            if (isset($arFields['PROPERTY_PARTNERS_VALUE']) && !empty($arFields['PROPERTY_PARTNERS_VALUE'])) {
                $arObjectPartners[] = $arFields['PROPERTY_PARTNERS_VALUE'];
            }
        }
        if (!empty($arObjectPartners)) {
            $arPartnerData = [];
            $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            foreach ($arObjectPartners as $partnerId) {
                $data = $entity_data_class::getList(array(
                    "select" => array("*"),
                    "order" => array("ID" => "ASC"),
                    "filter" => array("ID" => $partnerId)
                ));
                while ($arData = $data->Fetch()) {
                    if (!empty($arData)) {
                        $arPartnerData[$arData['ID']] = [
                            'ID' => $arData['ID'],
                            'EMAIL' => $arData['UF_PARTNER_EMAIL'],
                            'TELEGRAM' => $arData['UF_TELEGRAM_API'],
                            'CHAT' => $arData['UF_CHAT_ID'],
                        ];
                    }

                }
            }
            if (!empty($arPartnerData)) {
                return $arPartnerData;
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

function sendDataToPartner($arPartners, $orderData)
{
    if (!empty($arPartners) && !empty($orderData)) {
        foreach ($arPartners as $partner) {
            if (isset($partner['TELEGRAM']) && !empty($partner['TELEGRAM'])) {
                sendDataToTelegram($partner['TELEGRAM'], $partner['CHAT'], $orderData);
            }
            if (isset($partner['EMAIL']) && !empty($partner['EMAIL'])) {
                sendDataToEmail($partner['EMAIL'], $orderData);
            }
        }
    }
}

function sendDataToTelegram($apiString, $chatId, $data)
{
    if (isset($apiString) && !empty($data)) {
        Loader::includeModule("iblock");
        $object = [];
        $arSelect = array(
            "NAME",
            "PROPERTY_LOCATION",
        );
        //TODO:ПОФИКСИТЬ ВЫБОРКУ ДАННЫХ ОБЪЕКТА
        $arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => intval($data['PROPERTY_VALUES'][21]['VALUE']));
        $object_data = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($object_values = $object_data->GetNextElement()) {
            $arObject = $object_values->GetFields();
            $object['name'] = $arObject['NAME'];
            $object['location'] = $arObject['PROPERTY_LOCATION_VALUE'];
        }
        if ($object['location']) {
            $res = CIBlockElement::GetByID($object['location']);
            if ($ar_res = $res->GetNext()) {
                $object['location'] = $ar_res['NAME'];
            }
        }
        $ORDER_ID = $data['ID'];
        $arSelect = array(
            "ID",
            "PROPERTY_QR_CODE",
            "PROPERTY_UNIQUE_ORDER_CODE"
        );
        $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $ORDER_ID);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            if (isset($arFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"])) {
                $link = 'https:///receipt/?order=' . $arFields["ID"] . '&code=' . $arFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"];
            }
        }
        $tlgrm = (object)[
            'object_name' => $object['name'],
            'object_location' => htmlspecialchars_decode($object['location']),
            'phone' => $data['PROPERTY_VALUES'][20],
            'fio' => $data['PROPERTY_VALUES'][9] . ' ' . $data['PROPERTY_VALUES'][10],
            'booking_period' => 'с ' . $data['PROPERTY_VALUES'][11]['VALUE'] . ' ' . $data['PROPERTY_VALUES'][13] . ' по ' . $data['PROPERTY_VALUES'][12]['VALUE'] . ' ' . $data['PROPERTY_VALUES'][14],
        ];
        if (isset($chatId)) {
            $telegramQuery = [
                'chat_id' => $chatId,
                'text' => "Оплачена услуга - локация \"$tlgrm->object_location\", объект \"$tlgrm->object_name\". \nФИО: $tlgrm->fio \nТел: $tlgrm->phone \nПериод бронирования: $tlgrm->booking_period \n$link"
            ];
            $resp = file_get_contents("https://api.telegram.org/bot" . $apiString . "/sendMessage?" . http_build_query($telegramQuery));
            if (!$resp) {
                $error = error_get_last();
                $now = new DateTime();
                \Bitrix\Main\Diag\Debug::dumpToFile($error['message'] . $now->format('d.m.Y H:i:s'), 'sendDataToTelegram', 'functions_error_log.txt');
            }
        }
    }
}

function sendDataToEmail($email, $data)
{
    if (isset($email) && !empty($data)) {
        Loader::includeModule("iblock");
        $object = CIBlockElement::GetByID(intval($data['PROPERTY_VALUES'][21]['VALUE']));
        if ($ar_object = $object->GetNext()) {
            $OBJECT_NAME = $ar_object['NAME'];
        }
        $arSelect = array(
            "NAME",
            "PROPERTY_LOCATION",
        );
        //TODO:ПОФИКСИТЬ ВЫБОРКУ ДАННЫХ ОБЪЕКТА
        $arFilter = array("IBLOCK_ID" => IB_OBJECT, "ID" => intval($data['PROPERTY_VALUES'][21]['VALUE']));
        $object_data = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
        while ($object_values = $object_data->GetNextElement()) {
            $arObject = $object_values->GetFields();
            $OBJECT_LOCATION = $arObject['PROPERTY_LOCATION_VALUE'];
        }
        if ($OBJECT_LOCATION) {
            $res = CIBlockElement::GetByID($OBJECT_LOCATION);
            if ($ar_res = $res->GetNext()) {
                $OBJECT_LOCATION = $ar_res['NAME'];
            }
        }
        $ARRIVAL_DATE = $data['PROPERTY_VALUES'][11]['VALUE'];
        $CHECK_IN_TIME = $data['PROPERTY_VALUES'][13];
        $DEPARTURE_DATE = $data['PROPERTY_VALUES'][12]['VALUE'];
        $DEPARTURE_TIME = $data['PROPERTY_VALUES'][14];
        $USER_PHONE = $data['PROPERTY_VALUES'][20];
        $USER_FIO = $data['PROPERTY_VALUES'][9] . ' ' . $data['PROPERTY_VALUES'][10];
        $ORDER_ID = $data['ID'];
        $arSelect = array(
            "ID",
            "PROPERTY_QR_CODE",
            "PROPERTY_UNIQUE_ORDER_CODE"
        );
        if ($ORDER_ID) {
            $arFilter = array("IBLOCK_ID" => IB_BOOKING_LIST, "ID" => $ORDER_ID);
            $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if (isset($arFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"])) {
                    $RECEIPT_LINK = 'https:///receipt/?order=' . $arFields["ID"] . '&code=' . $arFields["PROPERTY_UNIQUE_ORDER_CODE_VALUE"];
                }
            }
            \Bitrix\Main\Mail\Event::send(array(
                "EVENT_NAME" => "NEW_ORDER_NOTIFICATION",
                "LID" => "s1",
                "C_FIELDS" => array(
                    'EMAIL' => $email,
                    'LOCATION' => $OBJECT_LOCATION,
                    'OBJECT' => $OBJECT_NAME,
                    'USER_FIO' => $USER_FIO,
                    'USER_PHONE' => $USER_PHONE,
                    'BOOKING_FROM' => $ARRIVAL_DATE . ' ' . $CHECK_IN_TIME,
                    'BOOKING_TO' => $DEPARTURE_DATE . ' ' . $DEPARTURE_TIME,
                    'RECEIPT_LINK' => $RECEIPT_LINK,
                ),
            ));
        } else {
            $errMsg = 'Отсутствует значение $ORDER_ID';
            \Bitrix\Main\Diag\Debug::dumpToFile($errMsg, $varName = 'sendDataToEmail', $fileName = 'functions_error_log.txt');
        }
    }
}

function selectFromArr($resultArr, $arSelectFrom)
{
    $result = array_intersect($resultArr, $arSelectFrom);
    return $result;
}

//взял функцию из интернета - возвращает корневой раздел элемента
function getParent($id)
{
    $tt = CIBlockSection::GetList(array(), array('ID' => $id));
    $as = $tt->GetNext();
    static $a;
    if ($as['DEPTH_LEVEL'] == 1) {
        $a = $as['ID'];
    } else {
        getParent($as['IBLOCK_SECTION_ID']);
    }
    return $a;
}

//функция, которая добавляет пользователя в перечень посетителей
function addUserRecord($userId, $propertyUserGroup, $propertyUserVehicle, $arProps = false)
{
    if ($userId) {
        if (Loader::includeModule("iblock")) {
            global $USER;
            $el = new CIBlockElement;
            $PROP = array();
            $PROP['USER_ID'] = $userId;
            if ($propertyUserGroup) {
                $PROP['USER_GROUP'] = $propertyUserGroup;
            }
            if ($propertyUserVehicle) {
                $PROP['USER_TRANSPORT'] = $propertyUserVehicle;
            }
            if ($arProps) {
                $PROP = array_merge($PROP, $arProps);
            }
            $user = getUserData($userId);
            $userFio = $user['LAST_NAME'] . ' ' . $user['NAME'] . ' ' . $user['SECOND_NAME'];
            $arLoadProductArray = array(
                "MODIFIED_BY" => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => IB_USERS,
                "PROPERTY_VALUES" => $PROP,
                "NAME" => $userFio,
                "ACTIVE" => "Y",
            );
            if ($element_id = $el->Add($arLoadProductArray)) {
                return $element_id;
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

//функция, которая проверяет наличие записи пользователя в перечне посетителей
function checkUserRecord($userId)
{
    if ($userId) {
        if (Loader::includeModule("iblock")) {
            $record = [];
            $arSelect = array("ID");
            $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields) {
                    $record = $arFields;
                }
            }
            if (!empty($record)) {
                return $record['ID'];
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

//функция, которая формирует qr код
function getQrCode($uniqueCode, $folder)
{
    if ($uniqueCode && $folder) {
        $permission_link = 'https://' . $_SERVER['HTTP_HOST'] . '/receipt/' . $folder . '/' . $uniqueCode . '/';
        $qrcode = new QRCode();
        $qrcode->render($permission_link, $_SERVER["DOCUMENT_ROOT"] . "/qrCodes/qr" . $uniqueCode . ".png");
        return CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"] . "/qrCodes/qr" . $uniqueCode . ".png");
    } else {
        return false;
    }
}

//функция возвращает льготную категорию по id
function getPrefCategoryById($categoryId)
{
    if ($categoryId) {
        if (Loader::includeModule("iblock")) {
            $arPrefCategories = [];
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_PREFERENTIAL_CATEGORY"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arPrefCategories[$enum_fields['ID']] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                    ];
                }
            }
            if (!empty($arPrefCategories)) {
                return $arPrefCategories[$categoryId];
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//функция возвращает локацию для льготной категории по id
function getPrefUserLocationById($locationId)
{
    if ($locationId) {
        if (Loader::includeModule("iblock")) {
            $arUserLocations = [];
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), array("IBLOCK_ID" => IB_VISITORS, "CODE" => "U_LOCATION"));
            while ($enum_fields = $property_enums->GetNext()) {
                if ($enum_fields) {
                    $arUserLocations[$enum_fields['ID']] = [
                        'ID' => $enum_fields['ID'],
                        'VALUE' => $enum_fields['VALUE'],
                    ];
                }
            }
            if (!empty($arUserLocations)) {
                return $arUserLocations[$locationId];
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//функция возвращает рандомную строку заданной длины (взял в интернете)
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

//функция возвращает данные об объекте
function getObjectById($objectId)
{
    if ($objectId) {
        if (Loader::includeModule("iblock")) {
            $arObject = [];
            $arSelect = array(
                "ID",
                'NAME',
                'DETAIL_TEXT',
                'PROPERTY_TIME_UNLIMIT_OBJECT',
                'PROPERTY_CAPACITY_MAXIMUM',
                'PROPERTY_PRICE',
                'PROPERTY_CAR_POSSIBILITY',
                'PROPERTY_CAR_CAPACITY',
                'PROPERTY_PARTNERS',
                'PROPERTY_LOCATION_FEATURES',
                'PROPERTY_PRICE_TYPE',
                'PROPERTY_OBJECT_TYPE',
                'PROPERTY_NORTHERN_LATITUDE',
                'PROPERTY_EASTERN_LONGITUDE',
                'PROPERTY_DETAIL_GALLERY',
                'PROPERTY_CAPACITY_ESTIMATED',
                'PROPERTY_TIME_INTERVAL',
                'PROPERTY_COST_PER_PERSON',
                'PROPERTY_OBJECT_COST',
                'PROPERTY_OBJECT_DAILY_COST',
                'PROPERTY_COST_PER_PERSON_ONE_DAY',
                'PROPERTY_FIXED_COST',
                'PROPERTY_START_TIME',
                'PROPERTY_END_TIME',
                'SORT',
                'PROPERTY_GALLERY_VIDEO_PREVIEW',
                'PROPERTY_GALLERY_VIDEO_FILES',
                'PROPERTY_CAN_BOOK',
                'PROPERTY_SERVICE_COST',//TODO:проверить актуальность параметра
                'PROPERTY_DAILY_TRAFFIC',
                'PROPERTY_ROUTE_COORDS',
            );
            $arFilter = array("IBLOCK_ID" => IB_LOCATIONS, 'ID' => $objectId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arObject = [
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'TIME_UNLIMIT_OBJECT' => $arFields['PROPERTY_TIME_UNLIMIT_OBJECT_VALUE'],
                    'CAPACITY_MAXIMUM' => $arFields['PROPERTY_CAPACITY_MAXIMUM_VALUE'],
                    'PRICE' => $arFields['PROPERTY_PRICE_VALUE'],
                    'CAR_POSSIBILITY' => $arFields['PROPERTY_CAR_POSSIBILITY_VALUE'],
                    'PARTNERS' => $arFields['PROPERTY_PARTNERS_VALUE'],
                    'LOCATION_FEATURES' => $arFields['PROPERTY_LOCATION_FEATURES_VALUE'],
                    'PRICE_TYPE' => $arFields['PROPERTY_PRICE_TYPE_VALUE'],
                    'OBJECT_TYPE' => $arFields['PROPERTY_OBJECT_TYPE_VALUE'],
                    'NORTHERN_LATITUDE' => $arFields['PROPERTY_NORTHERN_LATITUDE_VALUE'],
                    'EASTERN_LONGITUDE' => $arFields['PROPERTY_EASTERN_LONGITUDE_VALUE'],
                    'DETAIL_GALLERY' => $arFields['PROPERTY_DETAIL_GALLERY_VALUE'],
                    'CAPACITY_ESTIMATED' => $arFields['PROPERTY_CAPACITY_ESTIMATED_VALUE'],
                    'TIME_INTERVAL' => $arFields['PROPERTY_TIME_INTERVAL_VALUE'],
                    'COST_PER_PERSON' => $arFields['PROPERTY_COST_PER_PERSON_VALUE'],
                    'OBJECT_COST' => $arFields['PROPERTY_OBJECT_COST_VALUE'],
                    'OBJECT_DAILY_COST' => $arFields['PROPERTY_OBJECT_DAILY_COST_VALUE'],
                    'COST_PER_PERSON_ONE_DAY' => $arFields['PROPERTY_COST_PER_PERSON_ONE_DAY_VALUE'],
                    'FIXED_COST' => $arFields['PROPERTY_FIXED_COST_VALUE'],
                    'START_TIME' => $arFields['PROPERTY_START_TIME_VALUE'],
                    'END_TIME' => $arFields['PROPERTY_END_TIME_VALUE'],
                    'SORT' => $arFields['SORT'],
                    'GALLERY_VIDEO_PREVIEW' => $arFields['PROPERTY_GALLERY_VIDEO_PREVIEW_VALUE'],
                    'GALLERY_VIDEO' => $arFields['PROPERTY_GALLERY_VIDEO_FILES_VALUE'],
                    'DETAIL_TEXT' => $arFields['DETAIL_TEXT'],
                    'CAR_CAPACITY' => $arFields['PROPERTY_CAR_CAPACITY_VALUE'],
                    'CAN_BOOK' => $arFields['PROPERTY_CAN_BOOK_VALUE'],
                    'SERVICE_COST' => $arFields['PROPERTY_SERVICE_COST_VALUE'],
                    'DAILY_TRAFFIC' => $arFields['PROPERTY_DAILY_TRAFFIC_VALUE'],
                    'ROUTE_COORDS' => $arFields['PROPERTY_ROUTE_COORDS_VALUE'],
                ];
            }
            if (!empty($arObject)) {
                $objectGroups = CIBlockElement::GetElementGroups($arObject['ID'], true);
                while ($arGroup = $objectGroups->Fetch()) {
                    $arObject['SECTIONS'][] = [
                        'ID' => $arGroup['ID'],
                        'NAME' => $arGroup['NAME'],
                        'DEPTH_LEVEL' => $arGroup['DEPTH_LEVEL'],
                    ];
                }
                return $arObject;
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

//функция возвращает данные о транспортном средстве
function getVehicleById($vehicleId)
{
    if ($vehicleId) {
        if (Loader::includeModule("iblock")) {
            $arVehicle = [];
            $arSelect = array(
                "ID",
                'NAME',
                'PROPERTY_VEHICLE_TYPE',
                'PROPERTY_MODEL',
            );
            $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, 'ID' => $vehicleId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arVehicle = [
                    'ID' => $arFields['ID'],
                    'NAME' => $arFields['NAME'],
                    'TYPE' => getUserVehicleType($arFields['PROPERTY_VEHICLE_TYPE_VALUE'][0]),
                    'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                ];
            }
            if (!empty($arVehicle)) {
                return $arVehicle;
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

//функция возвращает структуру разделов от корня
function getLocationStructure()
{
    if (Loader::includeModule("iblock")) {
        $arSections = [];
        $arFilter = array('IBLOCK_ID' => IB_LOCATIONS, 'GLOBAL_ACTIVE' => 'Y');
        $db_list = CIBlockSection::GetList([], $arFilter, true);
        while ($ar_result = $db_list->GetNext()) {
            $arSections[] = [
                'ID' => $ar_result['ID'],
                'NAME' => $ar_result['NAME'],
                'DEPTH_LEVEL' => $ar_result['DEPTH_LEVEL'],
            ];
        }
        if (!empty($arSections)) {
            $arResult = [];
            foreach ($arSections as $section) {
                switch ($section['DEPTH_LEVEL']) {
                    case '1':
                        $arResult['CATEGORY'][] = $section;
                        break;
                    case '2':
                        $arResult['TYPE'][] = $section;
                        break;
                    case '3':
                        $arResult['LOCATION'][] = $section;
                        break;
                }
            }
            if (!empty($arResult)) {
                return $arResult;
            }
        }
    }
}

//функция возвращает перечень объектов раздела
function getObjects($locationId = false)
{
    if (Loader::includeModule("iblock")) {
        $arObjects = [];
        $arSelect = array(
            "ID",
            'NAME',
        );
        $arFilter = array("IBLOCK_ID" => IB_LOCATIONS);
        if ($locationId) {
            $arFilter['SECTION_ID'] = $locationId;
        }
        $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arObjects[] = [
                'ID' => $arFields['ID'],
                'NAME' => $arFields['NAME'],
            ];
        }
        if (!empty($arObjects)) {
            return $arObjects;
        }
    }
}

//функция возвращает разницу между датами в днях
function getDateDiff($dateFrom, $dateTo)
{
    if ($dateFrom && $dateTo) {
        $date1 = new DateTime($dateFrom);
        $date2 = new DateTime($dateTo);
        $interval = $date1->diff($date2);
        return $interval->days;
    }
}

function getObjectCharacteristic($characteristicId = false)
{
    if (Loader::includeModule("highloadblock")) {
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_FEATURES)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $filter = [
            "select" => ["*"],
            "order" => ["ID" => "ASC"],
        ];
        if ($characteristicId) {
            $filter["filter"] = ["UF_XML_ID" => $characteristicId];
        } else {
            $filter["filter"] = [];
        }
        $data = $entity_data_class::getList($filter);
        $arCharacteristics = [];
        while ($arData = $data->Fetch()) {
            $arCharacteristics[] = [
                "NAME" => $arData["UF_OF_NAME"],
                "VALUE" => $arData["UF_XML_ID"],
            ];
        }
        if (!empty($arCharacteristics)) {
            return $arCharacteristics;
        } else {
            return false;
        }
    }
}

function getObjectType($objectTypeId = false)
{
    if (Loader::includeModule("highloadblock")) {
        $hlblock = HL\HighloadBlockTable::getById(HL_OBJECT_TYPE)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $filter = [
            "select" => ['ID', "UF_NAME"],
            "order" => ["ID" => "ASC"],
        ];
        if ($objectTypeId) {
            $filter["filter"] = ["UF_XML_ID" => $objectTypeId];
        } else {
            $filter["filter"] = [];
        }
        $rsData = $entity_data_class::getList($filter);
        $arObjectTypes = [];
        while ($arData = $rsData->Fetch()) {
            $arObjectTypes[] = [
                'ID' => $arData["ID"],
                'NAME' => $arData["UF_NAME"],
            ];
        }
        if (!empty($arObjectTypes)) {
            return $arObjectTypes;
        } else {
            return false;
        }
    }
}

function getIblockListProperties($iblockId, $arPropCodes = false)
{
    if ($iblockId) {
        if (Loader::includeModule("iblock")) {
            $arProps = [];
            $filter = ["IBLOCK_ID" => $iblockId];
            if ($arPropCodes) {
                $filter["CODE"] = $arPropCodes;
            }
            $property_enums = CIBlockPropertyEnum::GetList(array("DEF" => "DESC", "SORT" => "ASC"), $filter);
            while ($enum_fields = $property_enums->GetNext()) {
                $arProps[$enum_fields['PROPERTY_CODE']][] = [
                    'ID' => $enum_fields['ID'],
                    'PROPERTY_ID' => $enum_fields['PROPERTY_ID'],
                    'VALUE' => $enum_fields['VALUE'],
                    'PROPERTY_NAME' => $enum_fields['PROPERTY_NAME'],
                    'PROPERTY_CODE' => $enum_fields['PROPERTY_CODE'],
                ];
            }
            if (!empty($arProps)) {
                return $arProps;
            } else {
                return false;
            }
        }
    }
}

function getPartnersList($arPartnersId = [])
{
    if (Loader::includeModule("highloadblock")) {
        $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $arPartners = [];
        $filter = [];
        if (!empty($arPartnersId)) {
            $filter = [
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => ['ID' => $arPartnersId]
            ];
        } else {
            $filter = [
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => []
            ];
        }
        $data = $entity_data_class::getList($filter);
        while ($arData = $data->Fetch()) {
            $arPartners[] = [
                'ID' => $arData['ID'],
                'NAME' => $arData['UF_NAME'],
                'PARTNER_EMAIL' => $arData['UF_PARTNER_EMAIL'],
                'TELEGRAM_API' => $arData['UF_TELEGRAM_API'],
                'CHAT_ID' => $arData['UF_CHAT_ID'],
            ];
        }
        if (!empty($arPartners)) {
            return $arPartners;
        } else {
            return false;
        }
    }
}

/*function translit($value)
{
    $converter = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
    );

    $value = strtr($value, $converter);
    return $value;
}*/

function getObjectPartnerByName($partnerName)
{
    if ($partnerName) {
        if (Loader::includeModule("highloadblock")) {
            $hlblock = HL\HighloadBlockTable::getById(HL_PARTNERS)->fetch();
            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $arPartner = [];
            $data = $entity_data_class::getList(array(
                "select" => array("*"),
                "order" => array("ID" => "ASC"),
                "filter" => array("UF_NAME" => $partnerName)
            ));
            while ($arData = $data->Fetch()) {
                $arPartner = [
                    'ID' => $arData['ID'],
                    'NAME' => $arData['UF_NAME'],
                    'PARTNER_EMAIL' => $arData['UF_PARTNER_EMAIL'],
                    'TELEGRAM_API' => $arData['UF_TELEGRAM_API'],
                    'CHAT_ID' => $arData['UF_CHAT_ID'],
                ];
            }
            if (!empty($arPartner)) {
                return $arPartner;
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

//возвращает все разделы элемента
function getObjectSections($objectId)
{
    if ($objectId) {
        if (Loader::includeModule("iblock")) {
            $arObjectSections = [];
            $objectGroups = CIBlockElement::GetElementGroups($objectId, true);
            while ($arGroup = $objectGroups->Fetch()) {
                $arObjectSections[] = [
                    'ID' => $arGroup['ID'],
                    'NAME' => $arGroup['NAME'],
                    'DEPTH_LEVEL' => $arGroup['DEPTH_LEVEL'],
                ];
            }
            if (!empty($arObjectSections)) {
                return $arObjectSections;
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

function getUserGroupDataByUserId($userId)
{
    if ($userId) {
        if (Loader::includeModule("iblock")) {
            $arUserGroup = [];
            $arSelect = array("ID", "PROPERTY_USER_GROUP");
            $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields) {
                    $arUserGroup[$arFields['ID']] = $arFields['PROPERTY_USER_GROUP_VALUE'];
                }
            }
            if (!empty($arUserGroup)) {
                $temp = [];
                foreach ($arUserGroup as $groupElements) {
                    if (!empty($groupElements)) {
                        $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME");
                        $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ID" => $groupElements);
                        $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                        while ($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            $groupElement = [
                                'ID' => $arFields['ID'],
                                'NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                                'LAST_NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                                'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                            ];
                            $temp[] = $groupElement;
                        }
                    } else {
                        return false;
                    }
                }
                return $temp;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getUserTransportByUserId($userId)
{
    if ($userId) {
        if (Loader::includeModule("iblock")) {
            $arUserTransport = [];
            $arSelect = array("ID", "PROPERTY_USER_TRANSPORT");
            $arFilter = array("IBLOCK_ID" => IB_USERS, "PROPERTY_USER_ID" => $userId);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                if ($arFields) {
                    $arUserTransport[$arFields['ID']] = $arFields['PROPERTY_USER_TRANSPORT_VALUE'];
                }
            }
            if (!empty($arUserTransport)) {
                $temp = [];
                foreach ($arUserTransport as $arTransportId) {
                    $arSelect = array(
                        "ID",
                        "PROPERTY_VEHICLE_TYPE",
                        "PROPERTY_DRIVING_LICENSE_SERIES",
                        "PROPERTY_DRIVING_LICENSE_NUMBER",
                        "PROPERTY_MODEL",
                        "PROPERTY_INSPECTION_DATE",
                        "PROPERTY_BLOCKED",
                    );
                    $arFilter = array("IBLOCK_ID" => IB_TRANSPORT, "ID" => $arTransportId);
                    $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
                    while ($ob = $res->GetNextElement()) {
                        $arFields = $ob->GetFields();
                        $element = [
                            'ID' => $arFields['ID'],
                            'VEHICLE_TYPE' => getUserVehicleType($arFields['PROPERTY_VEHICLE_TYPE_VALUE']),
                            'DRIVING_LICENSE_SERIES' => $arFields['PROPERTY_DRIVING_LICENSE_SERIES_VALUE'],
                            'DRIVING_LICENSE_NUMBER' => $arFields['PROPERTY_DRIVING_LICENSE_NUMBER_VALUE'],
                            'MODEL' => $arFields['PROPERTY_MODEL_VALUE'],
                            'INSPECTION_DATE' => $arFields['PROPERTY_INSPECTION_DATE_VALUE'],
                            'BLOCKED' => $arFields['PROPERTY_BLOCKED_VALUE'],
                        ];
                        $temp[] = $element;
                    }
                }
                return $temp;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function registerNewUser($userData)
{
    if ($userData) {
        if (isset($userData['NAME']) && isset($userData['LAST_NAME']) && isset($userData['EMAIL'])) {
            $passwd = generateRandomString();
            $user = new CUser;
            $arFields = array(
                "NAME" => $userData['NAME'],
                "LAST_NAME" => $userData['LAST_NAME'],
                "SECOND_NAME" => $userData['SECOND_NAME'] ? $userData['SECOND_NAME'] : '',
                "EMAIL" => $userData['EMAIL'],
                "WORK_PHONE" => $userData['PHONE'] ? $userData['PHONE'] : '',
                "LOGIN" => $userData['EMAIL'],
                "LID" => "ru",
                "ACTIVE" => "Y",
                "GROUP_ID" => array(10),
                "PASSWORD" => $passwd,
                "CONFIRM_PASSWORD" => $passwd,
            );
            $ID = $user->Add($arFields);
            if (intval($ID) > 0) {
                $newUserData = [
                    'ID' => $ID,
                    'LOGIN' => $userData['EMAIL'],
                    'PASSWD' => $passwd,
                    "NAME" => $userData['NAME'],
                    "LAST_NAME" => $userData['LAST_NAME'],
                    "SECOND_NAME" => $userData['SECOND_NAME'],
                    "PHONE" => $userData['PHONE'],
                ];
                sendEmail(
                    'NEW_USER',
                    14,
                    [
                        'EMAIL' => $newUserData['LOGIN'],
                        'USER_FIO' => $newUserData['LAST_NAME'] . ' ' . $newUserData['NAME'] . ' ' . $newUserData['SECOND_NAME'],
                        'AUTH_LINK' => "https://" . $_SERVER['HTTP_HOST'] . '/auth/',
                        'NEW_USER_LOGIN' => $newUserData['LOGIN'],
                        'NEW_USER_PASSWD' => $newUserData['PASSWD'],
                    ]);
                return $newUserData;
            } else {
                return $user->LAST_ERROR;
            }
        } else {
            return false;
        }
    }
}

function sendEmail($eventName, $mesId, $arFields)
{
    if ($eventName && $mesId && $arFields) {
        \Bitrix\Main\Mail\Event::send([
            "EVENT_NAME" => $eventName,
            'MESSAGE_ID' => $mesId,
            "LID" => "s1",
            "C_FIELDS" => $arFields
        ]);
    }
}

function getDatePeriod($startDate, $endDate)
{
    if ($startDate && $endDate) {
        $arPeriod = [];
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $step = new DateInterval('P1D');
        $period = new DatePeriod($start, $step, $end);
        foreach ($period as $datetime) {
            $arPeriod[] = $datetime->format("d.m.Y");
        }
        $arPeriod[] = $endDate;
        if (!empty($arPeriod)) {
            return $arPeriod;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function checkRoute($objectId)
{
    if ($objectId) {
        $sections = getObjectSections($objectId);
        if ($sections) {
            $result = false;
            foreach ($sections as $section) {
                if ($section['ID'] == TOURISTS_ROUTS_SECTION_ID) {
                    $result = true;
                }
            }
            return $result;
        } else {
            return false;
        }
    } else {
        return false;
    }
}