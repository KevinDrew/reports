<?php
class 										ProgramProduct extends VWObject {

	static function getTableName() { return 'programProducts'; }

	function __construct($id) {
		parent::__construct($id);
	}

	// function getJobHistory() {
	// 	$q =  'SELECT * from Job_History_db where job_id='. $this->id_number;
	// 	$dbr = Factory::Get('dbread');
	// 	$res = $dbr->query($q);
	// 	$rows = array();
	// 	while ($row = $res->fetch(2)) {
	// 		$rows[] = $row;
	// 	}
	// 	return $rows;
	// }
}