<?php
namespace penguin\form;
use penguin\db\DB;

use penguin\common\Functions;

use \penguin\Import;
use \Exception;

/**
 * 
 * @author johs
 * 
 * Form dictionary parameters:
 * table				The table in the database
 * where				An array of conjugated statements, i.e. $where[0] and $where[1] etc.
 * order				string of order in form: field1 [asc], field2 desc
 * fields				An array of field dictionaries, see Field dictionary
 * method				form method, i.e. post or get
 * deleteable			boolean, will add delete button if true, default true
 * addline				boolean, will add "add new" button if true, default true
 * action				form action, default Functions::getArgs()
 * submitlabel			Label of submit button
 * links				array of link dictionaries, see Link dictionary
 * subform				in form of a form dictionary
 * rowanchor			boolean, will add anchors with names for each rows, default false
 * displayheader		boolean, will add headers automatically, default false
 * type					'single' or 'continous', default is 'continous'
 * 
 * Field dictionary parameters:
 * name					name of field in table
 * type					type of field, see field types
 * validate				a boolean closure function taking the submitted value
 * dict					dictionary depending on type
 * default				default value
 * width				width of field
 * 
 * Field types:
 * combobox				will show combobox based on the dict
 * hidden				will hide
 * date					will show datepicker
 * checkbox				will show as checkbox
 * 
 * Link dictionary:
 * url					url to goto on click, any instance of %id will be substituted to the row id
 * label				Label to link button
 * 
 */
class Form {
	static $formids = array();
	static $jsdone = false;
	
