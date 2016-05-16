<?php
class Booking_typevaccine extends VWObject {

	static function getTableName() { return 'booking_typevaccine'; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getJobs() {
		$q =  'SELECT * from jobs_db where site_id='. $this->id_number;
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);
		$rows = array();
		while ($row = $res->fetch(2)) {
			$rows[] = $row;
		}
		return $rows;
	}

}