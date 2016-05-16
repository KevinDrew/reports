<?php 
header( 'Content-type: text/html; charset=utf-8' );
define("_BNEXEC",1);

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

?>
<html>
	<body>

		<style>
			.highlight { color:red; font-weight:bold;}
		</style>

<?php 
	if (isset($_GET['class'])) {
		$dbr = Factory::get('dbread');
		$dbr->query("SET NAMES utf8");	

		$class = $_GET['class'];
		$obj = new $class($_GET['id']);		

		// if ($_GET['id'] == 'max') {
		// 	$prev = $obj->id_number -1;
		// 	$next = 1;
		// } else {
		// 	$prev = $_GET['id'] -1;
		// 	$next = $_GET['id'] +1;
		// }

		$obj->setPrevNextIds();

		print '
			<a href="?">List</a>&nbsp;&nbsp;
		';

		print backForwardLinks( $obj, __LINE__);
		print '<pre>';
		print replaceLinks(numberLines(print_r($obj,1)), $obj);

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
				print replaceLinks(print_r($obj->getReminders(),1), $obj);
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
				print '<span class="highlight">$crmCompany->getExtraData() </span>= ';
				print replaceLinks(tablify($obj->getContacts()), $obj);
			break;

			case 'CRMContact':
				//print '<span class="highlight">$crmCompany->getExtraData() </span>= ';
				//print replaceLinks(print_r($obj->getContacts(),1));
			break;

			case 'Site':
				print '<span class="highlight">$site->getJobs() </span>= ';
				print replaceLinks(replaceJobLinks(print_r($obj->getJobs(),1)),$obj);
			break;
		}

	} else {
		print '
			<a href="?class='. ($class="User") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="Job") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="BookingsDataHA") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="Program") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="Transaction") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="CRMCompany") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="CRMContact") .'&id=max">'. $class .'</a><br>
			<a href="?class='. ($class="Site") .'&id=max">'. $class .'</a><br>
		';
	}

	function replaceLinks($str, $obj) {
		foreach ($obj::getClassFields() as $field=>$class) {
			$str = preg_replace("/(\[$field\] => (\d+))/", "<a href=?class=$class&id=$2>$1</a>", $str);
		}
		return $str;
	}

	function replaceIdLinks($str, $class) {
		$str = preg_replace('/(\[id_number\] => (\d+))/', '<a href=?class='. $class .'&id=$2>$1</a>', $str);
		return $str;
	}

	function replaceJobLinks($str) {
		$str = preg_replace('/(\[id_number\] => (\d+))/', '<a href=?class=Job&id=$2>$1</a>', $str);
		return $str;
	}

	function numberLines($str) {
		global $obj;
		$res = '';
		$i=0;
		foreach (explode("\n", $str) as $line) {
			if (preg_match('/\s{12}\[(.*)\]/', $line, $matches))  {
				$res .= '<a href="index_field.php?table='. trim($obj->getTableName()) .'&field='.$matches[1] .'">i</a>';
				$res .= str_pad($i,12,' ', STR_PAD_LEFT). ' ' .trim($line) . "\n";
				$i++;
			} else {
				$res .= "$line\n";
			}
		}
		return $res;
	}
            
    function tablify($arr,$links) {
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
		$html = '<a name="'.$line.'""></a>';
		if ($obj->prev) {
			$html .= '<a href="?dbhost='. $dbhost.'&class='. $_GET['class'] .'&id='.  $obj->prev .'#'. $line .'">&laquo; Prev</a>&nbsp;&nbsp;';
		}
		if ($obj->next) {
			$html .= '<a href="?dbhost='. $dbhost.'&class='. $_GET['class'] .'&id='.  $obj->next .'#'. $line .'">Next &raquo;</a>';
		}
		return $html;
	}

?>
	</body>	
</html>
