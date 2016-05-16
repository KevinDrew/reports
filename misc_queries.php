<?php 
header( 'Content-type: text/html; charset=utf-8' );
define("_BNEXEC",1);
?>
<?php

include("common/object.php");
include("common/factory.php");

$env = '.kev';
$dbparams = array();
$param1 = isset($_REQUEST['param1']) ? $_REQUEST['param1'] : '1';
if (isset($_REQUEST['dbhost'])) {
	$urldbhost = '&dbhost='.$_REQUEST['dbhost']; // setting this will persist, because we'll keep it in the url
	$dbparams['host'] = $_REQUEST['dbhost'];
} else {
	$urldbhost ='';
}

$queries = array(
	__LINE__ => array( 
		'name' => 'Table Schema',
		'query' =>	"SELECT TABLE_NAME, format(TABLE_ROWS,0) as count_rows from information_schema.TABLES where TABLE_SCHEMA='provax_db'"
	),
	__LINE__ => array( 
		'name' => 'Users',
		'query' =>	
			'SELECT id_number,userid,password,lastaccesstime,email,type,name,surname,general_table,showToDoList,showJobs,showVaccines,SanitariumGroup,landingPage,userTimeOut,admin,manager,nurseVaccinator,operations,operationsManager,healthAssessor,businessDevelopment,businessDevelopmentManager,facilitator,positionTitle,emailSignature,emailUsername,emailPassword,emailIntergration,emailReach,
				`OHS-USERID`,
				mobileNumber,landLine,facilitatorCoordinator,emailIntergrationPart,preemploymentQuiz,`och-operations`,resetPassword,och_nurse,och_physio,och_doctor,och_admin,timeZone,NurseImmuniserQuiz_2014,2015RegComplete,NurseImmuniserQuiz_2015,userKey,
				`mpb-facilitator`,
				2016RegComplete,NurseImmuniserQuiz_2016

				from user			
		',
		'suppress_rowcount'=>false
	),
	__LINE__ => array( 
		'name' => 'General_Table',  // removed record_history, comments
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table limit 0,20	
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Clients',  // removed record_history, comments
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where client = "y" 
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Nurses',  // removed record_history, comments
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where nurse="y" 
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - staff',  // removed record_history, comments
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where staff="y" 	
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Agents',  // removed record_history, comments
		'query' =>	
			'SELECT agent,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,client,nurse,distribution,manufacturer,supplier,support,staff,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where agent="y"',
		'params' => array(			
		)
	),

	__LINE__ => array( 
		'name' => 'Bookings for a job',  // removed record_history, comments
		'query' =>	
			'SELECT b.id_number,b.job_id,p.fname, p.sname, j.jobstatus,consent,attended,patientData,`time`,product,b.general_table,b.program,consentCard,medicareCharge,SMSReminder,emailReminders,SMS,b.email,iCal,iCalType,b.signature,b.`key`,reportComplete,sites_id,dateType,facilitator,progress,billable,cost,availability,och_bookingsData,facilitatorType,timeEnd,status,added,emailReminderSent,SMSReminderSent,postVaxSent,syncStamp,b.bookingType,b.vaccineType as b_vaccineType,j.vaccineType as j_vaccineType,note 
				from bookingsDataHA b
				left join jobs_db j on b.job_id=j.id_number
				left join patientDataHA p on b.patientData=p.id_number
				where b.job_id = '. ($job_id = param('job_id', $default='20642')) .'
				order by consent,attended, `time`',
		'params' => array(
			'job_id' => $job_id
		)
	),


);


$field_links = array(
	'unit_id' => 					'caps'. $env
);

$last_group_by = '';
$gb = '';
$csv = !empty($_REQUEST['csv']);

