<?php
namespace penguin\jobs;
abstract class Job {
	abstract function run();
	function create() {
		
	}
}