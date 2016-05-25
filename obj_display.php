<?php 
header( 'Content-type: text/html; charset=utf-8' );

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("common/object.php");
include("common/factory.php");
include("common/m.user.php");
include("common/m.job.php");
include("common/m.bookingsDataHA.php");
include("common/m.program.php");
include("common/m.transaction.php");
include("common/m.crm-company.php");
include("common/m.crm-contact.php");
include("common/m.site.php");
include("common/m.booking-vaccine-type.php");
include("common/m.patient.php");
include("common/m.program-product.php");
include("common/m.booking-system-notification.php");
include("common/m.booking-system-email-templates.php");
include("common/m.nurse.php");
include("common/m.account.php");

$url_extras = '';
$db_params = array();
if (isset($_GET['dbhost'])) {
 	$url_extras = 'dbhost='. $_GET['dbhost'];	
	$db_params['host'] = $_GET['dbhost'];
}

$classFields = array(
	'company_id' => 'CRMCompany',
	'jobID'         => 'Job',
	'program'       => 'Program',
	'program_db' => 'Program',
	'program' => 'Program',
	'primaryContact' => 'CRMContact',
	'BillingContact' => 'CRMContact',
	'opsManager' => 'User',
	'operations' => 'User'
);

?>
<html>
	<body>

		<style>
			.highlight { color:red; font-weight:bold;}
		</style>

		<a href='misc_queries.php?<?php echo $url_extras; ?>'>misc_queries</a><br>
		<a href='?dbhost=localhost'>database localhost</a><br>
		<a href='?dbhost=192.168.3.1'>database host 192.168.3.1</a><br>
