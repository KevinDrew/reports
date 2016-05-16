<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
	$(document).ready(function(){
		$("#option").change(function(){
			$("#div2").html('');
			$("#div1").html('<img src="img/google-loading-icon.gif"/>');
			$.ajax({url: "reportV1.php?date="+$(this).val(), success: function(result){
        		    $("#div1").html(result);
			}});    
		});
	});

	function getLevel2() {
		$("#div2").html('<img src="img/google-loading-icon.gif"/>');
		$.ajax({url: "reportV2.php?date="+$("#option").val(), success: function(result){
			$("#div2").html(result);
		}});
	}

	function getLevel3() {
		$("#div2").html('<img src="img/google-loading-icon.gif"/>');
		$.ajax({url: "reportNurse.php?date="+$("#option").val(), success: function(result){
			$("#div2").html(result);
		}});
	}
	
	function viewLog(aa) {
		//alert("GET_"+aa+"-2016.txt");
		var newWindow = window.open("/API/Sync/debug/"+"GET_"+aa+"-2016.txt", "new window", "width=800, height=1024");

		//write the data to the document of the newWindow
		//newWindow.document.write(data);
	}
</script>
</head>
<body>
Please pick a date:
<select id='option'>
	<option value="">Please select</option>
<?php
$dir    = '../API/Sync/debug';
$files1 = scandir($dir);
foreach($files1 as $f) {
	
	if(count(explode("report_",$f)) > 1) {
		//print "<!--option value=$f></option-->";
		$f = str_replace("report_","",$f);
		$f = str_replace(".txt","",$f);
		echo "<option value='".str_replace("-2016","",$f)."'>".date('d-M-Y',strtotime($f))."</option>";
	}
}
?>
	<!--option value="16-04">16-April 2016</option-->
	<!--option value="12-04">12-April 2016</option-->
</select>
<br/><br/>
<div id="div1"></div>
<br/>
<div id="div2"></div>
</body>
</html>
