<?php
namespace penguin\form;
use \penguin\common\Functions;
use \Exception;
class ComboBox {
	private static $jsdone = false;
	
	static function js() {
		if (ComboBox::$jsdone) return false;
		ComboBox::$jsdone = true;
		return "<script src=\"".TEMPLATE_MEDIA_PATH."form/js/ComboBox.js\" type=\"text/javascript\"></script>";
	}
	/**
	 * 
	 * @param $dict
	 * @return string
	 */
	static function display($dict) {
		$class = Functions::nz($dict['class'],false);
		$rs = Functions::nz($dict["rs"],false);
		$array = Functions::nz($dict['array'],false);
		$model = Functions::nz($dict['model'],false);
		if ($rs===false && $array===false && $model===false) throw new Exception("ComboBox: no rs,model or array supplied!");
		$governs = Functions::nz($dict['governs'],false);
		$hidden = Functions::nz($dict['hidden'],'id');
		$shown = Functions::nz($dict['shown'],'name');
		$name = Functions::nz($dict['name'],false);
		$id = Functions::nz($dict['id'],false);
		$width = Functions::nz($dict['width'],false);
		$default = Functions::nz($dict['default'],false);
		$firstoption = Functions::nz($dict['firstoption'],false);
		$onchange = Functions::nz($dict['onchange'],false);
		$list = Functions::nz($dict['list'],false);
		$height = Functions::nz($dict['height'],false);
		$disabled = Functions::nz($dict['disabled'],false);
		$goto = Functions::nz($dict['goto'],false);
		$out = array();
		$url = Functions::getArgs($governs)."&{$governs}=%id";
		$out[] = "<select";
		if ($class) $out[] = " class=\"$class\"";
		if ($name) {
			$out[] = " name=\"$name\"";
		}
		if ($id) {
			$out[] = " id=\"$id\"";
		}
		if ($list) {
			$out[] = " multiple=\"multiple\"";
		}
		if ($disabled) { 
			$out[] = " disabled=\"disabled\"";
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
		$out[] = " style=\"";
		if ($width) {
			$out[] = "width: {$width}px;";
		}
		if ($height) {
			$out[] = "height: {$height}px;";
		}
		$out[] = "\"";
		$out[] = ">";
		if ($firstoption) {
			if (!is_array($firstoption)) throw new Exception("Fix firstoption!");
			$val = Functions::nz($firstoption['value'],".");
			$out[] = "<option value=\"$val\">".$firstoption['option']."</option>";
		}
		if ($rs) {
			$rs->moveFirst();
			while ($row = $rs->next()) {
				$out[] = ComboBox::option($row,$governs,$hidden,$shown,$default);
			}
		} elseif($array) {
			foreach ($array as $row) $out[] = ComboBox::option($row,$governs,$hidden,$shown,$default);
		} elseif ($model) {
			foreach ($model as $m) {
				$out[] = ComboBox::option($m->row(),$governs,$hidden,$shown,$default);
			}
		}
		$out[] = "</select>";
		return join("\n",$out);
	}
	static private function option($row,$governs,$hidden,$shown,$default) {
		$out = array();
		$out[] = "<option";
		if ($governs && isset($_GET[$governs]) && $_GET[$governs]==$row[$hidden])
			$out[] = " selected=\"selected\"";
		elseif ($default && $row[$hidden]==$default) 
			$out[] = " selected=\"selected\"";
		$out[] = " value=\"{$row[$hidden]}\">{$row[$shown]}</option>";
		return join("\n",$out);
	}
}