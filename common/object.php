<?php
/*
 * object.php
 *
 *
 * Author		: Kevin Drew
 * Create		: Sept 2013
 */
abstract class VWObject
{
	static function getTableName() { return 'object'; } 
	static function getJoins() { return array(); }
	static function getDefaultSort() { return 'name'; }  // set to something else if this table doesn't have the field called "name"
	static function getFilterEnabled() { return false; } // override to return true for tables that have a field called enabled
	static function getIdName() { return 'id_number'; }
	static function getFilter() { return '1'; }

	public $params=array();
	public static $table_name='';

	/**
	 * __construct 
	 * @param integer $id    an id freom the table, although can be 'max' which finds the last record created
	 * @param string  $table the name of the sql table
	 */
	public function __construct($id=0, $table='')
	{
		if (isset($_REQUEST['id']) && !$id) {
			$id = $_REQUEST['id'];
		}
		if($id){  // allow blank object to be created - this is useful for create new forms, e.g. using displaying  $obj->name  does not cause a warning, just a blank value

			$dbr = Factory::get('dbread');			

			if (!$table) $table = static::getTableName();

			$q = "SELECT *
			from `$table` ";
			if ($id == 'max') {
				$q .= "
				where ". static::getIdName() ."=(SELECT max(". static::getIdName() .") from `$table`)";
			} else {
				$q .= "
				where ". static::getIdName() ."=".intval($id);
			}

			$res = $dbr->query($q);

			if ($row = $res->fetch(2)) {

				foreach (array_keys($row) as $key) {
					$this->params[$key] = $row[$key];
				}

			} else {
				$this->params['id_number'] = 0;
				//print "not found ";
			}

		} else {
			$this->params['id_number'] = 0;
		}
	}

