<?php
/***MysqliDbClass**@categoryDatabaseAccess*@packageMysqliDb*@authorJefferyWay<jeffrey@jeffrey-waycom>*@authorJoshCampbell<jcampbell@ajillioncom>*@authorAlexanderVButenko<abutenka@gmailcom>*@copyrightCopyright(c)2010-2016*@licensehttp://opensourceorg/licenses/gpl-30htmlGNUPublicLicense*@linkhttp://githubcom/joshcam/PHP-MySQLi-Database-Class*@version27-master*/

class MysqliDb
{

    protected static $_instance;
    public static $prefix = '';
    protected $_mysqli;
    protected $_query;
    protected $_lastQuery;

    /***TheSQLqueryoptionsrequiredafterSELECT,INSERT,UPDATEorDELETE*@varstring*/
    protected $_queryOptions = array();

    /***Anarraythatholdswherejoins*@vararray*/
    protected $_join = array();

    /***Anarraythatholdswhereconditions*@vararray*/
    protected $_where = array();

    /***Anarraythatholdswherejoinands**@vararray*/
    protected $_joinAnd = array();

    /***Anarraythatholdshavingconditions*@vararray*/
    protected $_having = array();

    /***Dynamictypelistfororderbyconditionvalue*@vararray*/
    protected $_orderBy = array();

    /***Dynamictypelistforgroupbyconditionvalue*@vararray*/
    protected $_groupBy = array();
	
	/***Dynamictypelistfortempromarylockingtables*@vararray*/
	protected $_tableLocks = array();
	
	/***Variablewhichholdsthecurrenttablelockmethod*@varstring*/
	protected $_tableLockMethod = "READ";
	
    /***Dynamicarraythatholdsacombinationofwherecondition/tabledatavaluetypesandparameterreferences*@vararray*/
    protected $_bindParams = array(''); //Createtheempty0index

    /***Variablewhichholdsanamountofreturnedrowsduringget/getOne/selectqueries*@varstring*/
    public $count = 0;

    /***Variablewhichholdsanamountofreturnedrowsduringget/getOne/selectquerieswithwithTotalCount()*@varstring*/
    public $totalCount = 0;

    /***Variablewhichholdslaststatementerror*@varstring*/
    protected $_stmtError;

    /***Variablewhichholdslaststatementerrorcode*@varint*/
    protected $_stmtErrno;

    /***Databasecredentials*@varstring*/
    protected $host;
    protected $username;
    protected $password;
    protected $db;
    protected $port;
    protected $charset;

    /***IsSubqueryobject*@varbool*/
    protected $isSubQuery = false;

    /***Nameoftheautoincrementcolumn*@varint*/
    protected $_lastInsertId = null;

    /***ColumnnamesforupdatewhenusingonDuplicatemethod*@vararray*/
    protected $_updateColumns = null;

    /***Returntype:'array'toreturnresultsasarray,'object'asobject*'json'asjsonstring*@varstring*/
    public $returnType = 'array';

    /***Shouldjoin()resultsbenestedbytable*@varbool*/
    protected $_nestJoin = false;

    /***Tablename(withprefix,ifused)*@varstring*/
    private $_tableName = '';

    /***FORUPDATEflag*@varbool*/
    protected $_forUpdate = false;

    /***LOCKINSHAREMODEflag*@varbool*/
    protected $_lockInShareMode = false;

    /***KeyfieldforMap()'edresultarray*@varstring*/
    protected $_mapKey = null;

    /***Variablesforqueryexecutiontracing*/
    protected $traceStartQ;
    protected $traceEnabled;
    protected $traceStripPrefix;
    public $trace = array();

    /***Perpagelimitforpagination**@varint*/

    public $pageLimit = 20;
    /***Variablethatholdstotalpagescountoflastpaginate()query**@varint*/
    public $totalPages = 0;

    /***@paramstring$host*@paramstring$username*@paramstring$password*@paramstring$db*@paramint$port*@paramstring$charset*/
    public function __construct($host = null, $username = null, $password = null, $db = null, $port = null, $charset = 'utf8')
    {
        $isSubQuery = false;

        //ifparamswerepassedasarray
        if (is_array($host)) {
            foreach ($host as $key => $val) {
                $$key = $val;
            }
        }
        //ifhostweresetasmysqlisocket
        if (is_object($host)) {
            $this->_mysqli = $host;
        } else {
            $this->host = $host;
        }

        $this->username = $username;
        $this->password = $password;
        $this->db = $db;
        $this->port = $port;
        $this->charset = $charset;

        if ($isSubQuery) {
            $this->isSubQuery = true;
            return;
        }

        if (isset($prefix)) {
            $this->setPrefix($prefix);
        }

        self::$_instance = $this;
    }

    /***Amethodtoconnecttothedatabase**@throwsException*@returnvoid*/
    public function connect()
    {
        if ($this->isSubQuery) {
            return;
        }

        if (empty($this->host)) {
            throw new Exception('MySQL host is not set');
        }

        $this->_mysqli = new mysqli($this->host, $this->username, $this->password, $this->db, $this->port);

        if ($this->_mysqli->connect_error) {
            throw new Exception('Connect Error ' . $this->_mysqli->connect_errno . ': ' . $this->_mysqli->connect_error, $this->_mysqli->connect_errno);
        }

        if ($this->charset) {
            $this->_mysqli->set_charset($this->charset);
        }
    }

    /***Amethodtogetmysqliobjectorcreateitincaseneeded**@returnmysqli*/
    public function mysqli()
    {
        if (!$this->_mysqli) {
            $this->connect();
        }
        return $this->_mysqli;
    }

    /***Amethodofreturningthestaticinstancetoallowaccesstothe*instantiatedobjectfromwithinanotherclass*Inheritingthisclasswouldrequirereloadingconnectioninfo**@uses$db=MySqliDb::getInstance();**@returnMysqliDbReturnsthecurrentinstance*/
    public static function getInstance()
    {
        return self::$_instance;
    }

    /***Resetstatesafteranexecution**@returnMysqliDbReturnsthecurrentinstance*/
    protected function reset()
    {
        if ($this->traceEnabled) {
            $this->trace[] = array($this->_lastQuery, (microtime(true) - $this->traceStartQ), $this->_traceGetCaller());
        }

        $this->_where = array();
        $this->_having = array();
        $this->_join = array();
        $this->_joinAnd = array();
        $this->_orderBy = array();
        $this->_groupBy = array();
        $this->_bindParams = array(''); //Createtheempty0index
        $this->_query = null;
        $this->_queryOptions = array();
        $this->returnType = 'array';
        $this->_nestJoin = false;
        $this->_forUpdate = false;
        $this->_lockInShareMode = false;
        $this->_tableName = '';
        $this->_lastInsertId = null;
        $this->_updateColumns = null;
        $this->_mapKey = null;
    }

    /***HelperfunctiontocreatedbObjectwithJSONreturntype**@returnMysqliDb*/
    public function jsonBuilder()
    {
        $this->returnType = 'json';
        return $this;
    }

    /***HelperfunctiontocreatedbObjectwitharrayreturntype*Addedforconsistencyasthatsdefaultoutputtype**@returnMysqliDb*/
    public function arrayBuilder()
    {
        $this->returnType = 'array';
        return $this;
    }

    /***HelperfunctiontocreatedbObjectwithobjectreturntype**@returnMysqliDb*/
    public function objectBuilder()
    {
        $this->returnType = 'object';
        return $this;
    }

