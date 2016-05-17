<?php
class BookingSystemNotification extends VWObject {

	static function getTableName() { return 'bookingSystem-notifications'; }

	function __construct($id) {
		parent::__construct($id);
	}
}