<?php 
header( 'Content-type: text/html; charset=utf-8' );

$csv = !empty($_REQUEST['csv']);

//if (substr($_SERVER['REMOTE_ADDR'], 0, 6) != '192.168.' && $_SERVER['REMOTE_ADDR'] != '127.0.0.1')  exit;


hprint ("<a href='obj_display.php'>obj_display</a><br>");
hprint ("<a href='?dbhost=localhost'>localhost</a><br>");
hprint ("<a href='?dbhost=192.168.3.1'>192.168.3.1</a><br>");

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
		'name' => 'CRM Companies',
		'query' =>	
			'SELECT 
	 				id_number,
	 				general_table,
	 				added,
	 				company_name,
	 				industry,
	 				website,
	 				`key`,
	 				status,
	 				note,
	 				source

				from crm_company
				order by company_name
		',
	),
	__LINE__ => array( 
		'name' => 'Jobs and their companies',
		'query' =>	
			'SELECT j.id_number as job_number, bookingType, from_unixtime(startDateTime), p.year, p.id_number as program_id, p.programStatus, p.agent, c.id_number, c.company_name, t.id_number as trans
				from jobs_db j
				left join program_db p on j.program=p.id_number
				left join crm_company c on p.company_id=c.id_number
				left join transactions_db t on t.job=j.id_number
			where year=2016
			order by bookingType, p.programStatus, p.agent
		',
	),
	__LINE__ => array( 
		'name' => 'Jobs -confirmed and their companies by state',
		'query' =>	
			"SELECT j.id_number as job_number, from_unixtime(startDateTime), p.year, p.id_number as program_id, p.programStatus, c.id_number, c.company_name, s.state, j.requiredVacc
				from jobs_db j
				left join program_db p on j.program=p.id_number
				left join crm_company c on p.company_id=c.id_number
				left join sites_db s on j.site_id=s.id_number
			where year=2016 and j.bookingType='confirmed' and startDateTime > 1463702400 and s.state != 'NSW'
			order by s.state, c.company_name, p.programStatus, p.agent
		",
	),
	__LINE__ => array( 
		'name' => 'Jobs -confirmed and their companies by state - May-17',
		'query' =>	
			"SELECT j.id_number as job_number, j.bookingType, from_unixtime(startDateTime), p.year, p.id_number as program_id, p.programStatus, c.id_number, c.company_name, s.state, j.requiredVacc, p.vaxType
				from jobs_db j
				left join program_db p on j.program=p.id_number
				left join crm_company c on p.company_id=c.id_number
				left join sites_db s on j.site_id=s.id_number
			where year=2016 and (j.bookingType='confirmed' or j.bookingType='complete') and startDateTime > 1463414400 and (p.vaxType='Quadrivalent' or p.vaxType='both')
			order by s.state, c.company_name, p.programStatus, p.agent
		",
	),
	__LINE__ => array( 
		'name' => 'CAP Users',
		'query' =>	
			'SELECT j.id_number, p.year, p.id_number,c.id_number, c.company_name
				from admin_users
				order by type,emailAddress			
		',
		'suppress_rowcount'=>false
	),
	__LINE__ => array( 
		'name' => 'General_Table',  
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table 
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Clients',  
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where client = "y" 
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Nurses',  
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where nurse="y" 
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - staff',  
		'query' =>	
			'SELECT  client,nurse,distribution,manufacturer,supplier,support,staff,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,agent,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where staff="y" 	
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array( 
		'name' => 'General_Table - Agents',  
		'query' =>	
			'SELECT agent,Company_Name,Account_number,address,city,Postcode,State,Country,Telephone_General,Fax_General,Title,First_Name,Sir_Name,Position,Phone_Number,Fax_Number,mobile_Number,Email_Address,First_Contact,Last_Mod,bustype,sales_person,staff_number,Fax_Comments,letter_first,letter_second,end_row,client,nurse,distribution,manufacturer,supplier,support,staff,salesStatus,username,password,sold,contact2007,salesStatus2,showAccounts,HECS,TFF,Gov,taxFileNumber,contact2008,payRate,taxRate,nurseBag2008,bankAccountNumber,BSB,superFundName,memberNumber,SPIN,SFN,dob,bank,healthCheckPayRate,nurseImmuniser,nurseHealthAssessor,subDomain,tesPanflu,serviceProvider,lat,longi,reg,Next_of_Kin,Next_Kin_Relationship,Next_Kin_Number,out_of_hours,Number_of_hours_per_week,Willing_to_travel,Availability,Uniform_Style,Shirt_Size,Preferred_Name,Agree_to_uniform_conditions,SuperMemberNoId,bankAccountName,deliveryNoteNurse,`OHS-IMPORT`,`OHS-ENTITYID`,webAddress,import_key,typeOfCompany,employeesNO,clientID,APHRA_exp,ImmunisationCert_exp,CRP_exp,address_mailing,city_mailing,Postcode_mailing,State_mailing,Country_mailing,address_delivery,city_delivery,Postcode_delivery,State_delivery,Country_delivery,carMake,carModel,dedicated_Fridge,fridge_Years,ipads_Issue,carpark_CardNumber,nusre_History,driversLicenceNumber,SuperFundABN,SuperFundBSB,SuperFundAccountNumber,SuperFundESA,SuperFundEmailAddress
				from general_table where agent="y"',
		'params' => array(			
		)
	),

	__LINE__ => array( 
		'name' => 'Sites',  
		'query' =>	'SELECT
				id_number,customer_id,fname,sname,address1,address2,city,state,postcode,mobile,phone,siteName,email,salutation,position,staff,password,streetNumber,streetName,locationWithin,buildingName,Active,siteVisits,earlyDate,lateDate,zone,visitorder,exclusions,clientnotes,startTime,finishTime,level,metroStatus,geoCodeLat,geoCodeLong,WestpacBranchPropertyNumber,confirmed2014,`key`,KMStoNurse,apotexLetterTimeStamp,apotexLetterGeneratedTimeStamp,program_db,pref_date_1,pref_time_1,pref_date_2,pref_time_2,pref_date_3,pref_time_3
				from sites_db',
		'params' => array(			
		)
	),

	__LINE__ => array( 
		'name' => 'Bookings for a job',  
		'query' =>	
			'SELECT DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%l:%i:%s%p") as j__startTime, b.id_number,b.status as b__status,bt.vaxCat, p.fname, p.sname, j.jobstatus as job__jobstatus,consent,attended,patientData,
				DATE_FORMAT(FROM_UNIXTIME(b.time), "%l:%i:%s%p") as bookedTime,
				mod(b.time-j.startDateTime, 115) as btime_mod,
				b.bookingType, time,
				product,b.general_table,b.program,consentCard,medicareCharge,SMSReminder,emailReminders,SMS,b.email,iCal,iCalType,b.signature,b.`key`,reportComplete,sites_id,dateType,facilitator,progress,billable,cost,availability,och_bookingsData,facilitatorType,timeEnd,status,added,emailReminderSent,SMSReminderSent,postVaxSent,syncStamp,b.bookingType,b.vaccineType as b_vaccineType,j.vaccineType as j_vaccineType, note 
				from bookingsDataHA b
				left join jobs_db j on b.job_id=j.id_number
				left join patientDataHA p on b.patientData=p.id_number
				left join booking_typeVaccine bt on b.vaccineType=bt.id_number
				where b.job_id = '. ($job_id = param('job_id', $default='20642')) .'
				and b.status!="deleted"
				order by if(b.status="confirmed",1,0) desc,consent,attended, `time`',
		'params' => array(
			'job_id' => $job_id
		)
	),

	__LINE__ => array( 
		'name' => 'Bookings for a job - sort by name',  
		'query' =>	
			'SELECT DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%l:%i:%s%p") as j__startTime, b.id_number,b.status as b__status,bt.vaxCat, p.fname, p.sname, j.jobstatus as job__jobstatus,consent,attended,patientData,
				DATE_FORMAT(FROM_UNIXTIME(b.time), "%l:%i:%s%p") as bookedTime,
				mod(b.time-j.startDateTime, 115) as btime_mod,
				b.bookingType, time,
				product,b.general_table,b.program,consentCard,medicareCharge,SMSReminder,emailReminders,SMS,b.email,iCal,iCalType,b.signature,b.`key`,reportComplete,sites_id,dateType,facilitator,progress,billable,cost,availability,och_bookingsData,facilitatorType,timeEnd,status,added,emailReminderSent,SMSReminderSent,postVaxSent,syncStamp,b.bookingType,b.vaccineType as b_vaccineType,j.vaccineType as j_vaccineType, note 
				from bookingsDataHA b
				left join jobs_db j on b.job_id=j.id_number
				left join patientDataHA p on b.patientData=p.id_number
				left join booking_typeVaccine bt on b.vaccineType=bt.id_number
				where b.job_id = '. ($job_id = param('job_id', $default='20642')) .'
				and b.status!="deleted"
				order by p.fname, p.sname,if(b.status="confirmed",1,0) desc,consent,attended, `time`',
		'params' => array(
			'job_id' => $job_id
		)
	),

	__LINE__ => array( 
		'name' => 'Bookings for a job (include deleted)',  
		'query' =>	
			'SELECT DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%l:%i:%s%p") as j__startTime, b.id_number,b.status as b__status,bt.vaxCat, p.fname, p.sname, j.jobstatus as job__jobstatus,consent,attended,patientData,
				DATE_FORMAT(FROM_UNIXTIME(b.time), "%l:%i:%s%p") as bookedTime,
				mod(b.time-j.startDateTime, 115) as btime_mod,
				b.bookingType, time,
				product,b.general_table,b.program,consentCard,medicareCharge,SMSReminder,emailReminders,SMS,b.email,iCal,iCalType,b.signature,b.`key`,reportComplete,sites_id,dateType,facilitator,progress,billable,cost,availability,och_bookingsData,facilitatorType,timeEnd,status,added,emailReminderSent,SMSReminderSent,postVaxSent,syncStamp,b.bookingType,b.vaccineType as b_vaccineType,j.vaccineType as j_vaccineType, note 
				from bookingsDataHA b
				left join jobs_db j on b.job_id=j.id_number
				left join patientDataHA p on b.patientData=p.id_number
				left join booking_typeVaccine bt on b.vaccineType=bt.id_number
				where b.job_id = '. ($job_id = param('job_id', $default='20642')) .'
				order by if(b.status="confirmed",1,0) desc,consent,attended, `time`',
		'params' => array(
			'job_id' => $job_id
		)
	),

	__LINE__ => array( 
		'name' => 'Program Coordinators with Upcoming Bookings',  
		'query' => 'SELECT 
				j.program,
				j.id_number as job__id_number, 
				j.bookingType, 
				c.company_name, 
				s.fname,
				s.sname,
				concat(s.streetNumber," ",s.streetName," ",s.city," ",s.state) as address,
				concat(con.fname, " ", con.sname) as contact,
				con.email
				FROM jobs_db j
				join sites_db s on s.id_number=j.site_id
				join program_db p on p.id_number=j.program
				join crm_company c on p.company_id=c.id_number
				join crm_contact con on p.primaryContact=con.id_number
				where j.startDateTime > unix_timestamp(now()+43600) and j.startDateTime < (unix_timestamp(now()) + (86400 * 7)+43600) and j.bookingType="confirmed"
				group by p.id_number
				order by c.company_name
		'
	),

	__LINE__ => array( 
		'name' => 'Upcoming Bookings For A Program',  
		'query' =>			
			'SELECT 
				j.id_number as job__id_number, 
				DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%W %M %Y") as bookedDate,
				DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%l:%i%p") as bookedTime,
				DATE_FORMAT(FROM_UNIXTIME(j.finishDateTime), "%l:%i%p") as finishTime,
				p.id_number as program__id_number, 
				p.programType,
				c.company_name, 
				j.site_id,
				concat(s.address1," ",s.address2," ",s.city," ",s.state) as address,
				concat(con.fname, " ", con.sname) as contact,
				con.email,
				(select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.status!="deleted") as count_bookings,
				ceil((j.finishDateTime - j.startDateTime)/115) - (select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.patientData=6) as possible_bookings,
				ceil((j.finishDateTime - j.startDateTime)/115) as all_spots,
				(select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.patientData=6) as nurse_breaks

				FROM jobs_db j
				join sites_db s on s.id_number=j.site_id
				join program_db p on p.id_number=j.program
				join crm_company c on p.company_id=c.id_number
				join crm_contact con on p.primaryContact=con.id_number
				where j.startDateTime > unix_timestamp(now()) and j.startDateTime < (unix_timestamp(now()) + (86400 * 7)) and j.bookingType="confirmed" and p.id_number='. ($job_id = param('program_id', $default='3905')) .'
				order by c.company_name
				',
		'params' => array(
			'program_id' => $default
		)
	),
	__LINE__ => array( 
		'name' => 'Site Coordinators with Upcoming Bookings',  
		'query' =>			
			'SELECT 
				j.site_id,
				s.siteName,
				(select count(*) from bookingsDataHA where bookingsDataHA.job_id=j.id_number) as bookcount,
				j.program,
				p.year,
				j.id_number as job__id_number, 
				j.bookingType, 
				c.company_name, 
				s.fname,
				s.sname,
				concat(s.streetNumber," ",s.streetName," ",s.city," ",s.state) as address,
				concat(s.fname, " ", s.sname) as contact,
				s.email
				FROM jobs_db j
				left join sites_db s on s.id_number=j.site_id
				left join program_db p on p.id_number=j.program
				left join crm_company c on p.company_id=c.id_number
				left join crm_contact con on p.primaryContact=con.id_number				
				where j.startDateTime > (unix_timestamp(now()+43200)) and j.startDateTime < (unix_timestamp(now()) + (86400 * 2) + 43200) and j.bookingType="confirmed" and 
        		(p.programStatus = "Open" OR p.programStatus = "Active" OR p.programStatus = "New")
        		and p.year=2016
				group by j.site_id
				',
		'params' => array(			
		)
	),

	__LINE__ => array( 
		'name' => 'Upcoming Bookings For A Site',  
		'query' =>			
			'SELECT 
				j.id_number as job__id_number, 
				DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%W %M %Y") as bookedDate,
				DATE_FORMAT(FROM_UNIXTIME(j.startDateTime), "%l:%i%p")  as bookedTime,
				DATE_FORMAT(FROM_UNIXTIME(j.finishDateTime), "%l:%i%p") as finishTime,
				p.id_number as program__id_number, 
				p.programType,
				c.company_name, 
				j.site_id,
				concat(s.address1," ",s.address2," ",s.city," ",s.state) as address,
				concat(con.fname, " ", con.sname) as contact,
				con.email,
				(select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.status!="deleted") as count_bookings,
				ceil((j.finishDateTime - j.startDateTime)/115) - (select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.patientData=6) as possible_bookings,
				ceil((j.finishDateTime - j.startDateTime)/115) as all_spots,
				(select count(*) from bookingsDataHA b where b.job_id=j.id_number and b.patientData=6) as nurse_breaks

				FROM jobs_db j
				join sites_db s on s.id_number=j.site_id
				join program_db p on p.id_number=j.program
				join crm_company c on p.company_id=c.id_number
				join crm_contact con on p.primaryContact=con.id_number
				where j.startDateTime > unix_timestamp(now()) and j.startDateTime < (unix_timestamp(now()) + (86400 * 7)) and j.bookingType="confirmed" and j.site_id='. ($job_id = param('program_id', $default='9557')) .'
				order by c.company_name
				',
		'params' => array(
			'site_id' => $default
		)
	),

	__LINE__ => array( 
		'name' => 'Find by email CRM Contacts',  
		'query' =>			
			'SELECT con.fname, con.sname, con.email, c.company_name
			from crm_contact con 
			left join program_db p on p.primaryContact=con.id_number
			left join crm_company c on p.company_id=c.id_number
			where email like "%'. param('email',$default='lau') .'%"

				',
		'params' => array(
			'email' => $default
		)
	),

	__LINE__ => array( 
		'name' => 'Customer Invoicing',  
		'query' => "
			SELECT
			t.id_number as t_id,
			a.id_number as a_id,
			j.id_number as j,
			p.id_number as p,
			'A NO EMPLOYEE PAYMENT,NONE', 
			'',
			concat('Vax - ', p.vaxType), 
			s.siteName, 
			concat(s.streetNumber,' ',s.streetName,' ',s.city) as site_address, 
			date_format(from_unixtime(j.startDateTime), '%e-%c-%Y') as date,
			p.billingType,

			j.usedVacc,
			p.siteFeeMetro,
			p.siteFeeRegional,
			if (  ((j.finishDateTime-j.startDateTime)/3600) > 3, 3, ((j.finishDateTime-j.startDateTime)/3600)) as parking_qty

			from transactions_db t
			left join accounts_db a on (a.id_number = t.invoice_id)
			left join jobs_db j on (t.job=j.id_number)
			left join booking_typeVaccine btv on j.typeVaccine=btv.id_number
			left join program_db p on (p.id_number=j.program)
			left join sites_db s on (j.site_id=s.id_number)
			left join crm_company c on (a.customer_id=c.id_number)

			where p.year=2016
			and a.type='Invoice'
			and !t.exported
			group by t.id_number
		"
	),

	__LINE__ => array( 
		'name' => 'Customer Invoicing2',  
		'query' => "
			SELECT
				t.id_number as t_id, 
				a.id_number as a_id,
				from_unixtime(a.date_created) as created,
				t.job as t_j,
				j.id_number as j_id
			from transactions_db t
				left join accounts_db a on (a.id_number = t.invoice_id)
				left join jobs_db j on (t.job=j.id_number)
				left join program_db p on (p.id_number=j.program)

			where t.id_number 
				and a.type='Invoice'
			group by t.id_number
		"
	),
	__LINE__ => array(
		'name' => 'Clients To Invoice',
		'query' => "
			SELECT 
				program_db.agent, 
				program_db.vaxType,
				crm_company.company_name
			FROM program_db, crm_company 

			WHERE 
				crm_company.id_number = program_db.company_id 
					and 
				program_db.programStatus = 'Open' 
					and 
				program_db.year = '2016' 

			ORDER BY program_db.agent, crm_company.company_name
		"
	),
	__LINE__ => array(
		'name' => 'Programs Billing Type',
		'query' => "SELECT p.id_number, company_id, c.company_name, billingType
			FROM program_db p
			LEFT JOIN crm_company c ON c.id_number = p.company_id
			WHERE  and b.year=2016
		"
	),
	__LINE__ => array(
		'name' => 'Companies with Jobs To Invoice  (old-bms/dashboard/flu/programJobsToInvoice)',
		'query' => 'SELECT p.id_number as p__id_number, p.programName, c.id_number as c__id_number, c.company_name, p.programStatus, agent.Company_Name
			FROM `program_db` p
			left join crm_company c on c.id_number=p.company_id
			left join general_table agent on agent.Account_number=p.agent
			where p.year=2016 and p.programStatus = "Open"
			order by p.agent, c.company_name'
	),
	__LINE__ => array(
		'name' => 'Jobs to Invoice for a Program (old-bms/dashboard/flu/programInvoice-Home) program='.($default=2938),
		'query' => "SELECT jobs_db.id_number, 
						from_unixtime(jobs_db.startDateTime),
						jobs_db.jobStatus,				
						jobs_db.accounts_db,		
						(SELECT count(*) FROM bookingsDataHA where job_id = jobs_db.id_number and patientData != '000006' and (time != 'Manual Listing/Walkin' and  bookingType!='Walkin') and status = 'confirmed') as walkins,
						(SELECT count(*) FROM bookingsDataHA where job_id = jobs_db.id_number and patientData != '000006' and (time  = 'Manual Listing/Walkin' or   bookingType= 'Walkin') and status = 'confirmed') as bookings
					FROM jobs_db, sites_db 
					WHERE jobs_db.site_id = sites_db.id_number 
						and jobs_db.bookingType != 'delete' 
						and jobs_db.startDateTime < now()
						and jobs_db.jobStatus != 'Closed'						
						and jobs_db.program = ". param('program', $default),
		'params' => array(
			'program' => $default
		)
	),
	__LINE__ => array(
		'name' => 'Jobs with more than one vax',
		'query' => "SELECT
				p.company_id as p__company_id,	
				c.company_name,
				p.id_number as program_id, 
				job_id, 
				j.jobStatus,
				count(DISTINCT t.vaxCat),
				count(b.id_number) as bookings_count

			from bookingsDataHA b
			left join `booking_typeVaccine` t on b.vaccineType=t.id_number
			left join `jobs_db` j on b.job_id=j.id_number
			left join program_db p on j.program=p.id_number
			left join crm_company c on p.company_id=c.id_number
			where b.status != 'deleted' and b.patientData !=6 and consent='y'
				group by job_id
			having count(DISTINCT t.vaxCat) > 1"
	),
	__LINE__ => array( 
		'name' => 'Nurses - with Inventory',  
		'query' =>	
			'SELECT 
				name, 
				surname, 
				gt.Company_Name,
				gt.Account_number,
				gt.address,
				gt.city,
				gt.Postcode,
				gt.State,
				(select quantity from nurse_inventory where user.id_number=user_id and vaccineType="QIV") as Quadravalent,
				(select quantity from nurse_inventory where user.id_number=user_id and vaccineType="TIV") as Trivalent,
				(select updated from nurse_inventory where user.id_number=user_id and vaccineType="QIV") as updated
				from user 
				left join general_table gt on gt.Account_number=user.general_table
				where nurseVaccinator="y" 
				order by gt.Company_Name,gt.Postcode
		',
		'suppress_rowcount'=>true
	),
	__LINE__ => array(
		'name' => 'duplicate bookings',
		'query' => "SELECT 
				b.job_id, p.fname, p.sname, count(*)
				from bookingsDataHA b 
				join jobs_db j on j.id_number=b.job_id
				left join patientDataHA p on b.patientData=p.id_number
				where patientData != 6
				and status!='deleted'
				group by b.job_id, p.fname, p.sname having count(*) >1
		"
	),
);


$field_links = array(
	'unit_id' => 					'caps'. $env
);

$last_group_by = '';
$gb = '';

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

	$res = $dbr->query('SET time_zone = "Australia/Melbourne"');
	$res = $dbr->query($queries[$_REQUEST['qid']]['query']);
	hprint ('count = '.$res->rowCount());
	hprint ('<table border="1">'."\n");	 

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
	
	exit;	
}

hprint ('<table>');
foreach ($queries as $id=>$q) { 
	//$url="?qid=$id$urldbhost$urldbname$urlenv";
	//$url="?q_name={$q['name']}$urldbhost$urldbname$urlenv";
	$url="&q_id=$id&q_name={$q['name']}";	
	if (isset($q['params'])) {
		foreach ($q['params'] as $key=>$value) {
			$url .= "&$key=$value";
		}
	}

	hprint ('
		<tr>
			<td>
				<a href="?'.$urldbhost . $url. '&csv=1">csv</a>
				<a href="?'.$urldbhost . $url.'">'.$q["name"].'</a><br />
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

