<?php
namespace penguin\form;
class Date {

	var $day;
	var $month;
	var $year;
	function Date() {
	}
	function fromstring($date) {
		$temp = explode("-",$date);
		$this->day = $temp[0];
		$this->month = $temp[1];
		$this->year = $temp[2];
	}
	function fromdmy($day, $month, $year) {
		$this->day =$day;
		$this->month =$month;
		$this->year =$year;
	}	
	function fromUS($date) {
		$temp = explode("-",$date);
		$this->day = $temp[2];
		$this->month = $temp[1];
		$this->year = $temp[0];
	}
	function isValidDate () {
		return checkdate ($this->month, $this->day, $this->year);
	}
	
	function toUS () {
		return $this->year."-".$this->month."-".$this->day;
	}
	
	function toEU () {
	return $this->day."-".$this->month."-".$this->year;
	}
	function __copy($dato) {
		$this->day = $dato->day;
		$this->month = $dato->month;
		$this->year = $dato->year;
	}
	/**
	 * Enter description here...
	 *
	 * @param Date $date
	 */
	function isEqual(&$date) {
		return $date->day == $this->day && $date->month == $this->month && $date->year == $this->year;
	}
	function __toString() {
		return $this->toUS();
	}
}
?>