<?php

namespace penguin\model;

class DateField extends ModelField
{
    
    public function getType()
    {
        return 'date';
    }
    public function getFormType()
    {
        return $this->getType();
    }
    public static function currentDate()
    {
        return date('Y-m-d');
    }
    public function get($value)
    {
        if ($value == '0000-00-00' || $value == '') {
            return null;
        }

        return $value;
    }
    public function set($value)
    {
        if (strlen($value) == 10 && $value{2} == '-') {
            $value = substr($value, 6, 4).'-'.substr($value, 3, 2).'-'.substr($value, 0, 2);
        }

        return $value;
    }
    public function toString($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        } else {
            if ($value == '') {
                return null;
            }

            return $value;
        }
    }
    public function getFormField()
    {
        $f = new \penguin\forms\DateField();
        $f->name = $this->name;
        $f->dbname = $this->dbname;

        return $f;
    }
}
