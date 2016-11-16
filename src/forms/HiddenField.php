<?php

namespace penguin\forms;

use penguin\forms\widgets\HiddenInputWidget;

class HiddenField extends FormField
{
    public function __construct($dict = array())
    {
        $this->widget = new HiddenInputWidget();
        parent::__construct($dict);
    }
}
