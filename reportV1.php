<?php
$date = $_GET["date"];

if(!isset($date)) die("Please input report date in format dd-mm");

$date = (isset($date)?$date:"11-04");

print "Report Input Date : <span style='color:red'>".$date."-2016</span><br/>";
$q = array();
$filename = "../API/Sync/debug/report_$date-2016.txt";
$file = fopen($filename,"r");// or die("report not found");
if($file) {
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
}

print "Total patient consent : <span style='color:red'>".count($q)."</span><br/>";
print "Log File last updated : <span style='color:red'>" . ($filename==""?"n/a":date ("F d Y H:i:s.", filemtime($filename))). "</span>";
print "<table><tr><td>";
if(count($q)>1000)
	print "<button id='gaga' onClick='getLevel2()'>More Details <span style='color:red'>(take long to fetch data)</span></button>";
else 
	print "<button id='gaga' onClick='getLevel2()'>More Details</button>";
print "</td><td><button id='nurse' onClick='getLevel3()'>Show Nurses Used App</button></td></tr></table>";

?>