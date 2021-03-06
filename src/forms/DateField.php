<?php

namespace penguin\forms;

use penguin\forms\widgets\DateInputWidget;

class DateField extends FormField
{
    public function __construct($dict = array())
    {
        $this->widget = new DateInputWidget();
        parent::__construct($dict);
    }
}
