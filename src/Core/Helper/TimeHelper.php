<?php

namespace App\Core\Helper;

class TimeHelper
{

    public static function duration(int $duration): string
    {
        $minutes = round($duration / 60);
        if ($minutes < 60) {
            return $minutes.' min';
        }
        $hours = floor($minutes / 60);
        $minutes = str_pad((string) ($minutes - ($hours * 60)), 2, '0', STR_PAD_LEFT);
        return "{$hours}h{$minutes}";
    }

    public static function shortDuration(int $duration)
    {
        $minutes = floor($duration / 60);
        $seconds = $duration - $minutes * 60;
        /** @var int[] $times */
        $times = [$minutes, $seconds];
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes - ($hours * 60);
            $times = [$hours, $minutes, $seconds];
        }
        return implode(':', array_map(
            fn (int $duration) => str_pad(strval($duration), 2, '0', STR_PAD_LEFT),
            $times
        ));
    }

}