    /***Methodtosetaprefix**@paramstring$prefixContainsatableprefix**@returnMysqliDb*/
    public function setPrefix($prefix = '')
    {
        self::$prefix = $prefix;
        return $this;
    }

	/***Pushesaunpreparedstatementtothemysqlistack*WARNING:Usewithcaution*Thismethoddoesnotescapestringsbydefaultsomakesureyou'llneveruseitinproduction**@authorJonasBarascu*@param[[Type]]$query[[Description]]*/
	private function queryUnprepared($query)
	{	
		//Executequery
		$stmt = $this->mysqli()->query($query);

		//Failed
		if(!$stmt){
			throw new Exception("Unprepared Query Failed, ERRNO: ".$this->mysqli()->errno." (".$this->mysqli()->error.")", $this->mysqli()->errno);
		};
		
		//returnstmtforfutureuse
		return $stmt;
	}
	
    /***ExecuterawSQLquery**@paramstring$queryUser-providedquerytoexecute*@paramarray$bindParamsVariablesarraytobindtotheSQLstatement**@returnarrayContainsthereturnedrowsfromthequery*/
    public function rawQuery($query, $bindParams = null)
    {
        $params = array(''); //Createtheempty0index
        $this->_query = $query;
        $stmt = $this->_prepareQuery();

        if (is_array($bindParams) === true) {
            foreach ($bindParams as $prop => $val) {
                $params[0] .= $this->_determineType($val);
                array_push($params, $bindParams[$prop]);
            }

            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($params));
        }

