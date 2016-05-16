<?php
class Transaction extends VWObject {

	static function getTableName() { return 'transactions_db'; }

	function Transaction($id) {
		parent::__construct($id);
	}
}