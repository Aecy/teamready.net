<?php

namespace App\Core\Twig;

use App\Core\Helper\TimeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigTimeExtension extends AbstractExtension
{

    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']]),
            new TwigFilter('duration', [$this, 'duration']),
            new TwigFilter('duration_short', [$this, 'shortDuration'], ['is_safe' => ['html']]),
            new TwigFilter('countdown', [$this, 'countdown'], ['is_safe' => ['html']]),
        ];
    }

    public function ago(\DateTimeInterface $date, string $prefix = ''): string
    {
        $prefixAttribute = !empty($prefix) ? " prefix=\"{$prefix}\"" : '';
        return "<time-ago time=\"{$date->getTimestamp()}\"$prefixAttribute></time-ago>";
    }

    public function countdown(\DateTimeInterface $date): string
    {
        return "<time-countdown time=\"{$date->getTimestamp()}\"></time-countdown>";
    }

    public function duration(int $duration): string
    {
        return TimeHelper::duration($duration);
    }

    public function shortDuration(int $duration): string
    {
        return TimeHelper::shortDuration($duration);
    }

}
