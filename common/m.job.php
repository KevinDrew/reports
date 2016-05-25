<?php
class Job extends VWObject {

	static function getTableName() { return 'jobs_db'; }
	static function getClassFields() { 
		$classFields = parent::getClassFields();
		$classFields['nurse_accounts_db'] = 'NurseAccount';  // override to add fields, these fields are FKs to other tables
 		return $classFields;
	}

	static function getListFields() { 
 		return array('startDateTime','bookingType','site_id','s.city as siteName');
	}


	function __construct($id) {
		parent::__construct($id);
	}


	function getHistory() {
		$q =  'SELECT * from Job_History_db where job_id='. intval($this->id_number);
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);
		$rows = array();
		while ($row = $res->fetch(2)) {
			$rows[] = $row;
		}
		return $rows;
	}

	function getbookingsDataHA() {
		return BookingsDataHA::GetAllAsObjects($filter='job_id='. intval($this->id_number).' and bookingType!="deleted"', $sort='');
	}

	function getProgram() {
		$programArr = Program::GetAllAsObjects($filter='job_id='. intval($this->id_number));
		return $programArr[0];
	}
}
