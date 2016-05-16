<?php
class Patient extends VWObject {

	static function getTableName() { return 'patientDataHA'; }

	function __construct($id) {
		parent::__construct($id);
	}
}