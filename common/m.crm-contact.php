<?php
class CRMContact extends VWObject {

	static function getTableName() { return 'crm_contact'; }


	function __construct($id) {
		parent::__construct($id);
	}


	function getCompanies($sort='com.company_name') {
		$q = "SELECT com.id_number
			FROM crm_contactslink l
			left join crm_company com on com.id_number=l.contact_ID
			WHERE company_id=". $this->id_number ."
			order by $sort
		";

		$rows = array();
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);

		while($row = $res->fetch()) {
			$rows[] = new CRMCompany($row['id_number']);
		}
		return $rows;
	}	

}
