<?php
class Account extends VWObject {

	static function getTableName() { return 'accounts_db'; }
	static function getDefaultSort() { return 'company_name'; }

	function __construct($id) {
		parent::__construct($id);
	}
}