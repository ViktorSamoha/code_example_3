<?php

class Calendar
{
    /**
     * Вывод календаря на один месяц.
     */
    public static function getMonth($month, $year, $events = array())
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

        $out = '
		<div class="calendar_slide swiper-slide">
            <div class="c-month">
			    <div class="c-month_head">' . $months[$month] . ' ' . $year . '</div>
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

            $date_attribute = 'data-date="'.date("d.m.Y",strtotime($day . '.' . $month . '.' . $year)).'"';

            $booked_day = false;

            if (!empty($events)) {

                foreach ($events as $date) {

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
            }

            if ($booked_day) {

                $out .= '<td><span '.$date_attribute.'>' . $day . '</span></td>';

            } else {
                $date = new DateTime($day . '.' . $month . '.' . $year);
                $now = new DateTime();
                if($date < $now && $date->diff($now)->format('%a') != 0){

                    $out .= '<td><span>' . $day . '</span></td>';

                }else{

                    $out .= '<td><span class="day-free" '.$date_attribute.'>' . $day . '</span></td>';

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

        $out .= '</tr></table></div></div>';

        return $out;
    }

    /**
     * Вывод календаря на несколько месяцев.
     */
    public static function getInterval($start, $end, $events = array())
    {
        $curent = explode('.', $start);

        $curent[0] = intval($curent[0]);

        $end = explode('.', $end);

        $end[0] = intval($end[0]);

        $begin = true;

        $out = '<div class="calendar_wrap swiper-wrapper">';

        do {
            $out .= self::getMonth($curent[0], $curent[1], $events);

            if ($curent[0] == $end[0] && $curent[1] == $end[1]) {

                $begin = false;

            }

            $curent[0]++;

            if ($curent[0] == 13) {

                $curent[0] = 1;

                $curent[1]++;

            }
        } while ($begin == true);

        $out .= '</div>';

        return $out;
    }
}