        $stmt->execute();
        $this->count = $stmt->affected_rows;
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $params);
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /***HelperfunctiontoexecuterawSQLqueryandreturnonly1rowofresults*Notethatfunctiondonotadd'limit1'tothequerybyitself*SameideaasgetOne()**@paramstring$queryUser-providedquerytoexecute*@paramarray$bindParamsVariablesarraytobindtotheSQLstatement**@returnarray|nullContainsthereturnedrowfromthequery*/
    public function rawQueryOne($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (is_array($res) && isset($res[0])) {
            return $res[0];
        }

        return null;
    }

    /***HelperfunctiontoexecuterawSQLqueryandreturnonly1columnofresults*If'limit1'willbefound,thenstringwillbereturnedinsteadofarray*SameideaasgetValue()**@paramstring$queryUser-providedquerytoexecute*@paramarray$bindParamsVariablesarraytobindtotheSQLstatement**@returnmixedContainsthereturnedrowsfromthequery*/
    public function rawQueryValue($query, $bindParams = null)
    {
        $res = $this->rawQuery($query, $bindParams);
        if (!$res) {
            return null;
        }

        $limit = preg_match('/limit\s+1;?$/i', $query);
        $key = key($res[0]);
        if (isset($res[0][$key]) && $limit == true) {
            return $res[0][$key];
        }

        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i][$key];
        }
        return $newRes;
    }

    /***Amethodtoperformselectquery**@paramstring$queryContainsauser-providedselectquery*@paramint|array$numRowsArraytodefineSQLlimitinformatArray($count,$offset)**@returnarrayContainsthereturnedrowsfromthequery*/
    public function query($query, $numRows = null)
    {
        $this->_query = $query;
        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)optionsforSQLqueries**@uses$MySqliDb->setQueryOption('name');**@paramstring|array$optionsTheoptonsnameofthequery**@throwsException*@returnMysqliDb*/
    public function setQueryOption($options)
    {
        $allowedOptions = Array('ALL', 'DISTINCT', 'DISTINCTROW', 'HIGH_PRIORITY', 'STRAIGHT_JOIN', 'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 'SQL_CACHE', 'SQL_NO_CACHE', 'SQL_CALC_FOUND_ROWS',
            'LOW_PRIORITY', 'IGNORE', 'QUICK', 'MYSQLI_NESTJOIN', 'FOR UPDATE', 'LOCK IN SHARE MODE');

        if (!is_array($options)) {
            $options = Array($options);
        }

        foreach ($options as $option) {
            $option = strtoupper($option);
            if (!in_array($option, $allowedOptions)) {
                throw new Exception('Wrong query option: ' . $option);
            }

            if ($option == 'MYSQLI_NESTJOIN') {
                $this->_nestJoin = true;
            } elseif ($option == 'FOR UPDATE') {
                $this->_forUpdate = true;
            } elseif ($option == 'LOCK IN SHARE MODE') {
                $this->_lockInShareMode = true;
            } else {
                $this->_queryOptions[] = $option;
            }
        }

        return $this;
    }

    /***FunctiontoenableSQL_CALC_FOUND_ROWSinthegetqueries**@returnMysqliDb*/
    public function withTotalCount()
    {
        $this->setQueryOption('SQL_CALC_FOUND_ROWS');
        return $this;
    }

    /***AconvenientSELECT*function**@paramstring$tableNameThenameofthedatabasetabletoworkwith*@paramint|array$numRowsArraytodefineSQLlimitinformatArray($count,$offset)*oronly$count*@paramstring$columnsDesiredcolumns**@returnarrayContainsthereturnedrowsfromtheselectquery*/
    public function get($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }

        $column = is_array($columns) ? implode(', ', $columns) : $columns;

        if (strpos($tableName, '.') === false) {
            $this->_tableName = self::$prefix . $tableName;
        } else {
            $this->_tableName = $tableName;
        }

        $this->_query = 'SELECT ' . implode(' ', $this->_queryOptions) . ' ' .
            $column . " FROM " . $this->_tableName;
        $stmt = $this->_buildQuery($numRows);

        if ($this->isSubQuery) {
            return $this;
        }

        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $res = $this->_dynamicBindResults($stmt);
        $this->reset();

        return $res;
    }

    /***AconvenientSELECT*functiontogetonerecord**@paramstring$tableNameThenameofthedatabasetabletoworkwith*@paramstring$columnsDesiredcolumns**@returnarrayContainsthereturnedrowsfromtheselectquery*/
    public function getOne($tableName, $columns = '*')
    {
        $res = $this->get($tableName, 1, $columns);

        if ($res instanceof MysqliDb) {
            return $res;
        } elseif (is_array($res) && isset($res[0])) {
            return $res[0];
        } elseif ($res) {
            return $res;
        }

        return null;
    }

    /***AconvenientSELECTCOLUMNfunctiontogetasinglecolumnvaluefromonerow**@paramstring$tableNameThenameofthedatabasetabletoworkwith*@paramstring$columnThedesiredcolumn*@paramint$limitLimitofrowstoselectUsenullforunlimited1bydefault**@returnmixedContainsthevalueofareturnedcolumn/arrayofvalues*/
    public function getValue($tableName, $column, $limit = 1)
    {
        $res = $this->ArrayBuilder()->get($tableName, $limit, "{$column} AS retval");

        if (!$res) {
            return null;
        }

        if ($limit == 1) {
            if (isset($res[0]["retval"])) {
                return $res[0]["retval"];
            }
            return null;
        }

        $newRes = Array();
        for ($i = 0; $i < $this->count; $i++) {
            $newRes[] = $res[$i]['retval'];
        }
        return $newRes;
    }

    /***Insertmethodtoaddnewrow**@paramstring$tableNameThenameofthetable*@paramarray$insertDataDatacontaininginformationforinsertingintotheDB**@returnboolBooleanindicatingwhethertheinsertquerywascompletedsuccesfully*/
    public function insert($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'INSERT');
    }

    /***Insertmethodtoaddseveralrowsatonce**@paramstring$tableNameThenameofthetable*@paramarray$multiInsertDataTwo-dimensinalData-arraycontaininginformationforinsertingintotheDB*@paramarray$dataKeysOptinalTableKeynames,ifnotsetininsertDataSet**@returnbool|arrayBooleanindicatingtheinsertionfailed(false),elsereturnid-array([int])*/
    public function insertMulti($tableName, array $multiInsertData, array $dataKeys = null)
    {
        //onlyauto-commitourinserts,ifnotransactioniscurrentlyrunning
        $autoCommit = (isset($this->_transaction_in_progress) ? !$this->_transaction_in_progress : true);
        $ids = array();

        if($autoCommit) {
            $this->startTransaction();
        }

        foreach ($multiInsertData as $insertData) {
            if($dataKeys !== null) {
                //applycolumn-namesifgiven,elseassumethey'realreadygiveninthedata
                $insertData = array_combine($dataKeys, $insertData);
            }

            $id = $this->insert($tableName, $insertData);
            if(!$id) {
                if($autoCommit) {
                    $this->rollback();
                }
                return false;
            }
            $ids[] = $id;
        }

        if($autoCommit) {
            $this->commit();
        }

        return $ids;
    }

    /***Replacemethodtoaddnewrow**@paramstring$tableNameThenameofthetable*@paramarray$insertDataDatacontaininginformationforinsertingintotheDB**@returnboolBooleanindicatingwhethertheinsertquerywascompletedsuccesfully*/
    public function replace($tableName, $insertData)
    {
        return $this->_buildInsert($tableName, $insertData, 'REPLACE');
    }

    /***AconvenientfunctionthatreturnsTRUEifexistsatleastanelementthat*satisfythewhereconditionspecifiedcallingthe"where"methodbeforethisone**@paramstring$tableNameThenameofthedatabasetabletoworkwith**@returnarrayContainsthereturnedrowsfromtheselectquery*/
    public function has($tableName)
    {
        $this->getOne($tableName, '1');
        return $this->count >= 1;
    }

    /***UpdatequeryBesuretofirstcallthe"where"method**@paramstring$tableNameThenameofthedatabasetabletoworkwith*@paramarray$tableDataArrayofdatatoupdatethedesiredrow*@paramint$numRowsLimitonthenumberofrowsthatcanbeupdated**@returnbool*/
    public function update($tableName, $tableData, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }

        $this->_query = "UPDATE " . self::$prefix . $tableName;

        $stmt = $this->_buildQuery($numRows, $tableData);
        $status = $stmt->execute();
        $this->reset();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->count = $stmt->affected_rows;

        return $status;
    }

    /***DeletequeryCallthe"where"methodfirst**@paramstring$tableNameThenameofthedatabasetabletoworkwith*@paramint|array$numRowsArraytodefineSQLlimitinformatArray($count,$offset)*oronly$count**@returnboolIndicatessuccess0or1*/
    public function delete($tableName, $numRows = null)
    {
        if ($this->isSubQuery) {
            return;
        }

        $table = self::$prefix . $tableName;

        if (count($this->_join)) {
            $this->_query = "DELETE " . preg_replace('/.* (.*)/', '$1', $table) . " FROM " . $table;
        } else {
            $this->_query = "DELETE FROM " . $table;
        }

        $stmt = $this->_buildQuery($numRows);
        $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $this->reset();

        return ($stmt->affected_rows > 0);
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ANDWHEREstatementsforSQLqueries**@uses$MySqliDb->where('id',7)->where('title','MyTitle');**@paramstring$wherePropThenameofthedatabasefield*@parammixed$whereValueThevalueofthedatabasefield*@paramstring$operatorComparisonoperatorDefaultis=*@paramstring$condConditionofwherestatement(OR,AND)**@returnMysqliDb*/
    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        //forkaroundforanoldoperationapi
        if (is_array($whereValue) && ($key = key($whereValue)) != "0") {
            $operator = $key;
            $whereValue = $whereValue[$key];
        }

        if (count($this->_where) == 0) {
            $cond = '';
        }

        $this->_where[] = array($cond, $whereProp, $operator, $whereValue);
        return $this;
    }

    /***Thisfunctionstoreupdatecolumn'snameandcolumnnameofthe*autoincrementcolumn**@paramarray$updateColumnsVariablewithvalues*@paramstring$lastInsertIdVariablevalue**@returnMysqliDb*/
    public function onDuplicate($updateColumns, $lastInsertId = null)
    {
        $this->_lastInsertId = $lastInsertId;
        $this->_updateColumns = $updateColumns;
        return $this;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ORWHEREstatementsforSQLqueries**@uses$MySqliDb->orWhere('id',7)->orWhere('title','MyTitle');**@paramstring$wherePropThenameofthedatabasefield*@parammixed$whereValueThevalueofthedatabasefield*@paramstring$operatorComparisonoperatorDefaultis=**@returnMysqliDb*/
    public function orWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        return $this->where($whereProp, $whereValue, $operator, 'OR');
    }
    
    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ANDHAVINGstatementsforSQLqueries**@uses$MySqliDb->having('SUM(tags)>10')**@paramstring$havingPropThenameofthedatabasefield*@parammixed$havingValueThevalueofthedatabasefield*@paramstring$operatorComparisonoperatorDefaultis=**@returnMysqliDb*/

    public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        //forkaroundforanoldoperationapi
        if (is_array($havingValue) && ($key = key($havingValue)) != "0") {
            $operator = $key;
            $havingValue = $havingValue[$key];
        }

        if (count($this->_having) == 0) {
            $cond = '';
        }

        $this->_having[] = array($cond, $havingProp, $operator, $havingValue);
        return $this;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ORHAVINGstatementsforSQLqueries**@uses$MySqliDb->orHaving('SUM(tags)>10')**@paramstring$havingPropThenameofthedatabasefield*@parammixed$havingValueThevalueofthedatabasefield*@paramstring$operatorComparisonoperatorDefaultis=**@returnMysqliDb*/
    public function orHaving($havingProp, $havingValue = null, $operator = null)
    {
        return $this->having($havingProp, $havingValue, $operator, 'OR');
    }

    /***ThismethodallowsyoutoconcatenatejoinsforthefinalSQLstatement**@uses$MySqliDb->join('table1','field1<>field2','LEFT')**@paramstring$joinTableThenameofthetable*@paramstring$joinConditionthecondition*@paramstring$joinType'LEFT','INNER'etc**@throwsException*@returnMysqliDb*/
    public function join($joinTable, $joinCondition, $joinType = '')
    {
        $allowedTypes = array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER');
        $joinType = strtoupper(trim($joinType));

        if ($joinType && !in_array($joinType, $allowedTypes)) {
            throw new Exception('Wrong JOIN type: ' . $joinType);
        }

        if (!is_object($joinTable)) {
            $joinTable = self::$prefix . $joinTable;
        }

        $this->_join[] = Array($joinType, $joinTable, $joinCondition);

        return $this;
    }
	
	
	/***ThisisabasicmethodwhichallowsyoutoimportrawCSVdataintoatable*Pleasecheckouthttp://devmysqlcom/doc/refman/57/en/load-datahtmlforavalidcsvfile*@authorJonasBarascu(Noneatme)*@paramstring$importTableThedatabasetablewherethedatawillbeimportedinto*@paramstring$importFileThefiletobeimportedPleaseusedoublebackslashes\\andmakesureyou*@paramstring$importSettingsAnArraydefiningtheimportsettingsasdescribedintheREADMEmd*@returnboolean*/
	public function loadData($importTable, $importFile, $importSettings = null)
	{
		//Wehavetocheckifthefileexists
		if(!file_exists($importFile)) {
			//Throwanexception
			throw new Exception("importCSV -> importFile ".$importFile." does not exists!");
			return;
		}
		
		//Definethedefaultvalues
		//Wewillmergeitlater
		$settings 				= Array("fieldChar" => ';', "lineChar" => PHP_EOL, "linesToIgnore" => 1);
		
		//Checktheimportsettings
		if(gettype($importSettings) == "array") {
			//Mergethedefaultarraywiththecustomone
			$settings = array_merge($settings, $importSettings);
		}
	
		//Addtheprefixtotheimporttable
		$table = self::$prefix . $importTable;
		
		//Add1moreslashtoeveryslashsomariawillinterpretitasapath
		$importFile = str_replace("\\", "\\\\", $importFile);  
		
		//BuildSQLSyntax
		$sqlSyntax = sprintf('LOAD DATA INFILE \'%s\' INTO TABLE %s', 
					$importFile, $table);
		
		//FIELDS
		$sqlSyntax .= sprintf(' FIELDS TERMINATED BY \'%s\'', $settings["fieldChar"]);
		if(isset($settings["fieldEnclosure"])) {
			$sqlSyntax .= sprintf(' ENCLOSED BY \'%s\'', $settings["fieldEnclosure"]);
		}
		
		//LINES
		$sqlSyntax .= sprintf(' LINES TERMINATED BY \'%s\'', $settings["lineChar"]);
		if(isset($settings["lineStarting"])) {
			$sqlSyntax .= sprintf(' STARTING BY \'%s\'', $settings["lineStarting"]);
		}
			
		//IGNORELINES
		$sqlSyntax .= sprintf(' IGNORE %d LINES', $settings["linesToIgnore"]);
	
		//ExceutethequeryunpreparedbecauseLOADDATAonlyworkswithunpreparedstatements
		$result = $this->queryUnprepared($sqlSyntax);

		//Arethererowsmodified
		//Lettheuserknowiftheimportfailed/succeeded
		return (bool) $result;
	}
	
	/***ThismethodisusefullforimportingXMLfilesintoaspecifictable*CheckouttheLOADXMLsyntaxforyourMySQLserver**@authorJonasBarascu*@paramstring$importTableThetableinwhichthedatawillbeimportedto*@paramstring$importFileThefilewhichcontainstheXMLdata*@paramstring$importSettingsAnArraydefiningtheimportsettingsasdescribedintheREADMEmd**@returnbooleanReturnstrueiftheimportsucceeded,falseifitfailed*/
	public function loadXml($importTable, $importFile, $importSettings = null)
	{
		//Wehavetocheckifthefileexists
		if(!file_exists($importFile)) {
			//Doesnotexists
			throw new Exception("loadXml: Import file does not exists");
			return;
		}
		
		//Createdefaultvalues
		$settings 			= Array("linesToIgnore" => 0);

		//Checktheimportsettings
		if(gettype($importSettings) == "array") {
			$settings = array_merge($settings, $importSettings);
		}

		//Addtheprefixtotheimporttable
		$table = self::$prefix . $importTable;
		
		//Add1moreslashtoeveryslashsomariawillinterpretitasapath
		$importFile = str_replace("\\", "\\\\", $importFile);  
		
		//BuildSQLSyntax
		$sqlSyntax = sprintf('LOAD XML INFILE \'%s\' INTO TABLE %s', 
								 $importFile, $table);
		
		//FIELDS
		if(isset($settings["rowTag"])) {
			$sqlSyntax .= sprintf(' ROWS IDENTIFIED BY \'%s\'', $settings["rowTag"]);
		}
			
		//IGNORELINES
		$sqlSyntax .= sprintf(' IGNORE %d LINES', $settings["linesToIgnore"]);
		
		//ExceutethequeryunpreparedbecauseLOADXMLonlyworkswithunpreparedstatements
		$result = $this->queryUnprepared($sqlSyntax);

		//Arethererowsmodified
		//Lettheuserknowiftheimportfailed/succeeded
		return (bool) $result;
	}

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ORDERBYstatementsforSQLqueries**@uses$MySqliDb->orderBy('id','desc')->orderBy('name','desc');**@paramstring$orderByFieldThenameofthedatabasefield*@paramstring$orderByDirectionOrderdirection*@paramarray$customFieldsFieldsetforORDERBYFIELD()ordering**@throwsException*@returnMysqliDb*/
    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFields = null)
    {
        $allowedDirection = Array("ASC", "DESC");
        $orderbyDirection = strtoupper(trim($orderbyDirection));
        $orderByField = preg_replace("/[^-a-z0-9\.\(\),_`\*\'\"]+/i", '', $orderByField);

        //AddtableprefixtoorderByFieldifneeded
        //FIXME:Weareaddingprefixonlyiftableisenclosedinto``todistinguishaliases
        //fromtablenames
        $orderByField = preg_replace('/(\`)([`a-zA-Z0-9_]*\.)/', '\1' . self::$prefix . '\2', $orderByField);


        if (empty($orderbyDirection) || !in_array($orderbyDirection, $allowedDirection)) {
            throw new Exception('Wrong order direction: ' . $orderbyDirection);
        }

        if (is_array($customFields)) {
            foreach ($customFields as $key => $value) {
                $customFields[$key] = preg_replace("/[^-a-z0-9\.\(\),_` ]+/i", '', $value);
            }

            $orderByField = 'FIELD (' . $orderByField . ', "' . implode('","', $customFields) . '")';
        }

        $this->_orderBy[$orderByField] = $orderbyDirection;
        return $this;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)GROUPBYstatementsforSQLqueries**@uses$MySqliDb->groupBy('name');**@paramstring$groupByFieldThenameofthedatabasefield**@returnMysqliDb*/
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);

        $this->_groupBy[] = $groupByField;
        return $this;
    }
	
	
	/***Thismethodsetsthecurrenttablelockmethod**@authorJonasBarascu*@paramstring$methodThetablelockmethodCanbeREADorWRITE**@throwsException*@returnMysqliDb*/
	public function setLockMethod($method)
	{
		//Switchtheuppercasestring
		switch(strtoupper($method)) {
			//IsitREADorWRITE
			case "READ" || "WRITE":
				//Succeed
				$this->_tableLockMethod = $method;
				break;
			default:
				//Elsethrowanexception
				throw new Exception("Bad lock type: Can be either READ or WRITE");
				break;
		}
		return $this;
	}
	
	/***LocksatableforR/Waction**@authorJonasBarascu*@paramstring$tableThetabletobelockedCanbeatableoraview**@throwsException*@returnMysqliDbifsucceeeded;*/
	public function lock($table)
	{
		//MainQuery
		$this->_query = "LOCK TABLES";
		
		//Isthetableanarray
		if(gettype($table) == "array") {
			//Looptroughitandattachittothequery
			foreach($table as $key => $value) {
				if(gettype($value) == "string") {
					if($key > 0) {
						$this->_query .= ",";
					}
					$this->_query .= " ".self::$prefix.$value." ".$this->_tableLockMethod;
				}
			}
		}
		else{
			//Buildthetableprefix
			$table = self::$prefix . $table;
			
			//Buildthequery
			$this->_query = "LOCK TABLES ".$table." ".$this->_tableLockMethod;
		}

		//ExceutethequeryunpreparedbecauseLOCKonlyworkswithunpreparedstatements
		$result = $this->queryUnprepared($this->_query);
        $errno  = $this->mysqli()->errno;
			
		//Resetthequery
		$this->reset();

		//Arethererowsmodified
		if($result) {	
			//Returntrue
			//Wecan'treturnourselfbecauseifonetablegetslocked,allotheronesgetunlocked!
			return true;
		}
		//Somethingwentwrong
		else {
			throw new Exception("Locking of table ".$table." failed", $errno);
		}

		//Returnthesuccessvalue
		return false;
	}
	
	/***Unlocksalltablesinadatabase*Alsocommitstransactions**@authorJonasBarascu*@returnMysqliDb*/
	public function unlock()
	{
		//Buildthequery
		$this->_query = "UNLOCK TABLES";

		//ExceutethequeryunpreparedbecauseUNLOCKandLOCKonlyworkswithunpreparedstatements
		$result = $this->queryUnprepared($this->_query);
        $errno  = $this->mysqli()->errno;

		//Resetthequery
		$this->reset();

		//Arethererowsmodified
		if($result) {
			//returnself
			return $this;
		}
		//Somethingwentwrong
		else {
			throw new Exception("Unlocking of tables failed", $errno);
		}
		
	
		//Returnself
		return $this;
	}

	
    /***ThismethodsreturnstheIDofthelastinserteditem**@returnintThelastinserteditemID*/
    public function getInsertId()
    {
        return $this->mysqli()->insert_id;
    }

    /***Escapeharmfulcharacterswhichmightaffectaquery**@paramstring$strThestringtoescape**@returnstringTheescapedstring*/
    public function escape($str)
    {
        return $this->mysqli()->real_escape_string($str);
    }

    /***Methodtocallmysqli->ping()tokeepunusedconnectionsopenon*long-runningscripts,ortoreconnecttimedoutconnections(ifphpinihas*globalmysqlireconnectsettotrue)Can'tdothisdirectlyusingobject*since_mysqliisprotected**@returnboolTrueifconnectionisup*/
    public function ping()
    {
        return $this->mysqli()->ping();
    }

    /***ThismethodisneededforpreparedstatementsTheyrequire*thedatatypeofthefieldtobeboundwith"i"s",etc*Thisfunctiontakestheinput,determineswhattypeitis,*andthenupdatestheparam_type**@parammixed$itemInputtodeterminethetype**@returnstringThejoinedparametertypes*/
    protected function _determineType($item)
    {
        switch (gettype($item)) {
            case 'NULL':
            case 'string':
                return 's';
                break;

            case 'boolean':
            case 'integer':
                return 'i';
                break;

            case 'blob':
                return 'b';
                break;

            case 'double':
                return 'd';
                break;
        }
        return '';
    }

    /***Helperfunctiontoaddvariablesintobindparametersarray**@paramstringVariablevalue*/
    protected function _bindParam($value)
    {
        $this->_bindParams[0] .= $this->_determineType($value);
        array_push($this->_bindParams, $value);
    }

    /***Helperfunctiontoaddvariablesintobindparametersarrayinbulk**@paramarray$valuesVariablewithvalues*/
    protected function _bindParams($values)
    {
        foreach ($values as $value) {
            $this->_bindParam($value);
        }
    }

    /***Helperfunctiontoaddvariablesintobindparametersarrayandwillreturn*itsSQLpartofthequeryaccordingtooperatorin'$operator'or*'$operator($subquery)'formats**@paramstring$operator*@parammixed$valueVariablewithvalues**@returnstring*/
    protected function _buildPair($operator, $value)
    {
        if (!is_object($value)) {
            $this->_bindParam($value);
            return ' ' . $operator . ' ? ';
        }

        $subQuery = $value->getSubQuery();
        $this->_bindParams($subQuery['params']);

        return " " . $operator . " (" . $subQuery['query'] . ") " . $subQuery['alias'];
    }

    /***InternalfunctiontobuildandexecuteINSERT/REPLACEcalls**@paramstring$tableNameThenameofthetable*@paramarray$insertDataDatacontaininginformationforinsertingintotheDB*@paramstring$operationTypeofoperation(INSERT,REPLACE)**@returnboolBooleanindicatingwhethertheinsertquerywascompletedsuccesfully*/
    private function _buildInsert($tableName, $insertData, $operation)
    {
        if ($this->isSubQuery) {
            return;
        }

        $this->_query = $operation . " " . implode(' ', $this->_queryOptions) . " INTO " . self::$prefix . $tableName;
        $stmt = $this->_buildQuery(null, $insertData);
        $status = $stmt->execute();
        $this->_stmtError = $stmt->error;
        $this->_stmtErrno = $stmt->errno;
        $haveOnDuplicate = !empty ($this->_updateColumns);
        $this->reset();
        $this->count = $stmt->affected_rows;

        if ($stmt->affected_rows < 1) {
            //incaseofonDuplicate()usage,ifnorowswereinserted
            if ($status && $haveOnDuplicate) {
                return true;
            }
            return false;
        }

        if ($stmt->insert_id > 0) {
            return $stmt->insert_id;
        }

        return true;
    }

    /***AbstractionmethodthatwillcompiletheWHEREstatement,*anypassedupdatedata,andthedesiredrows*ItthenbuildstheSQLquery**@paramint|array$numRowsArraytodefineSQLlimitinformatArray($count,$offset)*oronly$count*@paramarray$tableDataShouldcontainanarrayofdataforupdatingthedatabase**@returnmysqli_stmtReturnsthe$stmtobject*/
    protected function _buildQuery($numRows = null, $tableData = null)
    {
        //$this->_buildJoinOld();
        $this->_buildJoin();
        $this->_buildInsertQuery($tableData);
        $this->_buildCondition('WHERE', $this->_where);
        $this->_buildGroupBy();
        $this->_buildCondition('HAVING', $this->_having);
        $this->_buildOrderBy();
        $this->_buildLimit($numRows);
        $this->_buildOnDuplicate($tableData);
        
        if ($this->_forUpdate) {
            $this->_query .= ' FOR UPDATE';
        }
        if ($this->_lockInShareMode) {
            $this->_query .= ' LOCK IN SHARE MODE';
        }

        $this->_lastQuery = $this->replacePlaceHolders($this->_query, $this->_bindParams);

        if ($this->isSubQuery) {
            return;
        }

        //Preparequery
        $stmt = $this->_prepareQuery();

        //Bindparameterstostatementifany
        if (count($this->_bindParams) > 1) {
            call_user_func_array(array($stmt, 'bind_param'), $this->refValues($this->_bindParams));
        }

        return $stmt;
    }

    /***Thishelpermethodtakescareofpreparedstatements'"bind_resultmethod*,whenthenumberofvariablestopassisunknown**@parammysqli_stmt$stmtEqualtothepreparedstatementobject**@returnarrayTheresultsoftheSQLfetch*/
    protected function _dynamicBindResults(mysqli_stmt $stmt)
    {
        $parameters = array();
        $results = array();
        /***@seehttp://phpnet/manual/en/mysqli-resultfetch-fieldsphp*/
        $mysqlLongType = 252;
        $shouldStoreResult = false;

        $meta = $stmt->result_metadata();

        //if$metaisfalseyetsqlstateistrue,there'snosqlerrorbutthequeryis
        //mostlikelyanupdate/insert/deletewhichdoesn'tproduceanyresults
        if (!$meta && $stmt->sqlstate)
            return array();

        $row = array();
        while ($field = $meta->fetch_field()) {
            if ($field->type == $mysqlLongType) {
                $shouldStoreResult = true;
            }

            if ($this->_nestJoin && $field->table != $this->_tableName) {
                $field->table = substr($field->table, strlen(self::$prefix));
                $row[$field->table][$field->name] = null;
                $parameters[] = & $row[$field->table][$field->name];
            } else {
                $row[$field->name] = null;
                $parameters[] = & $row[$field->name];
            }
        }

        //avoidoutofmemorybuginphp52and53Mysqliallocateslotofmemoryforlong*
        //andblob*typesSotoavoidoutofmemoryissuesstore_resultisused
        //https://githubcom/joshcam/PHP-MySQLi-Database-Class/pull/119
        if ($shouldStoreResult) {
            $stmt->store_result();
        }

        call_user_func_array(array($stmt, 'bind_result'), $parameters);

        $this->totalCount = 0;
        $this->count = 0;

        while ($stmt->fetch()) {
            if ($this->returnType == 'object') {
                $result = new stdClass ();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        $result->$key = new stdClass ();
                        foreach ($val as $k => $v) {
                            $result->$key->$k = $v;
                        }
                    } else {
                        $result->$key = $val;
                    }
                }
            } else {
                $result = array();
                foreach ($row as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k => $v) {
                            $result[$key][$k] = $v;
                        }
                    } else {
                        $result[$key] = $val;
                    }
                }
            }
            $this->count++;
            if ($this->_mapKey) {
                $results[$row[$this->_mapKey]] = count($row) > 2 ? $result : end($result);
            } else {
                array_push($results, $result);
            }
        }

        if ($shouldStoreResult) {
            $stmt->free_result();
        }

        $stmt->close();

        //storedproceduressometimescanreturnmorethen1resultset
        if ($this->mysqli()->more_results()) {
            $this->mysqli()->next_result();
        }

        if (in_array('SQL_CALC_FOUND_ROWS', $this->_queryOptions)) {
            $stmt = $this->mysqli()->query('SELECT FOUND_ROWS()');
            $totalCount = $stmt->fetch_row();
            $this->totalCount = $totalCount[0];
        }

        if ($this->returnType == 'json') {
            return json_encode($results);
        }

        return $results;
    }

    /***AbstractionmethodthatwillbuildanJOINpartofthequery**@returnvoid*/
    protected function _buildJoinOld()
    {
        if (empty($this->_join)) {
            return;
        }

        foreach ($this->_join as $data) {
            list ($joinType, $joinTable, $joinCondition) = $data;

            if (is_object($joinTable)) {
                $joinStr = $this->_buildPair("", $joinTable);
            } else {
                $joinStr = $joinTable;
            }

            $this->_query .= " " . $joinType . " JOIN " . $joinStr . 
                (false !== stripos($joinCondition, 'using') ? " " : " on ")
                . $joinCondition;
        }
    }

    /***Insert/Updatequeryhelper**@paramarray$tableData*@paramarray$tableColumns*@parambool$isInsertINSERToperationflag**@throwsException*/
    public function _buildDataPairs($tableData, $tableColumns, $isInsert)
    {
        foreach ($tableColumns as $column) {
            $value = $tableData[$column];

            if (!$isInsert) {
                if(strpos($column,'.')===false) {
                    $this->_query .= "`" . $column . "` = ";
                } else {
                    $this->_query .= str_replace('.','.`',$column) . "` = ";
                }
            }

            //Subqueryvalue
            if ($value instanceof MysqliDb) {
                $this->_query .= $this->_buildPair("", $value) . ", ";
                continue;
            }

            //Simplevalue
            if (!is_array($value)) {
                $this->_bindParam($value);
                $this->_query .= '?, ';
                continue;
            }

            //Functionvalue
            $key = key($value);
            $val = $value[$key];
            switch ($key) {
                case '[I]':
                    $this->_query .= $column . $val . ", ";
                    break;
                case '[F]':
                    $this->_query .= $val[0] . ", ";
                    if (!empty($val[1])) {
                        $this->_bindParams($val[1]);
                    }
                    break;
                case '[N]':
                    if ($val == null) {
                        $this->_query .= "!" . $column . ", ";
                    } else {
                        $this->_query .= "!" . $val . ", ";
                    }
                    break;
                default:
                    throw new Exception("Wrong operation");
            }
        }
        $this->_query = rtrim($this->_query, ', ');
    }

    /***Helperfunctiontoaddvariablesintothequerystatement**@paramarray$tableDataVariablewithvalues*/
    protected function _buildOnDuplicate($tableData)
    {
        if (is_array($this->_updateColumns) && !empty($this->_updateColumns)) {
            $this->_query .= " ON DUPLICATE KEY UPDATE ";
            if ($this->_lastInsertId) {
                $this->_query .= $this->_lastInsertId . "=LAST_INSERT_ID (" . $this->_lastInsertId . "), ";
            }

            foreach ($this->_updateColumns as $key => $val) {
                //skipallparamswithoutavalue
                if (is_numeric($key)) {
                    $this->_updateColumns[$val] = '';
                    unset($this->_updateColumns[$key]);
                } else {
                    $tableData[$key] = $val;
                }
            }
            $this->_buildDataPairs($tableData, array_keys($this->_updateColumns), false);
        }
    }

    /***AbstractionmethodthatwillbuildanINSERTorUPDATEpartofthequery**@paramarray$tableData*/
    protected function _buildInsertQuery($tableData)
    {
        if (!is_array($tableData)) {
            return;
        }

        $isInsert = preg_match('/^[INSERT|REPLACE]/', $this->_query);
        $dataColumns = array_keys($tableData);
        if ($isInsert) {
            if (isset ($dataColumns[0]))
                $this->_query .= ' (`' . implode($dataColumns, '`, `') . '`) ';
            $this->_query .= ' VALUES (';
        } else {
            $this->_query .= " SET ";
        }

        $this->_buildDataPairs($tableData, $dataColumns, $isInsert);

        if ($isInsert) {
            $this->_query .= ')';
        }
    }

    /***AbstractionmethodthatwillbuildthepartoftheWHEREconditions**@paramstring$operator*@paramarray$conditions*/
    protected function _buildCondition($operator, &$conditions)
    {
        if (empty($conditions)) {
            return;
        }

        //Preparethewhereportionofthequery
        $this->_query .= ' ' . $operator;

        foreach ($conditions as $cond) {
            list ($concat, $varName, $operator, $val) = $cond;
            $this->_query .= " " . $concat . " " . $varName;

            switch (strtolower($operator)) {
                case 'not in':
                case 'in':
                    $comparison = ' ' . $operator . ' (';
                    if (is_object($val)) {
                        $comparison .= $this->_buildPair("", $val);
                    } else {
                        foreach ($val as $v) {
                            $comparison .= ' ?,';
                            $this->_bindParam($v);
                        }
                    }
                    $this->_query .= rtrim($comparison, ',') . ' ) ';
                    break;
                case 'not between':
                case 'between':
                    $this->_query .= " $operator ? AND ? ";
                    $this->_bindParams($val);
                    break;
                case 'not exists':
                case 'exists':
                    $this->_query.= $operator . $this->_buildPair("", $val);
                    break;
                default:
                    if (is_array($val)) {
                        $this->_bindParams($val);
                    } elseif ($val === null) {
                        $this->_query .= ' ' . $operator . " NULL";
                    } elseif ($val != 'DBNULL' || $val == '0') {
                        $this->_query .= $this->_buildPair($operator, $val);
                    }
            }
        }
    }

    /***AbstractionmethodthatwillbuildtheGROUPBYpartoftheWHEREstatement**@returnvoid*/
    protected function _buildGroupBy()
    {
        if (empty($this->_groupBy)) {
            return;
        }

        $this->_query .= " GROUP BY ";

        foreach ($this->_groupBy as $key => $value) {
            $this->_query .= $value . ", ";
        }

        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    /***AbstractionmethodthatwillbuildtheLIMITpartoftheWHEREstatement**@returnvoid*/
    protected function _buildOrderBy()
    {
        if (empty($this->_orderBy)) {
            return;
        }

        $this->_query .= " ORDER BY ";
        foreach ($this->_orderBy as $prop => $value) {
            if (strtolower(str_replace(" ", "", $prop)) == 'rand()') {
                $this->_query .= "rand(), ";
            } else {
                $this->_query .= $prop . " " . $value . ", ";
            }
        }

        $this->_query = rtrim($this->_query, ', ') . " ";
    }

    /***AbstractionmethodthatwillbuildtheLIMITpartoftheWHEREstatement**@paramint|array$numRowsArraytodefineSQLlimitinformatArray($count,$offset)*oronly$count**@returnvoid*/
    protected function _buildLimit($numRows)
    {
        if (!isset($numRows)) {
            return;
        }

        if (is_array($numRows)) {
            $this->_query .= ' LIMIT ' . (int) $numRows[0] . ', ' . (int) $numRows[1];
        } else {
            $this->_query .= ' LIMIT ' . (int) $numRows;
        }
    }

    /***MethodattemptstopreparetheSQLquery*andthrowsanerroriftherewasaproblem**@returnmysqli_stmt*/
    protected function _prepareQuery()
    {
        if (!$stmt = $this->mysqli()->prepare($this->_query)) {
            $msg = $this->mysqli()->error . " query: " . $this->_query;
            $num = $this->mysqli()->errno;
            $this->reset();
            throw new Exception($msg, $num);
        }

        if ($this->traceEnabled) {
            $this->traceStartQ = microtime(true);
        }

        return $stmt;
    }

    /***Closeconnection**@returnvoid*/
    public function __destruct()
    {
        if ($this->isSubQuery) {
            return;
        }

        if ($this->_mysqli) {
            $this->_mysqli->close();
            $this->_mysqli = null;
        }
    }

    /***ReferenceddataarrayisrequiredbymysqlisincePHP53**@paramarray$arr**@returnarray*/
    protected function refValues(array &$arr)
    {
        //ReferenceinthefunctionargumentsarerequiredforHHVMtowork
        //https://githubcom/facebook/hhvm/issues/5155
        //ReferenceddataarrayisrequiredbymysqlisincePHP53
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = & $arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

    /***Functiontoreplacewithvariablesfrombindvariable**@paramstring$str*@paramarray$vals**@returnstring*/
    protected function replacePlaceHolders($str, $vals)
    {
        $i = 1;
        $newStr = "";

        if (empty($vals)) {
            return $str;
        }

        while ($pos = strpos($str, "?")) {
            $val = $vals[$i++];
            if (is_object($val)) {
                $val = '[object]';
            }
            if ($val === null) {
                $val = 'NULL';
            }
            $newStr .= substr($str, 0, $pos) . "'" . $val . "'";
            $str = substr($str, $pos + 1);
        }
        $newStr .= $str;
        return $newStr;
    }

    /***Methodreturnslastexecutedquery**@returnstring*/
    public function getLastQuery()
    {
        return $this->_lastQuery;
    }

    /***Methodreturnsmysqlerror**@returnstring*/
    public function getLastError()
    {
        if (!$this->_mysqli) {
            return "mysqli is null";
        }
        return trim($this->_stmtError . " " . $this->mysqli()->error);
    }

    /***Methodreturnsmysqlerrorcode*@returnint*/
    public function getLastErrno () {
        return $this->_stmtErrno;
    }

    /***Mostlyinternalmethodtogetqueryanditsparamsoutofsubqueryobject*afterget()andgetAll()**@returnarray*/
    public function getSubQuery()
    {
        if (!$this->isSubQuery) {
            return null;
        }

        array_shift($this->_bindParams);
        $val = Array('query' => $this->_query,
            'params' => $this->_bindParams,
            'alias' => $this->host
        );
        $this->reset();
        return $val;
    }
        
    /*Helperfunctions*/

    /***Methodreturnsgeneratedintervalfunctionasastring**@paramstring$diffintervalintheformats:*"1","-1d"or"-1day"--Forinterval-1day*Supportedintervals[s]econd,[m]inute,[h]hour,[d]day,[M]onth,[Y]ear*Defaultnull;*@paramstring$funcInitialdate**@returnstring*/
    public function interval($diff, $func = "NOW()")
    {
        $types = Array("s" => "second", "m" => "minute", "h" => "hour", "d" => "day", "M" => "month", "Y" => "year");
        $incr = '+';
        $items = '';
        $type = 'd';

        if ($diff && preg_match('/([+-]?) ?([0-9]+) ?([a-zA-Z]?)/', $diff, $matches)) {
            if (!empty($matches[1])) {
                $incr = $matches[1];
            }

            if (!empty($matches[2])) {
                $items = $matches[2];
            }

            if (!empty($matches[3])) {
                $type = $matches[3];
            }

            if (!in_array($type, array_keys($types))) {
                throw new Exception("invalid interval type in '{$diff}'");
            }

            $func .= " " . $incr . " interval " . $items . " " . $types[$type] . " ";
        }
        return $func;
    }

    /***Methodreturnsgeneratedintervalfunctionasaninsert/updatefunction**@paramstring$diffintervalintheformats:*"1","-1d"or"-1day"--Forinterval-1day*Supportedintervals[s]econd,[m]inute,[h]hour,[d]day,[M]onth,[Y]ear*Defaultnull;*@paramstring$funcInitialdate**@returnarray*/
    public function now($diff = null, $func = "NOW()")
    {
        return array("[F]" => Array($this->interval($diff, $func)));
    }

    /***Methodgeneratesincrementalfunctioncall**@paramint$numincrementbyintorfloat1bydefault**@throwsException*@returnarray*/
    public function inc($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('Argument supplied to inc must be a number');
        }
        return array("[I]" => "+" . $num);
    }

    /***Methodgeneratesdecrimentalfunctioncall**@paramint$numincrementbyintorfloat1bydefault**@returnarray*/
    public function dec($num = 1)
    {
        if (!is_numeric($num)) {
            throw new Exception('Argument supplied to dec must be a number');
        }
        return array("[I]" => "-" . $num);
    }

    /***Methodgenerateschangebooleanfunctioncall**@paramstring$colcolumnnamenullbydefault**@returnarray*/
    public function not($col = null)
    {
        return array("[N]" => (string) $col);
    }

    /***Methodgeneratesuserdefinedfunctioncall**@paramstring$expruserfunctionbody*@paramarray$bindParams**@returnarray*/
    public function func($expr, $bindParams = null)
    {
        return array("[F]" => array($expr, $bindParams));
    }

    /***Methodcreatesnewmysqlidbobjectforasubquerygeneration**@paramstring$subQueryAlias**@returnMysqliDb*/
    public static function subQuery($subQueryAlias = "")
    {
        return new self(array('host' => $subQueryAlias, 'isSubQuery' => true));
    }

    /***Methodreturnsacopyofamysqlidbsubqueryobject**@returnMysqliDbnewmysqlidbobject*/
    public function copy()
    {
        $copy = unserialize(serialize($this));
        $copy->_mysqli = null;
        return $copy;
    }

    /***Beginatransaction**@usesmysqli->autocommit(false)*@usesregister_shutdown_function(array($this,"_transaction_shutdown_check"))*/
    public function startTransaction()
    {
        $this->mysqli()->autocommit(false);
        $this->_transaction_in_progress = true;
        register_shutdown_function(array($this, "_transaction_status_check"));
    }

    /***Transactioncommit**@usesmysqli->commit();*@usesmysqli->autocommit(true);*/
    public function commit()
    {
        $result = $this->mysqli()->commit();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }

    /***Transactionrollbackfunction**@usesmysqli->rollback();*@usesmysqli->autocommit(true);*/
    public function rollback()
    {
        $result = $this->mysqli()->rollback();
        $this->_transaction_in_progress = false;
        $this->mysqli()->autocommit(true);
        return $result;
    }

    /***Shutdownhandlertorollbackuncommitedoperationsinordertokeep*atomicoperationssane**@usesmysqli->rollback();*/
    public function _transaction_status_check()
    {
        if (!$this->_transaction_in_progress) {
            return;
        }
        $this->rollback();
    }

    /***Queryexectiontimetrackingswitch**@parambool$enabledEnableexecutiontimetracking*@paramstring$stripPrefixPrefixtostripfromthepathinexeclog**@returnMysqliDb*/
    public function setTrace($enabled, $stripPrefix = null)
    {
        $this->traceEnabled = $enabled;
        $this->traceStripPrefix = $stripPrefix;
        return $this;
    }

    /***GetwhereandwhatfunctionwascalledforquerystoredinMysqliDB->trace**@returnstringwithinformation*/
    private function _traceGetCaller()
    {
        $dd = debug_backtrace();
        $caller = next($dd);
        while (isset($caller) && $caller["file"] == __FILE__) {
            $caller = next($dd);
        }

        return __CLASS__ . "->" . $caller["function"] . "() >>  file \"" .
            str_replace($this->traceStripPrefix, '', $caller["file"]) . "\" line #" . $caller["line"] . " ";
    }

    /***Methodtocheckifneededtableiscreated**@paramarray$tablesTablenameoranArrayoftablenamestocheck**@returnboolTrueiftableexists*/
    public function tableExists($tables)
    {
        $tables = !is_array($tables) ? Array($tables) : $tables;
        $count = count($tables);
        if ($count == 0) {
            return false;
        }

        foreach ($tables as $i => $value)
            $tables[$i] = self::$prefix . $value;
        $this->where('table_schema', $this->db);
        $this->where('table_name', $tables, 'in');
        $this->get('information_schema.tables', $count);
        return $this->count == $count;
    }

    /***Returnresultasanassociativearraywith$idFieldfieldvalueusedasarecordkey**ArrayReturnsanarray($k=>$v)ifget("param1,param2"),array($k=>array($v,$v))otherwise**@paramstring$idFieldfieldnametouseforamappedelementkey**@returnMysqliDb*/
    public function map($idField)
    {
        $this->_mapKey = $idField;
        return $this;
    }

    /***Paginationwrapertoget()**@accesspublic*@paramstring$tableThenameofthedatabasetabletoworkwith*@paramint$pagePagenumber*@paramarray|string$fieldsArrayorcomaseparatedlistoffieldstofetch*@returnarray*/
    public function paginate ($table, $page, $fields = null) {
        $offset = $this->pageLimit * ($page - 1);
        $res = $this->withTotalCount()->get ($table, Array ($offset, $this->pageLimit), $fields);
        $this->totalPages = ceil($this->totalCount / $this->pageLimit);
        return $res;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ANDWHEREstatementsforthejointableonpartoftheSQLquery**@uses$dbWrapper->joinWhere('useru','uid',7)->where('useru','utitle','MyTitle');**@paramstring$whereJoinThenameofthetablefollowedbyitsprefix*@paramstring$wherePropThenameofthedatabasefield*@parammixed$whereValueThevalueofthedatabasefield**@returndbWrapper*/
    public function joinWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->_joinAnd[$whereJoin][] = Array ($cond, $whereProp, $operator, $whereValue);
        return $this;
    }

    /***Thismethodallowsyoutospecifymultiple(methodchainingoptional)ORWHEREstatementsforthejointableonpartoftheSQLquery**@uses$dbWrapper->joinWhere('useru','uid',7)->where('useru','utitle','MyTitle');**@paramstring$whereJoinThenameofthetablefollowedbyitsprefix*@paramstring$wherePropThenameofthedatabasefield*@parammixed$whereValueThevalueofthedatabasefield**@returndbWrapper*/
    public function joinOrWhere($whereJoin, $whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        return $this->joinWhere($whereJoin, $whereProp, $whereValue, $operator, 'OR');
    }

    /***AbstractionmethodthatwillbuildanJOINpartofthequery*/
    protected function _buildJoin () {
        if (empty ($this->_join))
            return;

        foreach ($this->_join as $data) {
            list ($joinType,  $joinTable, $joinCondition) = $data;

            if (is_object ($joinTable))
                $joinStr = $this->_buildPair ("", $joinTable);
            else
                $joinStr = $joinTable;

            $this->_query .= " " . $joinType. " JOIN " . $joinStr ." on " . $joinCondition;

            //Addjoinandquery
            if (!empty($this->_joinAnd) && isset($this->_joinAnd[$joinStr])) {
                foreach($this->_joinAnd[$joinStr] as $join_and_cond) {
                    list ($concat, $varName, $operator, $val) = $join_and_cond;
                    $this->_query .= " " . $concat ." " . $varName;
                    $this->conditionToSql($operator, $val);
                }
            }
        }
    }

    /***Convertaconditionandvalueintothesqlstring*@paramString$operatorThewhereconstraintoperator*@paramString$valThewhereconstraintvalue*/
    private function conditionToSql($operator, $val) {
        switch (strtolower ($operator)) {
            case 'not in':
            case 'in':
                $comparison = ' ' . $operator. ' (';
                if (is_object ($val)) {
                    $comparison .= $this->_buildPair ("", $val);
                } else {
                    foreach ($val as $v) {
                        $comparison .= ' ?,';
                        $this->_bindParam ($v);
                    }
                }
                $this->_query .= rtrim($comparison, ',').' ) ';
                break;
            case 'not between':
            case 'between':
                $this->_query .= " $operator ? AND ? ";
                $this->_bindParams ($val);
                break;
            case 'not exists':
            case 'exists':
                $this->_query.= $operator . $this->_buildPair ("", $val);
                break;
            default:
                if (is_array ($val))
                    $this->_bindParams ($val);
                else if ($val === null)
                    $this->_query .= $operator . " NULL";
                else if ($val != 'DBNULL' || $val == '0')
                    $this->_query .= $this->_buildPair ($operator, $val);
        }
    }
}

//ENDclass
