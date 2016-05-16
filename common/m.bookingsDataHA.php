<?php
class 										BookingsDataHA extends VWObject {

	static function getTableName() { return 'bookingsDataHA'; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getReminders() {
		$q =  'SELECT * from `bookingSystem-reminders` where bookingDB='. $this->id_number;
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);
		$rows = array();
		while ($row = $res->fetch(2)) {
			$rows[] = $row;
		}
		return $rows;
	}

	function getPatient() {
		// $q =  'SELECT * from bookingsDataHA where job_id='. $this->id_number;
		// $dbr = Factory::Get('dbread');
		// $res = $dbr->query($q);
		// $rows = array();
		// while ($row = $res->fetch(2)) {
		// 	$rows[] = $row;
		// }
		// return $rows;
	}
}