	static function js() {
		if (Form::$jsdone) return false;
		Form::$jsdone = true;
		return Import::JS()."<script src=\"".TEMPLATE_MEDIA_PATH."form/js/Filepicker.js\" type=\"text/javascript\"></script><script src=\"".TEMPLATE_MEDIA_PATH."form/js/Data.js\" type=\"text/javascript\"></script><script src=\"".TEMPLATE_MEDIA_PATH."form/js/Form.js\" type=\"text/javascript\"></script>";
	}
	private static function getRS($table,$where,$order,$subforminfo=false) {
		$sql = "SELECT * FROM $table";
		$first = true;
		foreach ($where as $c) {
			if ($subforminfo)
				$c = str_replace("%id",$subforminfo['id'],$c);
			if ($first) {					
				$sql.= " WHERE ($c)";
				$first = false;
			} else {
				$sql .= " and ($c)";
			}
		}
		if ($order) {
			$sql .= " ORDER by $order";
		}
		
		return DB::fetch($sql);
	}
	private static function checkTableForm($formid,$table,$ar,$fields,$subform,$allfields,$onupdate,$relocate=true) {
		$test = function ($val) use (&$test) {
			if (is_array($val)) {
				$val = array_map($test,$val);
			} else {
				$val = mysql_real_escape_string($val);
			}
			return $val;
		};
		$ar = $test($ar);
		$line = 0;
		while (isset($ar[$formid."_id_".$line])) {
			if ($ar[$formid."_".$line."_dirty"]==1) {
				$qry = array();
				foreach($fields as $field) {
					$type=Functions::nz($field['type'],$allfields[$field['name']]['type']);
					if ($type!="hidden") {
						if ($type=="checkbox") {
							$val = isset($ar[$formid."_".$field['name']."_".$line]) ? 'true' : 'false';
						} elseif ($type=="date") {
							if ($ar[$formid."_".$field['name']."_".$line]=="") {
								$val="0000-00-00";
							} else {
								$tmp = new Date();
								$tmp->fromstring($ar[$formid."_".$field['name']."_".$line]);
								$val = $tmp->toUS();
							}
						} elseif($type=="real") {
							$val = str_replace(",",".",str_replace(".","",$ar[$formid."_".$field['name']."_".$line]));
						} else {
							$val = $ar[$formid."_".$field['name']."_".$line];
						}
						if ($validate = Functions::nz($field['validate'],false)) {
							if ($validate($val)) {
								$qry[$field['name']] = $val;
							}	
						} else {
							$qry[$field['name']] = $val;
						}
					}
				}
				DB::ezQuery("UPDATE",$table,$qry,"id=".$ar[$formid."_id_".$line]);
				if ($onupdate) {
					$onupdate($ar[$formid."_id_".$line]);
				}
			}
			$line++;
		}
		if ($ar[$formid."_id_new"]==1) {
			$qry = array();
			foreach($fields as $field) {
				$type=Functions::nz($field['type'],$allfields[$field['name']]['type']);
				if ($type=="checkbox") {
					$val = isset($ar[$formid."_".$field['name']."_new"]) ? 'true' : 'false';
				}  elseif ($type=="date") {
					if ($ar[$formid."_".$field['name']."_new"]=="") {
						$val="0000-00-00";
					} else {
						$tmp = new Date();
						$tmp->fromstring($ar[$formid."_".$field['name']."_new"]);
						$val = $tmp->toUS();
					}
				} elseif($type=="real") {
					$val = str_replace(",",".",str_replace(".","",$ar[$formid."_".$field['name']."_new"]));
				} else {
					$val = $ar[$formid."_".$field['name']."_new"];
				}
				if ($validate = Functions::nz($field['validate'],false)) {
					if ($validate($val)) {
						$qry[$field['name']] = $val;
					}	
				} else {
					$qry[$field['name']] = $val;
				}
				
			}	
			
			DB::ezQuery("INSERT",$table,$qry);
			if ($onupdate) {
				$id = DB::getLastId();
				$onupdate($id);
			}
		}
		if ($subform) {
			foreach ($ar[$formid.'_subforms'] as $subformid) {
				$rs = DB::fetch("SELECT * FROM ".$subform['table']);
				$allFields = array();
				$qryFields =$rs->fetchFields();
				foreach ($qryFields as $f) {
					$allFields[$f->name] = array('type'=>$f->type);
					
				}
				Form::checkTableForm($subformid,$subform['table'],$ar,$subform['fields'],false,$allFields,Functions::nz($subform['onupdate'],false),false);
			}
		}
		if ($relocate)
			header('Location: '.Functions::getArgs(''));
	}
	static function display($dict,$subforminfo=false) {
		$table = Functions::nz($dict['table'],false);
		$tableclass = Functions::nz($dict['tableclass'],false);
		$where = Functions::nz($dict['where'],array());
		$order = Functions::nz($dict['order'],false);
		$fields = Functions::nz($dict['fields'],false);
		$method = Functions::nz($dict['method'],"post");
		$deleteable = Functions::nz($dict['deleteable'],true);
		$action = Functions::nz($dict['action'],Functions::getArgs());
		$submitlabel = Functions::nz($dict['submitlabel'],"Update");
		$addlabel = Functions::nz($dict['addlabel'],"Add new");
		$links = Functions::nz($dict['links'],array());
		$subform = Functions::nz($dict['subform'],false);
		$rowanchor = Functions::nz($dict['rowanchor'],false);
		$displayheader = Functions::nz($dict['displayheader'],false);
		$onupdate = Functions::nz($dict['onupdate'],false);
		$ondelete = Functions::nz($dict['ondelete'],false);
		$safedelete = Functions::nz($dict['safedelete'],false);
		$addline = Functions::nz($dict['addline'],true);
		$continous = Functions::nz($dict['type'],'continous')=='continous';
		$out = array();
		if ($table) {
			$i=0;
			while (isset(Form::$formids[$formid= ($subforminfo ? $subforminfo['pre']."_" : '').$table."_".$i])) {
				$i++;
			}
			Form::$formids[$formid] = true;
			
			
			$rs = Form::getRS($table,$where,$order,$subforminfo);
			$allFields = array();
			$qryFields =$rs->fetchFields();
			foreach ($qryFields as $f) {
				$allFields[$f->name] = array('type'=>$f->type);
				
			}
			
			if ($method=="post" && isset($_POST[$formid."_submit"]) ){
				Form::checkTableForm($formid,$table,$_POST,$fields,$subform,$allFields,$onupdate);
			} elseif($method=="get" && isset($_GET[$formid."_submit"])) {
				Form::checkTableForm($formid,$table,$_GET,$fields,$subform,$allFields,$onupdate);
			}
			if (isset($_GET['delete']) && isset($_GET['formid']) && $_GET['formid']==$formid) {
				if ($ondelete) $ondelete($_GET['delete']);
				DB::query("DELETE FROM $table WHERE id=".$_GET['delete']);
				header ("Location: ".Functions::getArgs('delete;formid'));
			}
			
			if (!$fields) {
				$fields=array();
				foreach ($qryFields as $f) {
					$fields[] = array('name'=>$f->name);
				}
			}
			
			# generate pretty names
			foreach ($fields as $k=>$f) {
				if (!Functions::nz($f['prettyname'],false)) {
					$fields[$k]['prettyname'] = Form::makePrettyName($f['name']);
				}
			}
			
			if (!$subforminfo) {
				$out[] = "<form name=\"$formid\" method=\"$method\" action=\"\">";
			} else {
				$out[] = "<input type=\"hidden\" name=\"{$subforminfo['pre']}_subforms[]\" value=\"$formid\" />";
			}
			if ($continous) {
				$out[] = "<table".($tableclass ? " class=\"$tableclass\""  : '').">";
				$cols = count($fields)+count($links);
				if ($deleteable) $cols++;
				if ($displayheader) {
					$out[] = "<thead>";
					$out[] = "<tr>";
					foreach ($fields as $f) {
						$type = isset($f['type']) ? $f['type'] : $allFields[$f['name']]['type'];
						if ($type != "hidden") {
							$out[] = "<th>{$f['prettyname']}</th>";
						}
					}
					$out[] = "</tr>";
					$out[] = "</thead>";
				}
				$line=0;
				$out[] = "<tbody>";
				while ($row = $rs->next()) {
					$out[] = "<input type=\"hidden\" name=\"{$formid}_id_$line\" value=\"{$row['id']}\" />";
					$out[] = "<input type=\"hidden\" name=\"{$formid}_{$line}_dirty\" value=\"0\" />";
					$out[] = "<tr>";
				
					$firstfield = true;
					foreach ($fields as $f) {
						list($hidden,$str) = Form::displayField($formid,$f,$allFields,$line,$row);
						if (!$hidden) $out[] = "<td>";					
						if ($firstfield && !$hidden) {
							if ($rowanchor) {
								$out[] = "<a name=\"id{$row['id']}\"></a>";
							}
							$firstfield=false;
						}
						$out[] = $str;
						if (!$hidden) $out[] = "</td>";					
					}
					if ($deleteable) {
						if (!$safedelete) {
							$out[] = "<td><input class=\"btn btn-default\" type=\"button\" onClick=\"location='".$action."&formid=$formid&delete={$row['id']}'\" value=\"Delete\" /></td>";
						} else {
							$out[] = "<td><input class=\"btn btn-default\" type=\"button\" onClick=\"ConfirmBox.display('Are you sure you want to delete this?','".$action."&formid=$formid&delete={$row['id']}')\" value=\"Delete\" /></td>";
						}
					}
					foreach ($links as $link) {
						$url = str_replace("%id",$row['id'],$link['url']);
						if (Functions::nz($link['fancybox'],false)) {
							$onclick = "$.fancybox({href:'$url'})";
						} else {
							$onclick = "location='$url'";
						}
						$out[] = "<td><input class=\"btn btn-default\" type=\"button\" onClick=\"$onclick\" value=\"{$link['label']}\" /></td>";
					}
					$out[] = "</tr>";
					if ($subform) {
						$dict = array('pre'=>$formid,'id'=>$row['id']);
						$subformdict = $subform;
						foreach ($subformdict['fields'] as $k=>$f) {
							if (isset($f['default'])) {
								$subformdict['fields'][$k]['default'] = str_replace("%id",$row['id'],$f['default']);
							}
						}
						$out[] = "<tr><td></td><td colspan=\"".($cols-1)."\">".Form::display($subformdict,$dict)."</td></tr>";
					}
					$line++;
				}
				if ($addline) {
					$out[] = "<tr><td colspan=\"$cols\"><input type=\"hidden\" name=\"{$formid}_id_new\" value=\"0\" /><input class=\"btn btn-default\" type=\"button\" value=\"$addlabel\" onclick=\"Form.addnew('$formid',this)\" /></td></tr>";
					$out[] = "<tr style=\"display: none\" id=\"{$formid}_addnew\">";
					foreach ($fields as $f) {					
						list($hidden,$str) = Form::displayField($formid,$f,$allFields);
						if (!$hidden) $out[] = "<td>";
						$out[] = $str;
						if (!$hidden) $out[] = "</td>";
					}
					$out[] = "</tr>";
				}
				$out[] = "</tbody>";
				$out[] = "</table>";
			} else {
				$row = $rs->next();
				if ($row) {
					$out[] = "<input type=\"hidden\" name=\"{$formid}_id_0\" value=\"{$row['id']}\" />";
					$out[] = "<input type=\"hidden\" name=\"{$formid}_0_dirty\" value=\"0\" />";
				} else {
					$out []= "<input type=\"hidden\" name=\"{$formid}_id_new\" value=\"1\" />";
				}
				
				$out[] = "<table>";
				foreach($fields as $f) {
					if ($row) {
						list($hidden,$str) = Form::displayField($formid,$f,$allFields,0,$row);
					} else {
						list($hidden,$str) = Form::displayField($formid,$f,$allFields);
					}
					if (!$hidden)
						$out[] = "<tr><td>{$f['prettyname']}</td><td>";
					$out[] = $str;
					if (!$hidden)
						$out[]= "</td></tr>";
				}
				
				$out[] = "</table>";
			}
			if (!$subforminfo) {
				$out[] = "<input class=\"btn btn-default\" type=\"submit\" name=\"{$formid}_submit\" value=\"$submitlabel\" />";
				$out[] = "</form>";
			}
		}
		return join("\n",$out);
	}
	private static function displayField($formid,$f,$allFields,$line=false,$row=false) {
		$out = array();
		$hidden = false;
		if ($line!==false) {
			$inputname = "{$formid}_{$f['name']}_$line";
			$onchange = "Form.makedirty('$formid',$line)";
		} else {
			$inputname = "{$formid}_{$f['name']}_new";
			$onchange = false;
		}
		$type = isset($f['type']) ? $f['type'] : $allFields[$f['name']]['type'];
		
		$default = Functions::nz($f['default'],"");
		if ($row) {
			$default = $row[$f['name']];	
		}
		if ($type=="combobox") {
			$dict = Functions::nz($f['dict'],false);
			if (!$dict) throw new FormException("No dict");
			$dict["default"]=$default;
			$dict["name"]=$inputname;
			$dict["onchange"]=$onchange;
			$dict['class'] = 'form-control';
			$out[] = ComboBox::display($dict);
		} elseif($type=="hidden") {
			$hidden = true;
			$out[] = "<input type=\"hidden\" name=\"$inputname\" value=\"$default\" />";
		} elseif($type=="date") {
			if ($default!='') {
				$d = new Date();
				$d->fromUS($default);
				$default = $d->toEU();
			}
			$dict = array(
				"name"=>$inputname,
				"default"=>$default,
				"eu"=>true,
				"onchange"=>$onchange,
				);
			$out[] = DateBox::display($dict);
		} elseif ($type=='image' || $type=="file") {
			$txt= "";
			if ($default!="") {
				if ($type =='image') {
					$txt.= "<input class=\"btn btn-default\" type=\"button\" onclick=\"$.fancybox({href:$(this.nextSibling).val()});\" value=\"Show\">";
				} else {
					$txt.= "<input class=\"btn btn-default\" type=\"button\" onclick=\"window.open($(this.nextSibling).val(),'_blank');\" value=\"Open\">";
				}
			}
			$txt .= "<input type=\"hidden\" name=\"$inputname\" value=\"$default\" ".($onchange ? " onchange=\"$onchange\"" : '')." /><input type=\"button\" onclick=\"Form.pickfile(this.previousSibling)\" value=\"Pick\">";
			$out[]=$txt;
		} elseif($type=="checkbox") {
			$out[] = "<input class=\"form-control\" type=\"checkbox\"".($onchange ? " onchange=\"$onchange\"" : '')." name=\"$inputname\"".($default=='true'?'checked="checked"':'')."/>";
		} elseif($type=="int") {
			$out[] = "<input class=\"form-control\"".($onchange ? " onchange=\"$onchange\"" : '')." style=\"width: ".Functions::nz($f['width'],100)."px;text-align:right;\" type=\"text\" value=\"$default\" name=\"$inputname\" />";
		} elseif ($type=="real") {
			$out[] = "<input class=\"form-control\"".($onchange ? " onchange=\"$onchange\"" : '')." style=\"width: ".Functions::nz($f['width'],100)."px;text-align:right;\" type=\"text\" value=\"".str_replace(".",",",$default)."\" name=\"$inputname\" />";
		} else {
			$out[] = "<input class=\"form-control\"".($onchange ? " onchange=\"$onchange\"" : '')." style=\"width: ".Functions::nz($f['width'],100)."px;\" type=\"text\" value=\"$default\" name=\"$inputname\" />";
		}
		return array($hidden,join("\n",$out));
	}
	static function makePrettyName($str) {
		if (substr($str,-3)=="_id") $str = substr($str,0,-3);
		return ucfirst(str_replace("_"," ",$str));
	}
}