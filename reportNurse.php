<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/reports/DB.php');


$link_id = db_connect('provax_db');

$date = isset($_GET['date'])?$_GET['date']:"";
//echo "date:".$date;
$dir    = '../API/Sync/debug';
$files1 = scandir($dir);
$nurseIDArr = array();
foreach($files1 as $f) {
	if(count(explode("GET_",$f)) > 1 && count($a = explode("_",$f)) > 2 && count(explode($date,$a[2])) > 1 ) {		
		$nurseIDArr[] = $a[1];
	}
}

$count=1;
print "<table border='1'><tr><th>#</th><th>NurseID</th><th>First Name</th><th>Surname</th><th>Email</th><th>Action</th></tr>";
foreach($nurseIDArr as $nurseID) {	
	$keySearch = mysql_query("select * from `user` where `general_table`=".$nurseID,$link_id);
	$keyData = mysql_fetch_assoc($keySearch);//, MYSQLI_ASSOC );
	if(!empty($keyData)) {
		print "<tr><td>$count</td><td>".$nurseID."</td>";
		print "<td>".$keyData['name']."</td>";
		print "<td>".$keyData['surname']."</td>";
		print "<td>".$keyData['userid']."</td><td><button onClick='viewLog(\"".$nurseID."_".$date."\")'>View Log</button></td></tr>";
		$count++;
	}
}
print "</table>";
?>
