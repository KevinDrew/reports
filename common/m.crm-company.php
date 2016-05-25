<?php
class CRMCompany extends VWObject {

	static function getTableName() { return 'crm_company'; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getContacts($sort='con.sname,con.fname') {
		$q = "SELECT con.id_number
			FROM crm_contactslink l
			left join crm_contact con on con.id_number=l.contact_ID
			WHERE company_id=". $this->id_number ."
			order by $sort
		";

		$rows = array();
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);

		while($row = $res->fetch()) {
			$rows[] = new CRMContact($row['id_number']);
		}
		return $rows;
	}

	function getPrograms() {
		return Program::GetAllAsObjects($filter = 'company_id = '. intval($this->id_number));
	}
}