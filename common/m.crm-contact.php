<?php
class CRMContact extends VWObject {

	static function getTableName() { return 'crm_contact'; }

	function __construct($id) {
		parent::__construct($id);
	}

	// function getContacts() {
	// 	$q =  'SELECT c.id_number,
 // 				c.fname,
 // 				c.sname,
 // 				c.position,
 // 				c.department,
 // 				c.devision,
 // 				c.email,
 // 				c.phone,
 // 				c.primaryBD,
 // 				c.MYOB,
 // 				c.edm,
 // 				c.note,
 // 				c.key,
 // 				c.active,
 // 				c.mobile,
 // 				c.calendar
	// 		from crm_contactsLink l 
	// 		left join crm_contact c on (c.id_number=l.contact_id)
	// 		where company_id='. $this->id_number;
	// 	$dbr = Factory::Get('dbread');
	// 	$res = $dbr->query($q);
	// 	$rows = array();
	// 	while ($row = $res->fetch(2)) {
	// 		$rows[] = $row;
	// 	}
	// 	return $rows;
	// }

}