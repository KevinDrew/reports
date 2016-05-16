<?php 
include("common/factory.php");

header( 'Content-type: text/html; charset=utf-8' );
define("_BNEXEC",1);

		$dbr = Factory::get('dbread');
		$dbr->query("SET NAMES utf8");	

		$table = $_GET['table'];
		$field = $_GET['field'];

		$q = "SELECT `$field`, count(*) from `$table` group by `$field` order by count(*) desc, `$field`";
		$res = $dbr->query($q);

?>
<html>
	<body>
		<table border=1>
			<tr>
				<th>
					<?php echo $field; ?>
				</th>
				<th>
					Counts
				</th>
			</tr>
			<?php 
				while($row = $res->fetch(2)) {
					echo '
						<tr>
							<td>'. $row[$field] .'</td>
							<td>'. $row['count(*)'] .'</td>
						</tr>
					';
				}
			?>
	</body>	
</html>
