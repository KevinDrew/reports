<?php 
$dbhost = 'localhost';  //have to run this from the db server where root login is allowed
$dbusername = 'root';
$dbuserpassword = '1181633eca89553ab34298d6e06d901b';
$dbuserpassword = '';
$default_dbname = 'provax_db';


$conn = mysqli_connect($dbhost, $dbusername, $dbuserpassword);

do {
	$q = "SHOW STATUS LIKE 'Threads_connected';";
	$result = mysqli_query($conn,$q);
	$row = mysqli_fetch_assoc($result);
	
	if ($row['Value'] > 1) {
		print "Logging\n";
		$q2 = "SHOW FULL PROCESSLIST;";
		$result2 = mysqli_query($conn, $q2);
		$rows = array();
		while ($row = mysqli_fetch_assoc($result2)) {
			$rows[] = $row;
		}

		$str = 
			date(DATE_RFC2822). ' ' . $row['Value'] . "\n".
			print_r($rows,1).
			"-------------------------------------------------------------\n";
	
		file_put_contents('mysql_highthreads',$str, FILE_APPEND);
		sleep(30);
		print_r($row,1);
	} else {
		print date(DATE_RFC2822). ' ' . $row['Value'] . "\n";
		sleep(1);
	}

} while (1);
