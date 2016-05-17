<?php
class Site extends VWObject {

	static function getTableName() { return 'sites_db'; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getJobs() {
		return Job::GetAllAsObject( $filter = "site_id = ". $this->id_number);
	}
}