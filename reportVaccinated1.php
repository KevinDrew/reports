<?php

$systemStatus = "On";

ini_set('max_input_vars', 30000);

$dbhost = '192.168.3.1';
$dbusername = 'vitalityworks';
$dbuserpassword = 'UhZpc3Y5jx26cPn6';
$default_dbname = 'provax_db';

$MYSQL_ERRNO = '';
$MYSQL_ERROR = '';

function db_connect($dbname = '') {
   global $dbhost, $dbusername, $dbuserpassword, $default_dbname;
   global $MYSQL_ERRNO, $MYSQL_ERROR;

   $link_id = mysql_connect($dbhost, $dbusername, $dbuserpassword);
   if(!$link_id) {
      $MYSQL_ERRNO = 0;
      $MYSQL_ERROR = "Connection failed to the host $dbhost.";
      return 0;
   }
   else if( (empty($dbname) && !mysql_select_db($default_dbname)) || (!empty($dbname) && !mysql_select_db($dbname)) ){
      $MYSQL_ERRNO = mysql_errno();
      $MYSQL_ERROR = mysql_error();
      return 0;
   } 
   else return $link_id;
}

function sql_error() {
   global $MYSQL_ERRNO, $MYSQL_ERROR;

   if(empty($MYSQL_ERROR)) {
      $MYSQL_ERRNO = mysql_errno();
      $MYSQL_ERROR = mysql_error();
   }
   return "$MYSQL_ERRNO: $MYSQL_ERROR";
}
$date = $_GET["date"];

if(!isset($date)) die("Please input report date in format dd-mm");

$date = (isset($date)?$date:"11-04");

$link_id = db_connect('provax_db');
print "Report Input Date : <span style='color:red'>".$date."-2016</span><br/>";
$q = array();
$filename = "../API/Sync/debug/report_$date-2016.txt";
$file = fopen($filename,"r") or die("report not found");
while( !feof($file)) {
	$line = fgets($file);
	$parts = explode(":|", $line);
	$id_number = $parts[1];
	if($parts[0] == 'SQL_insert' || $parts[0]=='booking_report_insert')
		$q[] = 'SELECT b.id_number as bookingNumber, p.sname, p.fname, p.email, b.consent, b.attended FROM bookingsDataHA b left join patientDataHA p on b.patientData = p.id_number WHERE b.tempID='.$id_number;
	elseif ($parts[0] == 'SQL_update' || $parts[0]=='booking_report_update') 
		$q[] = 'SELECT b.id_number as bookingNumber, p.sname, p.fname, p.email, b.consent, b.attended FROM bookingsDataHA b left join patientDataHA p on b.patientData = p.id_number WHERE b.id_number='.$id_number;
}

fclose($file);


print "Total patient consent : <span style='color:red'>".count($q)."</span><br/>";
print "Report last updated : <span style='color:red'>" . date ("F d Y H:i:s.", filemtime($filename)). "</span>";

echo "<br/><br/><br/>";
//die();
print "<table border='1'><tr><th>Booking ID</th><th>First Name</th><th>Surname</th><th>Email</th><th>Consent</th><th>Attended</th></tr>";

foreach($q as $g) {
	$keySearch = mysql_query($g,$link_id);//
	while ($keyData = mysql_fetch_array($keySearch, MYSQLI_ASSOC )) {
		print "<tr><td>".$keyData['bookingNumber']."</td>";
		print "<td>".$keyData['fname']."</td>";
		print "<td>".$keyData['sname']."</td>";
		print "<td>".$keyData['email']."</td>";
		print "<td>".$keyData['consent']."</td>";
		print "<td>".$keyData['attended']."</td></tr>";
	}
	//echo "<pre>".print_r($gaga,true)."</pre>";
}
print "</table>";

?>