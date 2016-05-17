<?php
class BookingSystemEmailTemplates extends VWObject {

	static function getTableName() { return 'bookingSystem-EmailTemplates'; }

	function __construct($id) {
		parent::__construct($id);
	}
}