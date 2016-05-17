<?php
class Nurse extends VWObject {

	static function getTableName() { return 'general_table'; }
	static function getDefaultSort() { return 'Company_Name'; }
	static function getIdName() { return 'Account_number'; }
	static function getFilter() { return 'nurse="y"'; }

	function __construct($id) {
		parent::__construct($id);
	}
}