<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/reports/DB.php');

$date = $_GET["date"];

if(!isset($date)) die("Please input report date in format dd-mm");

$date = (isset($date)?$date:"11-04");

$link_id = db_connect('provax_db');

$q = array();

$queryTemp1 = "SELECT b.id_number as bookingNumber, p.id_number as patientID, p.sname, p.fname, p.email, b.consent, b.attended, CONCAT(u.`name`, ' ', u.surname) as nurse
FROM bookingsDataHA b 
left join patientDataHA p on b.patientData = p.id_number 
left join nurse_db n on n.job_id=b.job_id
left join user u on u.general_table = n.nurse_id
WHERE ";

$queryTemp2 = "SELECT b.id_number as bookingNumber, p.id_number as patientID, p.sname, p.fname, p.email, b.consent, b.attended, b.job_id as nurse
FROM bookingsDataHA b left join patientDataHA p on b.patientData = p.id_number WHERE ";

$queryTemp = $queryTemp1;

$filename = "../API/Sync/debug/report_$date-2016.txt";
$file = fopen($filename,"r") or die("report not found");
while( !feof($file)) {
	$line = fgets($file);
	$parts = explode(":|", $line);
	$id_number = $parts[1];
	if($parts[0] == 'SQL_insert' || $parts[0]=='booking_report_insert')
		$q[] = $queryTemp.'b.tempID='.$id_number;
	elseif ($parts[0] == 'SQL_update' || $parts[0]=='booking_report_update') 
		$q[] = $queryTemp.'b.id_number='.$id_number;
}

fclose($file);

//die();
print "<table border='1'><tr><th>#</th><th>Booking ID</th><th>Patient ID</th><th>First Name</th><th>Surname</th><th>Email</th><th>Consent</th><th>Attended</th><th>Job</th><th>Type</th></tr>";
$count = 1;
foreach($q as $g) {
	$keySearch = mysql_query($g,$link_id);//
	//while ($keyData = mysql_fetch_array($keySearch, MYSQLI_ASSOC )) {
	$keyData = mysql_fetch_assoc($keySearch);//, MYSQLI_ASSOC );
	if(!empty($keyData)) {
		print "<tr><td>$count</td><td>".$keyData['bookingNumber']."</td>";
		print "<td>".$keyData['patientID']."</td>";
		print "<td>".$keyData['fname']."</td>";
		print "<td>".$keyData['sname']."</td>";
		print "<td>".$keyData['email']."</td>";
		print "<td>".$keyData['consent']."</td>";
		print "<td>".$keyData['attended']."</td>";
		//print "<td>".$keyData['nurse']."</td>";
		print "<td>".$keyData['nurse']."</td>";
		if(count(explode("WHERE b.tempID=",$g))>1)
			print "<td>App AddNew</td></tr>";
		elseif(count(explode("WHERE b.id_number=",$g))>1)
			print "<td>App Update</td></tr>";
		$count++;
	}
	elseif(empty($keyData)) {
		print "<tr><td>$count</td>";
		//die("no data from record $g");
		if(count(explode("WHERE b.tempID=",$g))>1) {
			$id = str_replace($queryTemp."b.tempID=","",$g);
			$id = str_replace("';","",$id);
			print "<td>".$id."</td><td colspan='6'>Return Empty Data </td><td>App AddNew</td></tr>";
		} elseif(count(explode("WHERE b.id_number=",$g))>1) {
			$id = str_replace($queryTemp."b.id_number=","",$g);
			$id = str_replace("';","",$id);
			print "<td>".$id."</td><td colspan='6'>Return Empty Data </td><td>App Update</td></tr>";
		}
		$count++;
	}
}
print "</table>";

?>