<?php 	

	if (isset($_GET['class'])) {
		$dbr = Factory::get('dbread', $db_params);
		$dbr->query("SET NAMES utf8");	

		$class = $_GET['class'];
		$obj = new $class($_GET['id']);		
		$objStr = replaceLinks(numberLines(print_r($obj,1)), $obj);

		print '<!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv -->';
		print '<!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv -->';
		print '<!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv --><!-- abcdefghijklmnopqrstuv -->';

		ob_flush();


		$obj->setPrevNextIds();
		print backForwardLinks($obj, '')."<br>";

		print "<a href='?'>List</a>&nbsp;&nbsp;";

		print '<pre>';
		print '$obj::getTableName() = '. $obj::getTableName() .'<br>';
		print $objStr;
		print backForwardLinks($obj, __LINE__)."\n";
	
		switch($class) {
			case 'User':
			break;

			case 'Job':
				print '<span class="highlight">$obj->getProgram() </span>= ';
				$programStr = print_r($obj->getProgram(),1);
				$programStr = replaceLinks($programStr, $obj);
				$programStr = replaceIdLinks($programStr, 'Program');
				print $programStr;
				print backForwardLinks($obj, __LINE__)."\n";

				print '<span class="highlight">$obj->getBookingsDataHA() </span>= ';

				$bkgArr = array();
				foreach($obj->getBookingsDataHA() as $booking) {
					$vacType = new Booking_typevaccine($booking->vaccineType);
					$patient = new Patient($booking->patientData);
					$bkgArr[] =  array(
						'id_number'			=> array('val' => $booking->id_number, 		'link' => 'obj=Booking&id_number='.$booking->id_number),
						'time'				=> array('val' => $booking->time, 			'obj'  => 'Unixtime'), // special obj type to convert timestamp
						'bookingType'		=> array('val' => $booking->bookingType),  
						'patientData'		=> array('val' => $booking->patientData,	'link' => 'obj=Patient&id_number'. $booking->patientData),
						'fname'				=> array('val' => $patient->fname),
						'sname'				=> array('val' => $patient->sname),
						'email'				=> array('val' => $booking->email),
						'key'				=> array('val' => $booking->key),
						'consent'			=> array('val' => $booking->consent),
						'attended'			=> array('val' => $booking->attended),
						'time'				=> array('val' => $booking->time, 			'obj'  => 'Unixtime'),
						'product'			=> array('val' => $booking->product),
						'vaccineType'		=> array('val' => $booking->vaccineType),
						'vaxCat'			=> array('val' => $vacType->vaxCat),
						'vaccineName'		=> array('val' => $vacType->vaccineName)

					);
				}

				print tablify($bkgArr);
				print backForwardLinks($obj, __LINE__)."\n";
			break;

			case 'BookingsDataHA':
				print '<span class="highlight">$booking->getReminders() </span>= ';
				print replaceLinks(print_r($obj->getReminders(),1));
			break;

			case 'Program':
				print '<span class="highlight">$program->getJobs() </span>= ';

				$jobArr=array();
				foreach ($obj->getJobs() as $job) {
					$site = 	new Site(	$job->site_id);
					$program = 	new Program($job->program_id);
					$jobArr[] =  array(
						'id_number'			=> array('val' => $job->id_number, 		'link' => 'Job&id='.$job->id_number),
						'startDateTime'		=> array('val' => $job->startDateTime, 	'obj' => 'Unixtime'), // special obj type to convert timestamp
						'finishDateTime'	=> array('val' => $job->finishDateTime, 'obj' => 'Unixtime'),
						'entered'			=> array('val' => $job->entered,		'obj' => 'Unixtime'),
						'site_id'			=> array('val' => $site->siteName.' '.$site->suburb,		'link' => 'obj=Site&id_number='. $site->id_number),
						'bookingType'		=> array('val' => $job->bookingType),  
						'requiredVacc'		=> array('val' => $job->requiredVacc),
						'jobStatus'			=> array('val' => $job->jobStatus)
					);
				}
				print tablify($jobArr);
				print backForwardLinks($obj, __LINE__);
			break;

			case 'CRMCompany':
				print '<span class="highlight">$crmCompany->getContacts() </span>= ';

				$conArr=array();
				foreach ($obj->getContacts() as $con) {
					$conArr[] =  array(
						'fname' => array('val' => $con->fname),
						'sname' => array('val' => $con->sname),
						'position' => array('val' => $con->position),
						'department' => array('val' => $con->department),
						'devision' => array('val' => $con->devision),
						'email' => array('val' => $con->email),
						'phone' => array('val' => $con->phone)
					);
				}
				print tablify($conArr);
			break;

			case 'CRMContact':
				print '<span class="highlight">$crmContact->getCompanies() </span>= ';
				print replaceLinks(print_r($obj->getCompanies(),1));
			break;

			case 'Site':
				print '<span class="highlight">$site->getJobs() </span>= ';
				//print replaceLinks(replaceJobLinks(print_r($obj->getJobs(),1)));

				$arr=array();
				foreach ($obj->getJobs() as $row) {
					$arr[] =  array(
						'id_number'			=> array('val' => $row->id_number, 		'link' => 'Job&id='.$row->id_number),
						'startDateTime'		=> array('val' => $row->startDateTime, 	'obj' => 'Unixtime'), // special obj type to convert timestamp
						'finishDateTime'	=> array('val' => $row->finishDateTime, 'obj' => 'Unixtime'),
						'entered'			=> array('val' => $row->entered,		'obj' => 'Unixtime'),
						'bookingType'		=> array('val' => $row->bookingType),  
						'requiredVacc'		=> array('val' => $row->requiredVacc),
						'jobStatus'			=> array('val' => $row->jobStatus)
					);
				}
				print tablify($arr);

			break;
		}

	} else {
		print '
			<a href="?'. $url_extras .'&class='. ($class="User") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="Job") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="BookingsDataHA") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="Program") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="Transaction") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="CRMCompany") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="CRMContact") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="Site") .'&id=max">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="ProgramProduct") .'&id=0001">'. $class .'</a><br>
			<a href="?'. $url_extras .'&class='. ($class="BookingSystemNotification") .'&id=max">'. $class .'</a><br> 
			<a href="?'. $url_extras .'&class='. ($class="BookingSystemEmailTemplates") .'&id=max">'. $class .'</a><br> 
			<a href="?'. $url_extras .'&class='. ($class="Nurse") .'&id=max">'. $class .'</a><br> 
			<a href="?'. $url_extras .'&class='. ($class="Account") .'&id=max">'. $class .'</a><br> 
			<a href="?'. $url_extras .'&class='. ($class="Transaction") .'&id=max">'. $class .'</a><br> 
		';
	}

	function replaceLinks($str) {
		$str = preg_replace('/(\[job_id\] => (\d*))/', '<a href=?class=Job&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[company_id\] => (\d*))/', '<a href=?class=CRMCompany&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[program_db\] => (\d*))/', '<a href=?class=Program&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[program\] => (\d*))/', '<a href=?class=Program&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[program_id\] => (\d*))/', '<a href=?class=Program&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[primaryContact\] => (\d*))/', '<a href=?class=CRMContact&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[BillingContact\] => (\d*))/', '<a href=?class=CRMContact&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[opsManager\] => (\d*))/', '<a href=?class=User&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[operations\] => (\d*))/', '<a href=?class=User&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[salesPerson\] => (\d*))/', '<a href=?class=User&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[sites_id\] => (\d*))/', '<a href=?class=Site&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[site_id\] => (\d*))/', '<a href=?class=Site&id=$2>$1</a>', $str);
		$str = preg_replace('/(\[programType\] => (\d*))/', '<a href=?class=ProgramProduct&id=$2>$1</a>', $str);

		// show the human readable times after a unix timestamp
		foreach (array('startDateTime', 'finishDateTime', 'entered', 'programEntered', 'timestamp', 'added', 'syncStamp','earlyDate','lateDate', 'time', 'deliveryStamp', 'insertStamp','sentTime') as $ts) {
			if (preg_match("/(\[$ts\] => (\d{10}))/", $str, $matches)) {
				$str = preg_replace("/(\[$ts\] => (\d{10}))/", $matches[1] .' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- '. date('r', $matches[2]), $str);
			}
		}
		return $str;
	}

	function replaceIdLinks($str, $class) {
		$str = preg_replace('/(\[id_number\] => (\d+))/', '<a href=?class='. $class .'&id=$2>$1</a>', $str);
		return $str;
	}

	function replaceBookingLinks($str) {
		$str = preg_replace('/(\[id_number\] => (\d*))/', '<a href=?class=BookingsDataHA&id=$2>$1</a>', $str);
		return $str;
	}

	function replaceJobLinks($str) {
		$str = preg_replace('/(\[id_number\] => (\d*))/', '<a href=?class=Job&id=$2>$1</a>', $str);
		return $str;
	}

	function replaceProgramLinks($str) {
		$str = preg_replace('/(\[id_number\] => (\d*))/', '<a href=?class=Program&id=$2>$1</a>', $str);
		return $str;
	}

	function numberLines($str) {
		global $obj;
		$res = '';
		$i=0;
		foreach (explode("\n", $str) as $line) {
			

			if (substr($line,0,8) == '        ' && preg_match('/\[(.*)\]/', $line, $matches)) {
				$res .= '<a href="index_field.php?table='. trim($obj->getTableName()) .'&field='.$matches[1] .'">i</a>';
				$res .= str_pad($i,12,' ', STR_PAD_LEFT). ' ' .trim($line) . "\n";
				$i++;
			} else {
				$res .= "$line\n";
			}
		}
		return $res;
	}
            
    function tablify($arr) {
    	$html = "<table border=1>\n";
    	for($row=0; $row<count($arr); $row++) {
   			$html .= "	<tr>";
    		if ($row==0) {
    			foreach (array_keys($arr[0]) as $key) {
    				$html .= "<th>$key</th>\n";
    			}
    			$html .= "	</tr>";
    			$html .= "	<tr>";
    		}
    		$col = 0;
			foreach (array_keys($arr[0]) as $key) {
				$val = $arr[$row][$key]['val'];
				if (isset($arr[$row][$key]['obj']) ) {
					
					switch ($arr[$row][$key]['obj']) {
						case 'Unixtime':

							if ($val > 1000000) {
								$val = date('d-M-y H:i:s',$val);
							}
						break;
						default:
							print 'dont know type '. $arr[$key]['obj'];

					}
				} elseif(isset($arr[$row][$key]['link'])) {
					$val = '<a href="obj_display.php?class='. $arr[$row][$key]['link'] .'">' . $val .'</a>'; 
				}			
				$html .= "		<td>$val</td>\n";
				$col++;
			}
    	}
    	return $html . '</table>';
    }

	function backForwardLinks($obj, $line) {
		global $dbhost;
		$html = '<a name="'.$line.'"></a>'."\n";
		if ($obj->prev) {
			$html .= '<a href="?dbhost='. $dbhost.'&class='. $_GET['class'] .'&id='.  $obj->prev .'#'. $line .'">&laquo; Prev</a>&nbsp;&nbsp;';
		} else {
			$html .= '&laquo; Prev';
		}
		if ($obj->next) {
			$html .= '<a href="?dbhost='. $dbhost.'&class='. $_GET['class'] .'&id='.  $obj->next .'#'. $line .'">Next &raquo;</a>';
		} else {
			$html .= 'Next &raquo;';
		}
		return $html;
	}

?>
	</body>	
</html>
