<?php

namespace penguin\forms\widgets;

class ImageWidget extends Widget
{
    public function render($name, $value, $attrs, $path)
    {
        return '<div class="fileinput fileinput-'.($value == '' ? 'new' : 'exists').'" data-provides="fileinput">
  <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
    <img data-src="holder.js/100%x100%" alt="..." />
  </div>
  <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">'.
  ($value == '' ? '' : "<img src=\"$path$value\" />")
  .'</div>
  <div>
    <input type="hidden" value="'.$value.'" name="'.$name.'_current" />
    <span class="btn btn-default btn-file"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="'.$name.'"></span>
    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
  </div>
</div>';
    }
}
