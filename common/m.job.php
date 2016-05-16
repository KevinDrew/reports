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

		// set the siteName
		// $q =  'SELECT city from Sites_db where id_number='. intval($this->site_id);
		// $dbr = Factory::Get('dbread');
		// $res = $dbr->query($q);
		// $rows = array();
		// if ($row = $res->fetch(1)) {
		// 	$this->params['siteName'] = $row[0];
		// }

		//$site = new Site($this->params['site_id']);
		//$this->params['siteName'] = !empty($site->siteName) ? $siteName : $site->city;
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
		// $q =  'SELECT b.id_number,patientData,time,product,attended,general_table,program,consentCard,medicareCharge,SMSReminder,emailReminders,SMS,b.email,iCal,iCalType,signature,b.`key`,reportComplete,sites_id,s.city as s_name, dateType,facilitator,progress,billable,cost,availability,och_bookingsData,facilitatorType,timeEnd,status,added,emailReminderSent,SMSReminderSent,postVaxSent,syncStamp,bookingType,vaccineType,note,consent
		//  from bookingsDataHA b
		//  left join sites_db s on b.sites_id=s.id_number
		//  where job_id='. intval($this->id_number);
		// $dbr = Factory::Get('dbread');
		// $res = $dbr->query($q);
		// $rows = array();
		// while ($row = $res->fetch(2)) {
		// 	$rows[] = $row;
		// }
		// return $rows;
		return BookingsDataHA::GetAllAsObjects($filter='job_id='. intval($this->id_number).' and bookingType!="deleted"', $sort='');
	}

	function getProgram() {
		$q =  'SELECT * from program_db where id_number='. $this->program;
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);
		$rows = array();
		return $res->fetch(2);
	}
}
