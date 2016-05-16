<?php
class User extends VWObject {

	static function getTableName() { return 'user'; }

	function User($id) {
		parent::__construct($id);
	}
}