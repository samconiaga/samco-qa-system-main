<?php

use Carbon\Carbon;

function toDateString($date, $showWeekday = true)
{
    if (!$date) {
        return '-';
    }

    return Carbon::parse($date)
        ->locale(app()->getLocale())
        ->translatedFormat(
            $showWeekday ? 'l, d M Y' : 'd M Y'
        );
}

function toDateTimeString($date, $hour12 = false, $showWeekday = true)
{
    if (!$date) {
        return '-';
    }

    $timeFormat = $hour12 ? 'h:i A' : 'H:i';

    return Carbon::parse($date)
        ->locale(app()->getLocale())
        ->translatedFormat(
            ($showWeekday ? 'l, ' : '') . "d M Y {$timeFormat}"
        );
}
