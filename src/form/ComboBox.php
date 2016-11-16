<?php

namespace penguin\form;

use penguin\common\Functions;

class ComboBox
{
    private static $jsdone = false;

    public static function js()
    {
        if (self::$jsdone) {
            return false;
        }
        self::$jsdone = true;

        return '<script src="'.TEMPLATE_MEDIA_PATH.'form/js/ComboBox.js" type="text/javascript"></script>';
    }
    /**
     * @param $dict
     *
     * @return string
     */
    public static function display($dict)
    {
        $class = Functions::nz($dict['class'], false);
        $rs = Functions::nz($dict['rs'], false);
        $array = Functions::nz($dict['array'], false);
        $model = Functions::nz($dict['model'], false);
        if ($rs === false && $array === false && $model === false) {
            throw new FormException('ComboBox: no rs,model or array supplied!');
        }
        $governs = Functions::nz($dict['governs'], false);
        $hidden = Functions::nz($dict['hidden'], 'id');
        $shown = Functions::nz($dict['shown'], 'name');
        $name = Functions::nz($dict['name'], false);
        $id = Functions::nz($dict['id'], false);
        $width = Functions::nz($dict['width'], false);
        $default = Functions::nz($dict['default'], false);
        $firstoption = Functions::nz($dict['firstoption'], false);
        $onchange = Functions::nz($dict['onchange'], false);
        $list = Functions::nz($dict['list'], false);
        $height = Functions::nz($dict['height'], false);
        $disabled = Functions::nz($dict['disabled'], false);
        $goto = Functions::nz($dict['goto'], false);
        $out = array();
        $url = Functions::getArgs($governs)."&{$governs}=%id";
        $out[] = '<select';
        if ($class) {
            $out[] = " class=\"$class\"";
        }
        if ($name) {
            $out[] = " name=\"$name\"";
        }
        if ($id) {
            $out[] = " id=\"$id\"";
        }
        if ($list) {
            $out[] = ' multiple="multiple"';
        }
        if ($disabled) {
            $out[] = ' disabled="disabled"';
        }
        if ($governs) {
            $out[] = " onchange=\"ComboBox.change(this,'$url')\"";
        }
        if ($goto) {
            $out[] = " onchange=\"ComboBox.change(this,'$goto')\"";
        }
        if ($onchange) {
            $out[] = " onchange=\"$onchange\"";
        }
        $out[] = ' style="';
        if ($width) {
            $out[] = "width: {$width}px;";
        }
        if ($height) {
            $out[] = "height: {$height}px;";
        }
        $out[] = '"';
        $out[] = '>';
        if ($firstoption) {
            if (!is_array($firstoption)) {
                throw new FormException('Fix firstoption!');
            }
            $val = Functions::nz($firstoption['value'], '.');
            $out[] = "<option value=\"$val\">".$firstoption['option'].'</option>';
        }
        if ($rs) {
            $rs->moveFirst();
            while ($row = $rs->next()) {
                $out[] = self::option($row, $governs, $hidden, $shown, $default);
            }
        } elseif ($array) {
            foreach ($array as $row) {
                $out[] = self::option($row, $governs, $hidden, $shown, $default);
            }
        } elseif ($model) {
            foreach ($model as $m) {
                $out[] = self::option($m->row(), $governs, $hidden, $shown, $default);
            }
        }
        $out[] = '</select>';

        return implode("\n", $out);
    }
    private static function option($row, $governs, $hidden, $shown, $default)
    {
        $out = array();
        $out[] = '<option';
        if (($governs && isset($_GET[$governs]) && $_GET[$governs] == $row[$hidden]) || ($default && $row[$hidden] == $default)) {
            $out[] = ' selected="selected"';
        }
        $out[] = " value=\"{$row[$hidden]}\">{$row[$shown]}</option>";

        return implode("\n", $out);
    }
}
