<?php
class CRMContact extends VWObject {

	static function getTableName() { return 'crm_contact'; }


	function __construct($id) {
		parent::__construct($id);
	}

}
