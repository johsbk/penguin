<?php

namespace penguin\form;

class Date
{
    public $day;
    public $month;
    public $year;
    public function __construct()
    {
    }
    public function fromstring($date)
    {
        $temp = explode('-', $date);
        $this->day = $temp[0];
        $this->month = $temp[1];
        $this->year = $temp[2];
    }
    public function fromdmy($day, $month, $year)
    {
        $this->day = $day;
        $this->month = $month;
        $this->year = $year;
    }
    public function fromUS($date)
    {
        $temp = explode('-', $date);
        $this->day = $temp[2];
        $this->month = $temp[1];
        $this->year = $temp[0];
    }
    public function isValidDate()
    {
        return checkdate($this->month, $this->day, $this->year);
    }

    public function toUS()
    {
        return $this->year.'-'.$this->month.'-'.$this->day;
    }

    public function toEU()
    {
        return $this->day.'-'.$this->month.'-'.$this->year;
    }
    public function __copy($dato)
    {
        $this->day = $dato->day;
        $this->month = $dato->month;
        $this->year = $dato->year;
    }
    /**
     * Enter description here...
     *
     * @param Date $date
     */
    public function isEqual(&$date)
    {
        return $date->day == $this->day && $date->month == $this->month && $date->year == $this->year;
    }
    public function __toString()
    {
        return $this->toUS();
    }
}
