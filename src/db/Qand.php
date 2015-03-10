<?php
namespace templates\db;
use templates\model\BaseModel;
use templates\model\DateTimeField;
use templates\model\DateField;
class Qand {
	var $type = 'AND';
	var $args;
	var $scope=0;
	function getNewScope() {
		return 't'.($this->scope++);
	}
	function __construct($args=array()) {
		$this->args=$args;
	}
	function as_sql($model) {
		$args = $this->args;
		ksort($args);
		$args = $this->_handleArgs($args);
		$lines = array();
		$scope = $this->getNewScope();
		$tmp = $this->_as_sql($model,$args,$scope);
		if ($tmp!= "()") $lines[] = $tmp;
		foreach ($this->args as $v) {
			if ($v instanceof Qand) {
				$tmp = $v->as_sql($model);
				if ($tmp!= "()") $lines[] = $tmp;
			}
		}

		return join(' '.$this->type.' ',$lines);
	}
	function _handleArgs($args) {
		#print_r($args);
		$newargs = array();
		foreach($args as $k=>$v) {
			if (!($v instanceof Qand)) {
				$tmp = explode("__",$k,2);
				if (count($tmp)>1) {
					if (!isset($newargs[$tmp[0]])) $newargs[$tmp[0]] = array();
					$newargs[$tmp[0]][$tmp[1]] = $v;
				} else {
					if (!isset($newargs[$tmp[0]])) $newargs[$tmp[0]] =$v;
				}
			}
		}
		foreach ($newargs as $k=>$v) {
			if (is_array($v)) {
				foreach ($v as $k2=>$v2) {
					if (strpos($k2,'__')!==false) {
						$newargs[$k] = $this->_handleArgs($v);
					}
				}
			}
		}
		return $newargs;
	}
	function _as_sql($model,$args,$scope) {
		$exps = array();
		$fields = $model::getFields();
		foreach ($args as $k=>$v) {
			$found = false;
			foreach ($fields as $field) {	
				if ($k==$field->name || $k==$field->dbname) {
					$found = true;
					$operator = '';
					$f = $k;
					if (is_array($v)) {
						foreach ($v as $k2=>$v2) {
							if ($v2 instanceof \DateTime) {
								if ($field instanceof DateTimeField)
									$v2 = $v2->format('Y-m-d H:i:s');
								elseif($field instanceof DateField)
									$v2 = $v2->format('Y-m-d');
								else
									throw new \Exception('Field '.$field->name.' doesnt take a datetime');
							}
							switch($k2) {
								case "contains":
									$operator = 'LIKE';
									$v2 = DB::escape($v2);
									$value = "'%$v2%'";
									break;
								case "in":
									$operator = 'IN';
									if (is_array($v2))
										$value = sprintf("(%s)",join(',',$v2));
									else
										$value = sprintf("(%s)",$v2);
									break;
								case "lte":
									$operator = '<=';
									$v2 = DB::escape($v2);
									$value = "'$v2'";
									break;
								case "gte":
									$operator = '>=';
									$v2 = DB::escape($v2);
									$value = "'$v2'";
									break;
								case "lt":
									$operator = '<';
									$v2 = DB::escape($v2);
									$value = "'$v2'";
									break;
								case "gt":
									$operator = '>';
									$v2 = DB::escape($v2);
									$value = "'$v2'";
									break;
								case "isnull":
									$operator = sprintf("IS %s NULL",$v2?'':'NOT');
									$value = "";
									break;
							}
							if ($operator!='')
								$exps[] = "$scope.`$f` $operator $value";
						}

					} else {
						$operator = '=';
						if ($v instanceof BaseModel)  {
							$value = $v->id;
							$f = sprintf("%s_id",$f);
						} else {
							$value = sprintf("'%s'",DB::escape($v));
						}
						$exps[] = "$scope.`$f` $operator $value";
					}
					if ($operator =='') {
						$m = $model::getForeignKeyModel($f);
						$modelName = $m::getName();
						$newscope = $this->getNewScope();
						$exps[] = "$scope.{$f}_id in (SELECT $newscope.id FROM $modelName $newscope WHERE ".$this->_as_sql($m,$v,$newscope).")";
					}
				}
				
			}
			if (!$found) {
				if ($class = $model::has($k)) {
					$found = true;
					list($m,$f) = $class;
					$modelName = $m::getName();
					$newscope = $this->getNewScope();
					$tmp = "{$scope}.id in (SELECT {$newscope}.$f->dbname FROM $modelName {$newscope} WHERE ".$this->_as_sql($m,$v,$newscope).")";
					$exps[] = $tmp;
					#throw new \Exception($tmp, 1);
					
				}
			}
			if (!$found) 
				throw new \Exception(sprintf("Unknown field: %s in model: %s",$k,$model));
		}
		return '('.join(' '.$this->type.' ',$exps).')';
	}
}