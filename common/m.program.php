<?php
class 										Program extends VWObject {

	static function getTableName() { return 'program_db '; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getJobs() {
		return Job::GetAllAsObjects($filter='program='. intval($this->id_number), $sort='');
	}
}