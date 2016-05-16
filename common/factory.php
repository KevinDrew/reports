<?php
/*
* factory.php
*
* 
* Author      : Al Kingsley
* Create      : 13th November 2009
* Last Update : 13th November 2009
*/

define ("config_file", '/etc/vitalityworks.conf');

class Factory
{
	static function Get($what, $params=null)
	{
		switch($what){
		case 'dbread': 		return Factory::_getdBRead($params);
		case 'dbwrite': 		return Factory::_getdBWrite($params);
		case 'mc': 				return Factory::_getMemcache($params);
		case 'session': 		return Factory::_getSession($params);
		case 'sessionobj': 	return Factory::_getSessionObj($params);
		case 'user': 			return Factory::_getUser($params);
		}
		return null;
	}
	static function _getdBRead($params = null)
	{	
		$config = parse_ini_file(config_file, true);
		$host = isset($params['host']) ? $params['host'] : $config['dbread']['host'];
		$name = isset($params['name']) ? $params['name'] : $config['dbread']['name'];

		$dsn = 'mysql:dbname='.$name.';host='.$host.';';//unix_socket=/var/lib/mysql/mysql.sock';
		static $instance;

		if(!is_object($instance) || isset($params['reset'])){
			try{				
				$instance = new bnPDOreadOnly($dsn, $config['dbread']['user'], $config['dbread']['pass'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			}
			catch(PDOException $e){
				print "dbr Error!: " . $e->getMessage() . "<br/>";
				print "blah!: " . $e->errorInfo . "<br/>";
				print "trace!: " . $e->getTraceAsString() . "<br/>";
				echo 'mysql:host='.$db['shost'].';dbname='.$db['name']." ". $db['suser']." ". $db['spass'];
				die();
			}
			//$instance->query("set names 'utf8'");
			$instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
		return $instance;
	}

	static function _getdBWrite($params = null)
	{
		$config = parse_ini_file(config_file, true);
		static $instance;
		$host = isset($params['host']) ? $params['host'] : $config['dbwrite']['host'];
		$name = isset($params['name']) ? $params['name'] : $config['dbwrite']['name'];

		if(!is_object($instance)){
			try{
				if($params<>null){
					$options = array(PDO::ATTR_AUTOCOMMIT=>FALSE,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
				}
				else{
					$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
				}
				
				$instance = new bnPDOwrite('mysql:host='.$host.';dbname='.$name, $config['dbwrite']['user'], $config['dbwrite']['pass'],$options);
			}
			catch(PDOException $e){
				print "dbw Error!: " . $e->getMessage() . "<br/>";
				die();
			}
			//$instance->query("set names 'utf8'");
			$instance->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		}
		return $instance;
	}

	static function _getMemcache($params = null)
	{
		$config = parse_ini_file(config_file, true);
		$host = isset($params['host']) ? $params['host'] : $config['memcache']['host'];
		
		static $instance;
		if(!isset($instance[$host]) || !is_object($instance[$host])){ 
			try{
				$instance[$host] =  new Memcache();
				if(!$instance[$host]) return null; 
				
				if(!$instance[$host]->connect($host,$config['memcache']['port'])) return null; 
			}
			catch(Exception $e){
				print "Memcache Error!<br/>";
				die();
			}
		}
		return $instance[$host];
	}

	static function _getUser($param = 'user')
	{
		
		static $instance;
		if($instance instanceof User){
		//if(!is_object($instance)){
			try{
				$s=$this->_getSession(null);
				if($s){
					$u=$s->read();
					if(isset($u[$param])){
						return $u[$param]; 
					}
				}
			}
			catch(Exception $e){
				print "Session Error!<br/>";
				die();
			}
		}
		return $instance->read();
	}
	static function _getSession($param)
	{
		static $instance;
		
		if(is_array($instance)){
			return $instance;
		}
		
		//$s=$this->_getSessionObj();
		$s=Factory::Get('sessionobj');
		if(!$s){
			return null;
		}
		$instance=$s->get();
		return $instance;
	}
}

class bnPDOreadOnly extends bnPDO
{
	public function query($q) {
		$q2 = trim(strtoupper($q));
		if (substr($q2,0,6) == 'UPDATE' || substr($q2,0,6) == 'INSERT' || substr($q2,0,6) == 'DELETE') {
			1/0;exit;
		}
		return parent::query($q);
	}
}

class bnPDOwrite extends bnPDO
{
	static $lastInsertId;

	public function query($q) {
		$res = parent::query($q);
		self::$lastInsertId = parent::lastInsertId();

		$this->logQuery($q);
		return $res;
	}

	public function lastInsertId($seqname = NULL) {
		return self::$lastInsertId;
	}

	public function logQuery($q) {
		$q = strtolower($q);
		if (preg_match('/where\W+id\W*=\W*(\d+)/m', $q, $matches)) {
			$target_record_id = $matches[1];
		} else {
			$target_record_id = self::$lastInsertId;
		}

		$auditaction = array(
			'created' 		=> 1,
			'edited' 		=> 2,
			'deleted' 		=> 3,
			'disabled' 		=> 4,
			'enabled' 		=> 5,
			'soft delete' 	=> 6
		);

		$audittarget = array(
			'bgnet_users' 			=> 1, 
			'client' 				=> 2, 
			'contact' 				=> 3, 
			'region' 				=> 4,
			'regionattribute' 	=> 5,
			'unit_entity' 			=> 6,
			'unit_entitycoll'		=> 7
		);

		$q = trim($q);
		$actionStr = substr($q,0,6);
		switch($actionStr) {
			case 'update':
				preg_match('/^update\W+(\w+)/', $q, $matches);
				$table = $matches[1];

				if (preg_match('/enabled\W+=\W+(\d)/', $q, $matches)) {					
					$action_id = $matches[1] ? $auditaction['enabled'] : $auditaction['disabled'] ;
				} else {
					$action_id = $auditaction['edited'];
				}

				break;
			case 'insert':
				$p = preg_match('/^insert\W+into\W+(\w+)/', $q, $matches);
				$table = $matches[1];
				$action_id = $auditaction['created'];
				break;
			case 'delete':
				preg_match('/^delete\W+from\W+(\w+)/', $q, $matches);
				$table = $matches[1];
				$action_id = $auditaction['deleted'];
				break;
			case 'set na':
			case 'select':
			case 'show s':
				return;				
			default:
				print "Couldn't recognise <BR>'$q'";
				1/0;
				exit;
		}

		if (isset(		 $audittarget[$table])) {
			$target_id = $audittarget[$table];

			$q = "INSERT into auditlog (user_id,actionat,action_id, target_id, target_record_id) 
					VALUES (". User::getUserId() .",now(),$action_id,$target_id,$target_record_id)";
			parent::query($q);			
		}
	}
}


class bnPDO extends PDO
{
	//this is annoying that I had to create another function for this, 
	//but can not add more params because method signatures must match 
	public function query_handle_errors($q, $handled_errors=array())
	{
		$logging = isset($params['logging']) ? $params['logging'] : 0;

		$config = parse_ini_file(config_file, true);
		$logging = isset($config['dbread']['logging']) ? $config['dbread']['logging'] : 0;

		if ($logging) {			
			$last_time = file_get_contents('/var/log/cfcl/last_query', time());
		
			file_put_contents('/var/log/cfcl/db_log', "$q\n". (time()-$last_time) ."-----------------------------------\n", FILE_APPEND);
			file_put_contents('/var/log/cfcl/last_query', time());
		}

		$result = parent::query($q);
		
		if (
			parent::errorCode() > 0 && 
			$q != 'SET CHARACTER SET \'utf8\'' &&
			!in_array(parent::errorCode(), $handled_errors)
		) { 
			print "<pre>Error: ".parent::errorCode()." $q\n";
			var_dump(parent::errorInfo());
			1/0;
			exit;
		}
		return $result;
	}	

	public function query($q)
	{
		$debug=false;
		if ($debug && (substr($q,0,6) == 'UPDATE' || substr($q,0,6) == 'INSERT' || substr($q,0,6) == 'DELETE')) {

			$_SESSION['row'] += 1;		
			$color = $_SESSION['row'] % 2 ? '#ddd' : '#fff';

			$result = $this->query_handle_errors($q);
			
			$db = debug_backtrace();
			if (preg_match('/(httpdocs|bluecommon\/trunk)\/(.*$)/', $db[0]['file'], $matches)) {				
				$file = $matches[2]; 
			} else {
				$file = $db[0]['file'];
			}

			if($_SESSION['row'] == 1) print '
				<table border="1" cellpadding="1">
			';
			print '
				<tr style="background-color:'.$color.'">
					<td style="color:purple">
						'. count($db) .'
					</td>
					<td style="color:blue">
						'.$file.'
					</td>
					<td style="color:green">
						'.$db[0]['line'] .'
					</td>
					<td style="color:red">
						'.$result->rowCount().'
  					</td>
					<td>
						'.$q.'
					</td>
				</tr>
			';
		} else {
			$result = $this->query_handle_errors($q);
		}
		return $result;
	}	
	
}

?>
