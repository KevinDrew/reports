<?php
class 										Program extends VWObject {

	static function getTableName() { return 'program_db '; }

	function __construct($id) {
		parent::__construct($id);
	}

	function getJobs() {
		return Job::GetAllAsObjects($filter='program='. intval($this->id_number), $sort='');
	}

	function getJobsList() {  //select a few fields for displaying as a list
 		//id_number,startDateTime,site_arrive_time,site_id,nurse_ID,typeVaccine,price,gst_quoted,requiredVacc,usedVacc,comments,nurse2_ID,nurseStatus,nurse2Status,nurse1Start,nurse1Finish,nurse2Start,nurse2Finish,bookingType,finishDateTime,jobManager,nuse1KMS,nuse2KMS,staffVaccinated,fluBatchNumber,hepABRequired,hepABUsed,hepABBatchNumber,otherVaccines,staffID,staffID_title,staffID_Required,locationDetails,jobStatus,program,nurseBookingStatus,dateComments,sendShowBags,showBagsSent,promoRequired,promoSent,showBagsDelivery,accounts_db,ShowbagQTY,postersQTY,handoutsQTY,entered,adminPack,zone,dayVisit,adminPackStatus,adminPackReceivedDate,adminPackSentDate,nurseBookingSent,ProVaxComments,conformationContact,apotexConfirmation,confirnedDateTime,locationBookingDetails,nursecoordinator,ipad,closeJobNurseComments,confirmationLetterSent,key,KMSToCharge,jobCloseTime,nurse_accounts_db,apotexNurseConfirm,apotexNurseConfirmDate,westpacBranchID,westpacConfirmedDate,timeDisplay,mpb_product_list,site_reminder_email,westpac_Branch_Confirm_Email,westpac_Branch_Confirm_Email_TS,westpac_Branch_Confirm_Phone,westpac_Branch_Confirm_Phone_TS,apotexUID,vaccineType,sync,signature,siteContactAgree

		//$jobListFields = join(',',Job::getListFields());   don't think we can use this - too complicated
		//$q =  "SELECT $jobListFields from jobs_db j 

		$q =  "SELECT j.id_number, startDateTime,bookingType,site_id,s.city as siteName
			from jobs_db j
			left join sites_db s on s.id_number=j.site_id
			where j.id_number=". $this->id_number;
		$dbr = Factory::Get('dbread');
		$res = $dbr->query($q);
		$rows = array();
		while ($row = $res->fetch(2)) {
			$rows[] = $row;
		}
		return $rows;
	}

}