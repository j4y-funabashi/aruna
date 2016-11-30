<?php

namespace Aruna\Reader;

/**
 * Class ShowDateFeedCommand
 * @author yourname
 */
class ShowDateFeedCommand
{
    public function __construct(
        $year,
        $month,
        $day
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function getMonth()
    {
        return $this->month;
    }

    public function getDay()
    {
        return $this->day;
    }
}
