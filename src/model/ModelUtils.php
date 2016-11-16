<?php

namespace penguin\model;

class ModelUtils
{
    public static function json($input)
    {
        if (is_array($input)) {
            $result = array();
            foreach ($input as $item) {
                /* @var $item BaseModel */
                $result[] = $item->raw();
            }
        } else {
            /* @var $input BaseModel */
            $result = $input->raw();
        }

        return json_encode($result);
    }
}