if (isset($_REQUEST['qid']) || isset($_REQUEST['q_name']) ) {
	if (isset($_REQUEST['q_name'])) {
		foreach ($queries as $id=>$query) {

			if (preg_replace('/=.*/', '', $_REQUEST['q_name']) == preg_replace('/=.*/','',$query['name'])) { //strip off everything before the & to to the comparison
				$_REQUEST['qid'] = $id;
				break;
			}
		}			
	} else {
		if (!isset($queries[$_REQUEST['qid']])) {
			header("Location: misc_queries.php?r=1$urldbhost$urldbname$urlenv");  //outdated link, send them to the list
		} 
	} 
	$dbr = Factory::get('dbread', $dbparams);
	$dbr->query("SET NAMES utf8");	
	$count =0;
	$queryName = $queries[$_REQUEST['qid']]['name'];
	
	if (true && $csv) {  // can turn off for debugging csv output		
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=misc_queries-".preg_replace("/[^A-Za-z0-9]/","_",$queryName).'-'.date('Y-m-d_H-i-s').".csv");
		header("Pragma: no-cache");
		header("Expires: 0");
	}

	hprint ('<a href="?a=0">List</a>&nbsp;&nbsp;');
	if (isset($queries[$_REQUEST['qid']]['params'])) {
		$arrkeys = array_keys($queries[$_REQUEST['qid']]['params']);
		if (isset($arrkeys[0])) {
			hprint('<a href="?qid='. $_REQUEST['qid'] . '&' .$arrkeys[0].'='.($_REQUEST[$arrkeys[0]]-1) .'">&laquo;Prev</a> &nbsp;&nbsp;');
			hprint('<a href="?qid='. $_REQUEST['qid'] . '&' .$arrkeys[0].'='.($_REQUEST[$arrkeys[0]]+1) .'">Next&raquo;</a> &nbsp;&nbsp;');
		}
	}

	hprint ("<br>" .$queryName ."<br>");
	
	hprint ("<pre>\n\n" .$queries[$_REQUEST['qid']]['query'] . "\n\n</pre><br>");	
	hprint (str_repeat('<!-- -->', 50));
	ob_flush();	
	flush();

	$res = $dbr->query($queries[$_REQUEST['qid']]['query']);
	hprint ('count = '.$res->rowCount());
	hprint ('<table border="1">');	 

	while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
		if ($count ==0) {
			hprint ('<tr>');
			if ($count_on = !isset($queries[$_REQUEST['qid']]['suppress_rowcount'])) {
				hprint ('<th></th>');
			}
			$keys = array_keys($row);

			$i=0;
			foreach ($keys as $key) {  															// column headings
				hprint ('<th>');
				cprint ('"');
				print $key;
				$i++;
				cprint('"');
				if ($i!=count($keys)) {
					cprint (','); // work around bug in libre office, ignores column headers if it ends in a comma
				}
				hprint ('</th>');
			}			

			hprint ("</tr>");
			print ("\n");
		}
		
		hprint ('<tr>');
		if ($count_on) {		
			hprint ('<th>'.($count+1).'</th>');
		}
		

		if (startsWith($queryName, 'Data table unit_id') || startsWith($queryName, 'Accata table unit_id')) {

		}

		$col = 0;
		foreach ($row as $key=>$value) {															// data values
			$color = isset($query['colors'][$col]) ? ('bgcolor="'. $query['colors'][$col] .'" ') : '';
			hprint ("<td $color>");
			cprint ('"');
			if ($key != $gb || !isset($row[$gb]) || $last_group_by != $row[$gb] || $csv) {				
				if (isset($field_links[$key]) && !$csv) {
					print ('<a href="http://'.$field_links[$key].$value. $urldbhost .$urldbname.'">'.$value.'</a>');										
				} else {
					print (($value));					
				}
			}
			cprint ("\",");
			hprint ('</td>');
			$col++;
		}		
		hprint ("</tr>");
		print "\n";	

		if (isset($queries[$_REQUEST['qid']]['group_by'])){
			$gb =   $queries[$_REQUEST['qid']]['group_by'];
			$last_group_by = $row[$gb];
		}
		
		$count++;
	}
	hprint ('</table>');
	
	if (startsWith($queries[$_REQUEST['qid']]['name'], 'Data table unit_id =')) { 
		print "Missing Values: ";

		foreach ($enabled as $key=>$val) {
			if ($val) print "$key, ";
		}
	}

	exit;	
}

hprint ('<table>');
foreach ($queries as $id=>$q) { 
	//$url="?qid=$id$urldbhost$urldbname$urlenv";
	//$url="?q_name={$q['name']}$urldbhost$urldbname$urlenv";
	$url="?q_id=$id&q_name={$q['name']}";	
	if (isset($q['params'])) {
		foreach ($q['params'] as $key=>$value) {
			$url .= "&$key=$value";
		}
	}

	hprint ('
		<tr>
			<td>
				<a href="'.$url.'&csv=1">csv</a>
				<a href="'.$url.'">'.$q["name"].'</a><br />
			</td>
		</tr>
	');
}
hprint ('</table>');
 
function param($key, $default='') {
	if (isset($_REQUEST[$key])) {
		return $_REQUEST[$key];
	}
	return $default;
}

function hprint($str) {
	global $csv;
	if (!$csv) {
		print $str;
	}
}

function cprint($str) {
	global $csv;
	if ($csv) {
		print $str;
	}
}

function startsWith ($a, $b) {
	$length = min (strlen($a), strlen($b));
	return substr($a, 0, $length)==substr($b, 0, $length);
}