	/**
	 * GetAll Generic GetAll records
	 * @param string $filter        
	 * @param string $sort          
	 * @param array  $structure_keys array of fields, only up to 3 supported for the array. Return array indexed by this
	 */
	public static function GetAll($filter='1', $sort='', $structure_keys = array(), $limit=0, $offset=0, $table='')
	{		
		$dbr = Factory::get('dbread');				

		if (!$table) $table = static::getTableName();		
		$q = "SELECT * \n";

		$q .= "from ". static::getTableName() . "\n";

		$q .= 'where 1 ';
		if ($filter) { 
			$q .= " AND $filter";
		}		
		if (static::getFilterEnabled()) {
			$q .= ' AND enabled';
		}
		$q .= ' AND '. static::getFilter();

		$q .= " order by ";
		if ($sort) {
			$q .= "$sort,";
		}
		if (static::getDefaultSort()) {
			$q .= static::getDefaultSort() .",";
		}
		$q .= static::getIdName();

		if ($limit) {
			$q.= " limit  $limit ";
			if ($offset) { $q.= ", $offset "; }
		}

		$returnResult = array();
		$result = $dbr->query($q);
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$record = array();
			foreach (array_keys($row) as $key) {
				$record[$key] = $row[$key];
			}
			if ($structure_keys) { // empty array is false
				if (isset($structure_keys[2])) {
					$id0 = $row[ $structure_keys[0]];
					$id1 = $row[ $structure_keys[1]];
					//  optional, put the word unique after field name e.g. 'cc_field_id unique'
					if (preg_match('/(.*) unique/', $structure_keys[2], $matches)) {
						$id2 = $row[ $matches[1] ];
						$returnResult[$id0][$id1][$id2] = $record;  // 3 level unique
					} else {
						$id2 = $row[ $structure_keys[2]];
						$returnResult[$id0][$id1][$id2][] = $record;  // 3 level
					}					
				} else if (isset($structure_keys[1])) {					
					$id0 = $row[ $structure_keys[0]];
					$id1 = $row[ $structure_keys[1]];
					$returnResult[$id0][$id1][] = $record;  // 2 level
				} else {					
					$id0 = $row[ $structure_keys[0]];
					if ($structure_keys[0] == 'id') {
						$returnResult[ $id0 ] = $record; // 1 level - assume id is unique
					} else {
						$returnResult[ $id0 ][] = $record; // 1 level
					}
				}
			} else {
				$returnResult[] = $record;
			}
		}
		return $returnResult;
	}

	// used for a select list as in bluereport filter, get a list of all suburb/state for a html select
	public static function GetDistinctList($field) {
		$arr = array_keys(self::GetAll('', $field, $structure_keys = array($field)));

		if ($arr[0] == '') { // first element is usually a blank, so remove it.
			array_splice($arr, 0, 1);
		}
		return $arr;
	}

	/**
	 * GetHtmlSelectOnValues			e.g. usage: Location::GetHtmlSelectOnValues('suburb', 'VIC')
	 * @param [type] $field          database field name
	 * @param [type] $value          default selected value
	 * @param string $defaultMessage, e.g. set to '--All--' if its a filter
	 */
	public static function GetHtmlSelectOnValues($field='name', $value=0, $defaultMessage="--Please Select--") {
		$html = '<select name="'. $field .'" id="'. $field .'_select">
			<option value="">'.$defaultMessage.'</option>
			';

		foreach (self::GetDistinctList($field) as $opt) {
			$selected = $opt==$value ? ' selected="selected"' : '';
			$html .= '<option value="'. $opt .'"'. $selected .'>'. $opt .'</option>
			';
		}
		return $html . '</select>';
	}

	public static function GetHtmlSelectOnIdName($field='name', $value=0, $defaultMessage="--Please Select--", $filter=1) {
		$html = '<select name="'. static::getTableName() .'_id" id="'. $field .'_select">
			<option value="">'.$defaultMessage.'</option>
			';
		foreach (self::GetAll($filter) as $opt) {
			$selected = $opt['id']==$value ? ' selected="selected"' : '';
			$html .= '<option value="'. $opt['id'] .'"'. $selected .'>'. $opt[$field] .'</option>
			';
		}
		return $html . '</select>';
	}


	public static function GetAllAsObjects($filter='', $sort='', $structure_keys = array())
	{
		$dbr = Factory::get('dbread');				
		$q = "SELECT * \n";

		$q .= "from ". static::getTableName() . "\n";

		$q .= " where $filter";
		$q .= " order by ". 
			($sort ? 
			"$sort " : 
			static::getIdName())
		;

		$result = $dbr->query($q);
		$returnResult = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$class = get_called_class();
			$record = new $class($row['id_number']); 
			foreach (array_keys($row) as $key) {
				$record->params[$key] = $row[$key];
			}
			if ($structure_keys) { // empty array is false
				if (isset($structure_keys[1])) {
					$id0 = $row[ $structure_keys[0]];
					$id1 = $row[ $structure_keys[1]];
					$returnResult[$id0][$id1][] = $record;  // 2 level
				} else {
					$id0 = $row[ $structure_keys[0]];
					$returnResult[ $id0 ][] = $record; // 1 level
				}
			} else {
				$returnResult[] = $record;
			}
		}
		return $returnResult;
	}

	public static function GetCount($filter = 1) {
		$dbr=Factory::Get('dbread');

		$q = "SELECT count(*) as count ";
		$q .= "from ". static::getTableName() . "\n";
		$q .= "where $filter";
		$results = $dbr->query($q);
		$row = $results->fetch(2);
		return $row['count'];
	}

	/**
	 * GetFoundRows uses mysql found_rows() and relies on SQL_CALC_FOUND_ROWS being in the previous query
	 */
	public static function GetFoundRows() {
		$dbr=Factory::Get('dbread');
		$q = "SELECT found_rows() as fr";
		$results = $dbr->query($q);
		$row = $results->fetch(2);
		return $row['fr'];
	}

	/**
	 * GetAllByIds
	 * return array of all records in the table with table.id as the array index
	 */	
	public static function GetAllByIds() {
		return self::GetAll(null,null,array('id'));
	}

	public static function GetLastId() {
		$dbr=Factory::Get('dbread');

		$q = "SELECT max(". static::getIdName() .") from ". static::getTableName();
		$results = $dbr->query($q);

		$row = $results->fetch();
		return $row['max(id)'];
	}

	/**
	 * setPrevNextIds -for creating links for stepping by pages in the view/edit screen, even handles missing record gaps
	 * @param string $sortField   
	 * @param string $filter    
	 */
	public function setPrevNextIds($sortField='name', $filter='1')
	{
		$dbr=Factory::Get('dbread');

		$q = "SELECT ". static::getIdName() ." from ". $this->getTableName()." order by ".static::getIdName();
		$results = $dbr->query($q);

		$idFound = FALSE;
		$lastValue = FALSE;
		$nextValue = FALSE;

		while ($row = $results->fetch()) {
			if (intval($row[ static::getIdName() ]) == intval($this->{static::getIdName()})) {
				if ($row = $results->fetch()) {
					$nextValue = $row[ static::getIdName() ];
					break;
				}
			} else {
				$lastValue = $row[ static::getIdName() ];
			}
		}
		$this->params['next'] = $nextValue;
		$this->params['prev'] = $lastValue;
	}

	/**
	 * InsertOrUpdate creates a record if not existing, otherwise sets the value - e.g. RegionAttrib::InsertOrUpdate('region=254 and type_id=4', 'value',4 )
	 * @param  array $filter    (key=>value) pairs, we need it this way in case we need to do an insert
	 * @param  string $fieldName e.g. value
	 * @param  scalar $value     number or int or float
	 */
	public static function InsertOrUpdate($filter, $fieldName, $value) {
		$dbr = Factory::Get('dbread');
		//$dbw = Factory::Get('dbwrite');

		$filterStr = '';
		foreach ($filter as $filterFieldName => $filterValue) {			
			$filterStr .= ($filterStr) ? " AND " : '';
			$filterStr .= "$filterFieldName = '$filterValue'";
		}

		$q = "SELECT ". static::getIdName() .", $fieldName from ". static::getTableName() ." where $filterStr";

		$res = $dbr->query($q);
		$found =false;
		if ($row = $res->fetch(2)) {			  // should we throw an error if more than one record?
			$q = "UPDATE ". static::getTableName() ." set $fieldName=". $dbw->quote($value) ." where $filterStr";
			$dbr->query($q);
			return $row[ static::getIdName() ];

		} else {
			$q1 = "INSERT into ". static::getTableName() ." ($fieldName";
			$q2 = ") values('$value'";
			foreach ($filter as $filterFieldName => $filterValue) {			
				$q1 .= ",$filterFieldName";
				$q2 .= ','.$dbr->quote($filterValue);
			}
			$q = "$q1  $q2)";
			$dbw->query($q);
			return $dbw->lastInsertId();
		}

	}

	public function __get($name) {
		if(array_key_exists($name, $this->params)){
			return $this->params[$name];
		}
		return '';
	}

	public function __set($name, $value) {
		$this->params[$name] = $value;
	}

	public function __isset($name) 	{
		return isset($this->params[$name]);
	}

	public function __unset($name) 	{
		unset($this->params[$name]);
	}
}

?>