<?php

namespace penguin\forms;

use penguin\forms\widgets\TextInputWidget;

class CharField extends FormField
{
    public function __construct($dict = array())
    {
        $this->widget = new TextInputWidget();
        parent::__construct($dict);
    }
}
