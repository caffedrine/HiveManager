<?php
/** @noinspection PhpUnused */
/** @noinspection SqlNoDataSourceInspection */
/** @noinspection DuplicatedCode */

namespace
{
    if (!defined("INTERNAL_INCLUSION")) require_once "./core/config.php";
    /* Includes */
    require_once CORE_GLOB_CONFIG_PATH . "/db_cfg.php"; # Database credentials
}

namespace Db
{
    use Exception;
    use PDO;
    use PDOException;
    use PDOStatement;
    use ReflectionClass;
    use RuntimeException;

    class Database
    {
        private static $instance = null;
        protected ?PDO $pdo = null;

        private int $CallStartTimestampNanoseconds;

        private function __construct()
        {
            $GLOBALS['DB_STATS_REQUESTS_COUTNTER'] = 0;
            $GLOBALS['DB_STATS_LOG_ID'] = -1;

            try
            {
                $this->pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

                // set the PDO error mode to exception
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e)
            {
                exit("Down for maintenance (0x90ff001a). Please come back later");
            }
        }

        protected static function getInstance(): self
        {
            if( (!self::$instance) || (!(self::$instance instanceof self)))
            {
                self::$instance = new Database();
            }

            return self::$instance;
        }

        protected static function BuildSqlQueryStrFromArray(string $key, array $array_vals, string $keyValOperator, string $operatorElements): string
        {
            $arr_count = count($array_vals);
            if($arr_count <= 0 )
            {
                return "1=1";
            }

            $query_str = "";
            foreach ( $array_vals as $i => $val)
            {
                $query_str .= "(`$key` $keyValOperator '$val')";
                if( $i < ($arr_count - 1) )
                {
                    $query_str .= " $operatorElements ";
                }
            }

            return $query_str;
        }

        protected static function ObjectStr($object)
        {
            if ($object === null)
            {
                return "null";
            }

            return print_r($object, true);
        }

        /**
         * @param $obj
         * @return array
         * @throws Exception
         */
        protected static function VariablesNamesArr($obj): array
        {
            $result = array();
            try
            {
                foreach ((new ReflectionClass($obj))->getProperties() as $var)
                {
                    $result[] = $var->getName();
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            return $result;
        }

        /**
         * @param $obj
         * @return false|string
         * @throws Exception
         */
        protected static function GetAllRowsNamesAsStringForSqlQuery($obj)
        {
            if (!is_object($obj))
            {
                throw new RuntimeException("GetAllRowsNamesAsStringForSqlQuery(...): an actual object must be passed!");
            }

            try
            {
                $table_rows_str = "";
                foreach (self::VariablesNamesArr($obj) as $var)
                {
                    $table_rows_str .= ("`" . $var . "`, ");
                }
                if (strlen($table_rows_str) > 2)
                {
                    $table_rows_str = substr($table_rows_str, 0, -2);
                }
                return $table_rows_str;
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }

        /**
         * @param $obj
         * @return false|string
         * @throws Exception
         */
        protected static function GetAllRowsNamesAsStringForInsert($obj)
        {
            if (!is_object($obj))
            {
                throw new RuntimeException("GetAllRowsNamesAsStringForInsert(...): an actual object must be passed!");
            }

            try
            {
                $table_rows_str = "";
                foreach (self::VariablesNamesArr($obj) as $var)
                {
                    $table_rows_str .= (":" . $var . ", ");
                }
                if (strlen($table_rows_str) > 2)
                {
                    $table_rows_str = substr($table_rows_str, 0, -2);
                }
                return $table_rows_str;
            }
            catch (Exception $exception)
            {
                throw $exception;
            }
        }

        /**
         * @param $obj
         * @return false|string
         * @throws Exception
         */
        protected static function GetAllRowsNamesAsStringForUpdate($obj)
        {
            if (!is_object($obj))
            {
                throw new RuntimeException("GetAllRowsNamesAsStringForUpdate(...): an actual object must be passed!");
            }

            try
            {
                $table_rows_str = "";
                foreach (self::VariablesNamesArr($obj) as $var)
                {
                    $table_rows_str .= ("`$var`=:$var, ");
                }
                if (strlen($table_rows_str) > 2)
                {
                    $table_rows_str = substr($table_rows_str, 0, -2);
                }
                return $table_rows_str;
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }

        /**
         * @param $sql_query
         * @param $dataObj
         * @return bool|PDOStatement
         * @throws Exception
         */
        protected static function GetPdoStmt($sql_query, $dataObj)
        {
            if (empty($sql_query) || empty($dataObj))
            {
                throw new RuntimeException("Empty sql_query or dataObj passed!");
            }

            try
            {
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);
                foreach (self::VariablesNamesArr($dataObj) as $rowName)
                {
                    $pdo_stmt->bindValue(":$rowName", $dataObj->{(string)$rowName});
                }
                return $pdo_stmt;
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }

        /**
         * @param $fetchAssocObj
         * @param $ObjectType
         * @return mixed
         * @throws Exception
         */
        protected static function QueryResultToObject($fetchAssocObj, $ObjectType)
        {
            if (empty($fetchAssocObj) || empty($ObjectType))
            {
                throw new RuntimeException("QueryResultToObject(...): Parameters passed cannot be null");
            }

            try
            {
                $result = new $ObjectType;
                foreach (self::VariablesNamesArr($result) as $row)
                {
                    $result->{(string)$row} = $fetchAssocObj[(string)$row];
                }
                return $result;
            }
            catch (Exception $e)
            {
                throw $e;
            }
        }

        protected function CallHook_BeforeQuery(): void
        {
            $this->CallStartTimestampNanoseconds = hrtime(true);
        }

        protected function CallHook_AfterQuery(string $query_string): void
        {
            $time_elapsed_us = ((hrtime(true) - $this->CallStartTimestampNanoseconds) / 1000);
            $dbg_str = "<br>[{$GLOBALS['DB_STATS_REQUESTS_COUTNTER']}]\t[". number_format((float)$time_elapsed_us/1000, 2) ." ms]</br>\t{$query_string}";
            $GLOBALS['DB_STATS_REQUESTS_COUTNTER']++;

            # If a record was already created, only update it by adding
            $sql_query = "";
            try
            {
                if( $GLOBALS['DB_STATS_LOG_ID'] === -1 ) # First time, log shall be inserted. The description only shall be updated
                {
                    # Build up log record
                    $log = new Table\DataTypes\logger_system_t();
                    $log->id = null;
                    $log->date = date("Y-m-d H:i:s");
                    $log->site_section = "internals";
                    $log->component = "DATABASE";
                    $log->category = "STAT";
                    $log->gravity = "DEBUG";
                    $log->tag = "DB STAT";
                    $log->description = $dbg_str;

                    # SQL query
                    $sql_query = 'INSERT INTO `' . ("core_logger_system") . '` (' . self::GetAllRowsNamesAsStringForSqlQuery($log) . ')';
                    $sql_query .= 'VALUES (' . self::GetAllRowsNamesAsStringForInsert($log) . ')';

                    # Bind params to SQL statement
                    $sql_stmt = self::GetPdoStmt($sql_query, $log);

                    # Execution
                    $sql_stmt->execute();

                    $GLOBALS['DB_STATS_LOG_ID'] = (int)(self::getInstance()->pdo->lastInsertId());
                }
                else # Log already created. Only update it
                {
                    # SQL query
                    $sql_query = 'UPDATE `' . ("core_logger_system") . "` SET `description` = CONCAT(`description`, :description) WHERE `id` = :id";

                    # Prepare statement for execution
                    $sql_stmt = self::getInstance()->pdo->prepare($sql_query);

                    # Bind params to SQL statement
                    $sql_stmt->bindValue(":description", "\n" . $dbg_str);
                    $sql_stmt->bindValue(":id", $GLOBALS['DB_STATS_LOG_ID']);

                    # Execution
                    $sql_stmt->execute();
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
        }

        /* BASIC DB OPERATIONS */

        public function Add($TableObject): ?int
        {
            $result = null;
            $sql_query = null;

            try
            {
                # SQL query
                $sql_query = sprintf("INSERT INTO `%s` (%s)", $this->GetTableName(), self::GetAllRowsNamesAsStringForSqlQuery($TableObject));
                $sql_query .= 'VALUES (' . self::GetAllRowsNamesAsStringForInsert($TableObject) . ')';

                # Bind params to SQL statement
                $sql_stmt = self::GetPdoStmt($sql_query, $TableObject);
                
                # Execution
                $result = $this->ExecutePdoStmt($sql_stmt);

                # Return ID of inserted record
                if( $result )
                {
                    $result = (int)(self::getInstance()->pdo->lastInsertId());
                }
                else
                {
                    $result = (int)($result);
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        public function DeleteById($id): ?int
        {
            $result = null;
            $sql_query = null;

            try
            {
                # SQL Query
                $sql_query = sprintf("DELETE FROM `%s` WHERE `id` = :id", $this->GetTableName());

                # Prepare query for execution
                $sql = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $sql->bindValue(':id', $id);

                # Execution
                if( $this->ExecutePdoStmt($sql) )
                {
                    $result = $sql->rowCount();
                }

            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        protected function DeleteAllRecords($limit = null, $order_by = null, $order = "ASC"): ?int
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query
                $ord = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit))?("LIMIT $limit"):(""));

                $sql_query = sprintf("DELETE FROM `%s` %s %s", $this->GetTableName(), $ord, $lim);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Execution
                if( $this->ExecutePdoStmt($pdo_stmt) )
                {
                    $result = $pdo_stmt->rowCount();
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        protected function DeleteAllByKeyVal($key, $val, $limit = null, $comparator = '=', $order_by = null, $order = "ASC"): ?int
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query
                $ord = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit))?("LIMIT $limit"):(""));

                if( empty($comparator) ) $comparator = "=";

                $sql_query = sprintf("DELETE FROM `%s` WHERE `%s` %s :val %s %s", $this->GetTableName(), $key, $comparator, $ord, $lim);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $pdo_stmt->bindValue(':val', $val);

                # Execution
                if( $this->ExecutePdoStmt($pdo_stmt) )
                {
                    $result = $pdo_stmt->rowCount();
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        protected function DeleteAllByKeysCompsVals(array $keysCompsValsArr, $limit = null, $order_by = null, $order = "ASC"): ?int
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Validate array containing keys and values
                if( empty($keysCompsValsArr) )
                {
                    throw new RuntimeException("Array with keys, comparators and vals cannot be empty!");
                }

                # Build query conditions
                $conditions = "";
                foreach( $keysCompsValsArr as $element )
                {
                    if(!isset($element['key'], $element['comp'], $element['val']))
                    {
                        throw new RuntimeException("Invalid key comp val array provided!");
                    }
                    $conditions .= sprintf("`%s` %s :%s AND ", $element['key'], $element['comp'], $element['key']);
                }
                # Remove last "AND"
                $conditions = substr($conditions, 0, -4);

                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));

                $sql_query = sprintf("DELETE FROM `%s` WHERE (%s) %s %s", $this->GetTableName(), $conditions, $order, $lim);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                foreach( $keysCompsValsArr as $element )
                {
                    $pdo_stmt->bindValue(":". $element['key'], $element['val']);
                }

                # Execution
                if( $this->ExecutePdoStmt($pdo_stmt) )
                {
                    $result = $pdo_stmt->rowCount();
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        public function RecordExists($id): ?bool
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query
                $sql_query = sprintf("SELECT COUNT(*) FROM `%s` WHERE `id` = :id LIMIT 1", $this->GetTableName());

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $pdo_stmt->bindValue(':id', $id);

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);

                $query_result = $pdo_stmt->fetchColumn();
                if( !empty($query_result) )
                {
                    $result = true;
                }
                else
                {
                    $result = false;
                }

            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        public function UpdateRecord($TableObject): ?bool
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query build - multiple tables can be selected
                $sql_query = 'UPDATE `' . $this->GetTableName() . '` SET ';
                $sql_query .= self::GetAllRowsNamesAsStringForUpdate($TableObject) . ' ';
                $sql_query .= 'WHERE `id`=:id2';

                # Get PDO Statement
                $pdo_stmt = self::GetPdoStmt($sql_query, $TableObject);

                # Additional bindings
                $pdo_stmt->bindValue(':id2', $TableObject->id);

                # Execution
                $result = $this->ExecutePdoStmt($pdo_stmt);
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        public function GetRecordsCount()
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query
                $sql_query = sprintf("SELECT COUNT(*) FROM `%s`", $this->GetTableName());

                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                $this->ExecutePdoStmt($pdo_stmt);

                $result = $pdo_stmt->fetchColumn();

            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $result;
        }

        public function GetRecordById($id)
        {
            $result = null;
            $sql_query = null;

            try
            {
                # Query
                $sql_query = sprintf("SELECT * FROM `%s` WHERE `id` = :id LIMIT 1", $this->GetTableName());

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $pdo_stmt->bindValue(':id', $id);
                
                $this->ExecutePdoStmt($pdo_stmt);

                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);

                $query_result = $pdo_stmt->fetch();
                if (!empty($query_result))
                {
                    $result = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
                else
                {
                    $result = null;
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $result;
        }

        protected function GetRecordsByKeyVal($key, $val, $limit = null, $comparator = '=', $order_by = null, $order = "ASC", $offset = 0): ?array
        {
            $results = array();
            $sql_query = null;

            try
            {
                # Query
                $ord = ( ($order_by!==null)?("ORDER BY $order_by $order"):("") );
                $lim = (($limit!==null && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");
                if( empty($comparator) ) $comparator = "=";

                $sql_query = sprintf("SELECT * FROM `%s` WHERE `%s` %s :val %s %s %s", $this->GetTableName(), $key, $comparator, $ord, $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $pdo_stmt->bindValue(':val', $val);

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetRecordsByKeysCompsVals(array $keysCompsValsArr, $limit = null, $order_by = null, $order = "ASC", $offset = 0): ?array
        {
            $results = array();

            $sql_query = null;
            try
            {
                # Validate array containing keys and values
                if( empty($keysCompsValsArr) )
                {
                    throw new RuntimeException("Array with keys, comparators and vals cannot be empty!");
                }

                # Build query conditions
                $conditions = "";
                foreach( $keysCompsValsArr as $element )
                {
                    if(!isset($element['key'], $element['comp'], $element['val']))
                    {
                        throw new RuntimeException("Invalid key comp val array provided!");
                    }
                    $conditions .= sprintf("`%s` %s :%s AND ", $element['key'], $element['comp'], $element['key']);
                }
                # Remove last "AND"
                $conditions = substr($conditions, 0, -4);

                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT * FROM `%s` WHERE %s %s %s %s", $this->GetTableName(), $conditions , $order, $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                foreach( $keysCompsValsArr as $element )
                {
                    $pdo_stmt->bindValue(":". $element['key'], $element['val']);
                }

                # Execution
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);
                $this->ExecutePdoStmt($pdo_stmt);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetRecordsInRanges(array $Ranges, array $keysCompsValsArr, $limit = null, $order_by = null, $order = "ASC", $offset = 0): ?array
        {
            $results = array();

            $sql_query = null;
            try
            {
                # Validate array containing keys and values
                $conditions = "";
                if( !empty($keysCompsValsArr) )
                {
                    # Build query conditions
                    $conditions = "";
                    foreach( $keysCompsValsArr as $element )
                    {
                        if(!isset($element['key'], $element['comp'], $element['val']))
                        {
                            throw new RuntimeException("Invalid key comp val array provided!");
                        }
                        $conditions .= sprintf("(`%s` %s :%s) AND ", $element['key'], $element['comp'], $element['key']);
                    }
                }

                # Ranges needs to be provided
                if( empty($Ranges) )
                {
                    throw new RuntimeException("No ranges were provided");
                }

                # Add ranges to conditions list
                foreach ($Ranges as $range)
                {
                    if( !isset($range['element_name'], $range['from'], $range['to']) )
                    {
                        throw new RuntimeException("Invalid ranges array provided!");
                    }
                    $conditions .= sprintf(" (`%s` >= :%s AND  `%s` <= :%s)", $range['element_name'], $range['element_name']."_from", $range['element_name'], $range['element_name']. "_to");
                }

                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT * FROM `%s` WHERE (%s) %s %s %s", $this->GetTableName(), $conditions , $order, $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params binding
                if( !empty($keysCompsValsArr) )
                {
                    foreach ($keysCompsValsArr as $element)
                    {
                        $pdo_stmt->bindValue(":" . $element['key'], $element['val']);
                    }
                }

                if( !empty($Ranges) )
                {
                    foreach ($Ranges as $range)
                    {
                        $pdo_stmt->bindValue(":" . $range['element_name']. "_from", $range['from']);
                        $pdo_stmt->bindValue(":" . $range['element_name']. "_to", $range['to']);
                    }
                }

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetAllRecords($limit = 0, $offset = null, $order_by = null, $order = "DESC"): ?array
        {
            $results = array();
            $sql_query = null;

            try
            {
                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offset =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT * FROM `%s` %s %s %s", $this->GetTableName(), $order, $lim, $offset);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetAllDistinctRecords($column_name, $limit = 0, $offset = null): ?array
        {
            $results = array();
            $sql_query = null;

            try
            {
                # Query
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offset =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = "SELECT `{$column_name}`, COUNT({$column_name}) FROM `{$this->GetTableName()}` {$limit} {$offset} GROUP BY `{$column_name}` ORDER BY COUNT({$column_name}) DESC";

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
//                $pdo_stmt->bindValue(':column_name', $column_name);
//                $pdo_stmt->bindValue(':table_name', $this->GetTableName());

                # Execution
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);
                $this->ExecutePdoStmt($pdo_stmt);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[$query_result[$column_name]] = $query_result["COUNT($column_name)"];
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $results;
        }

        public function GetAllRecordsOrdered($limit = 0, $offset = null): ?array
        {
            $results = array();
            $sql_query = null;

            try
            {
                $order_by = "id";
                $order = "DESC";

                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offset =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT * FROM `%s` %s %s %s", $this->GetTableName(), $order, $lim, $offset);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Execution
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);
                $this->ExecutePdoStmt($pdo_stmt);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetRecordsCountByKeyVal($key, $val, $comp = "=", $limit = 0)
        {
            $result = null;
            $sql_query = null;

            try
            {
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));

                # Query
                $sql_query = sprintf("SELECT COUNT(*) FROM `%s` WHERE `%s` %s :val %s", $this->GetTableName(), $key, $comp, $lim);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                $pdo_stmt->bindValue(':val', $val);

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);

                $result = $pdo_stmt->fetchColumn();
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $result;
        }

        protected function GetRecordsCountByKeysCompsVals(array $keysCompsValsArr, $limit = null, $offset = 0)
        {
            $results = null;
            $sql_query = null;

            try
            {
                # Validate array containing keys and values
                if( empty($keysCompsValsArr) )
                {
                    throw new RuntimeException("Array with keys, comparators and vals cannot be empty!");
                }

                # Build query conditions
                $conditions = "";
                foreach( $keysCompsValsArr as $element )
                {
                    if(!isset($element['key'], $element['comp'], $element['val']))
                    {
                        throw new RuntimeException("Invalid key comp val array provided!");
                    }
                    $conditions .= sprintf("`%s` %s :%s AND ", $element['key'], $element['comp'], $element['key']);
                }
                # Remove last "AND"
                $conditions = substr($conditions, 0, -4);

                # Query
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT COUNT(*) FROM `%s` WHERE %s %s %s", $this->GetTableName(), $conditions, $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                foreach( $keysCompsValsArr as $element )
                {
                    $pdo_stmt->bindValue(":". $element['key'], $element['val']);
                }

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $results = $pdo_stmt->fetchColumn();
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $results;
        }

        protected function GetRecordsCountInRanges(array $Ranges, array $keysCompsValsArr, $limit = null, $offset = 0)
        {
            $results = null;
            $sql_query = null;

            try
            {
                # Validate array containing keys and values
                $conditions = "";
                if( !empty($keysCompsValsArr) )
                {
                    # Build query conditions
                    $conditions = "";
                    foreach( $keysCompsValsArr as $element )
                    {
                        if(!isset($element['key'], $element['comp'], $element['val']))
                        {
                            throw new RuntimeException("Invalid key comp val array provided!");
                        }
                        $conditions .= sprintf("(`%s` %s :%s) AND ", $element['key'], $element['comp'], $element['key']);
                    }
                }

                # Ranges needs to be provided
                if( empty($Ranges) )
                {
                    throw new RuntimeException("No ranges were provided");
                }

                # Add ranges to conditions list
                foreach ($Ranges as $range)
                {
                    if( !isset($range['element_name'], $range['from'], $range['to']) )
                    {
                        throw new RuntimeException("Invalid ranges array provided!");
                    }
                    $conditions .= sprintf(" (`%s` >= :%s AND  `%s` <= :%s)", $range['element_name'], $range['element_name']."_from", $range['element_name'], $range['element_name']. "_to");
                }

                # Query
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("SELECT COUNT(*) FROM `%s` WHERE (%s) %s %s", $this->GetTableName(), $conditions , $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params binding
                if( !empty($keysCompsValsArr) )
                {
                    foreach ($keysCompsValsArr as $element)
                    {
                        $pdo_stmt->bindValue(":" . $element['key'], $element['val']);
                    }
                }

                if( !empty($Ranges) )
                {
                    foreach ($Ranges as $range)
                    {
                        $pdo_stmt->bindValue(":" . $range['element_name']. "_from", $range['from']);
                        $pdo_stmt->bindValue(":" . $range['element_name']. "_to", $range['to']);
                    }
                }

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $results = $pdo_stmt->fetchColumn();
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $results;
        }

        protected function UpdateRecordsByKeysCompsVals(array $keysCompsValsArr, array $NewColumns, int $limit, string $order_by = null, string $order = "ASC", int $offset = 0): ?bool
        {
            $results = null;
            $sql_query = null;

            try
            {
                # Validate array containing keys and values
                if( empty($keysCompsValsArr) || empty($NewColumns))
                {
                    throw new RuntimeException("Array with keys, comparators and vals cannot be empty nor new vals array!");
                }

                # Build query conditions
                $conditions = "";
                foreach( $keysCompsValsArr as $element )
                {
                    if(!isset($element['key'], $element['comp'], $element['val']))
                    {
                        throw new RuntimeException("Invalid key comp val array provided!");
                    }
                    $conditions .= sprintf("`%s` %s :%s AND ", $element['key'], $element['comp'], $element['key']);
                }
                # Remove last "AND"
                $conditions = substr($conditions, 0, -4);

                # Build update string for setting new values
                $vals = "";
                foreach ( $NewColumns as $Cols )
                {
                    if (!isset($Cols['name'], $Cols['val']))
                    {
                        throw new RuntimeException("The new values needs to be set in array as: array(name => value,...)");
                    }
                    $vals .= "`" . $Cols['name'] . "` = " . ((isset($Cols['val']))?(":".$Cols['name']):("NULL")) . ", ";
                }
                $vals = substr($vals, 0, -2);

                # Query
                $order = ( (!empty($order_by))?("ORDER BY $order_by $order"):("") );
                $lim = ((!empty($limit) && $limit > 0)?("LIMIT $limit"):(""));
                $offs =  (!empty($lim) && !empty($offset))?("OFFSET $offset"):("");

                $sql_query = sprintf("UPDATE `%s` SET %s WHERE %s %s %s %s", $this->GetTableName(), $vals, $conditions , $order, $lim, $offs);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Params
                # New values
                foreach ( $NewColumns as $Cols )
                {
                    if(!isset($Cols['val']))
                        continue;
                    $pdo_stmt->bindValue(":". $Cols['name'], $Cols['val']);
                }
                # Clauses
                foreach( $keysCompsValsArr as $element )
                {
                    $pdo_stmt->bindValue(":". $element['key'], $element['val']);
                }

                # Execution
                $results = $this->ExecutePdoStmt($pdo_stmt);

            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                    # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                                # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $results;
        }

        public function GetMaxID()
        {
            $result = null;
            $sql_query = null;

            try
            {
                $sql_query = sprintf("SELECT MAX(id) FROM  `%s`", $this->GetTableName());

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                # Execution
                if( $this->ExecutePdoStmt($pdo_stmt) )
                {
                    $res = $pdo_stmt->fetchAll();
                    if(isset($res[0][0]))
                    {
                        $result = $res[0][0];
                    }
                    else
                    {
                        return 0;
                    }
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                    # Log category
                    "WARNING",                                       # Log gravity
                    __FUNCTION__,                                # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $result;
        }

        protected function IncrementField($row_name, $record_id): ?array
        {
            $results = array();
            $sql_query = null;

            try
            {
                # Query
                $sql_query = sprintf("UPDATE `%s` SET `%s` = %s + 1 where `id` = :id", $this->GetTableName(), $row_name, $row_name);

                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($sql_query);

                $pdo_stmt->bindValue(":id", $record_id);

                # Execution
                $this->ExecutePdoStmt($pdo_stmt);
                $pdo_stmt->setFetchMode(PDO::FETCH_ASSOC);

                while($query_result = $pdo_stmt->fetch())
                {
                    $results[] = self::QueryResultToObject($query_result, $this->GetTableDataType());
                }
            }
            catch (Exception $exception)
            {
                $results = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $sql_query,                                             # SQL Query as it is a db request
                    $exception                                              # Exception message, error description
                );
            }
            return $results;
        }

        protected function ExecuteCustomQuery(string $QueryString, array $QueryParamsVals): ?array
        {
            $result = null;
            try
            {
                # Prepare statement for execution
                $pdo_stmt = self::getInstance()->pdo->prepare($QueryString);

                # Params
                foreach ( $QueryParamsVals as $key => $val)
                {
                    $pdo_stmt->bindValue(":$key", $val);
                }

                # Execution
                if ($this->ExecutePdoStmt($pdo_stmt))
                {
                    $result = $pdo_stmt->fetchAll();
                }
            }
            catch (Exception $exception)
            {
                $result = null;
                $this->SystemLog
                (
                    "EXCEPTION",                                   # Log category
                    "WARNING",                                      # Log gravity
                    __FUNCTION__,                               # Function that throws exception/error
                    func_get_args(),                                        # Arguments of that function
                    $QueryString,                                           # SQL Query as it is a db request
                    $exception                                             # Exception message, error description
                );
            }
            return $result;
        }

        protected function SystemLog($category, $gravity, $function_call, $function_args_array, $query, $error_description): void
        {
            # Create the output string:
            $log_description = "<b>TABLE: </b>" . $this->GetTableName() . "\n";
            $log_description .= "<b>CALL</b>: $function_call(...)\n";
            if (!empty($function_args_array))
                $log_description .= "<b>CALL ARGS:</b>\n" . self::ObjectStr($function_args_array);
            else
                $log_description .= "<b>CALL ARGS:</b>null\n";

            if (!empty($query))
                $log_description .= "<b>QUERY</b>:\n$query\n";
            else
                $log_description .= "<b>QUERY</b>:null\n";

            if (!empty($error_description))
                $log_description .= "<b>EXCEPTION/ERROR</b>:\n$error_description\n";
            else
                $log_description .= "<b>EXCEPTION/ERROR</b>:null";

            # Special implementation of this function is required to avoid infinite loop
            $log = new Table\DataTypes\logger_system_t();
            $log->id = null;
            $log->date = date("Y-m-d H:i:s");
            $log->site_section = "internals";
            $log->component = "DATABASE";
            $log->category = $category;
            $log->gravity = $gravity;
            $log->tag = "DB ERROR";
            $log->description = $log_description;

            try
            {
                # SQL query
                $sql_query = 'INSERT INTO `' . ("core_logger_system") . '` (' . self::GetAllRowsNamesAsStringForSqlQuery($log) . ')';
                $sql_query .= 'VALUES (' . self::GetAllRowsNamesAsStringForInsert($log) . ')';

                # Bind params to SQL statement
                $sql_stmt = self::GetPdoStmt($sql_query, $log);

                # Execution
                $sql_stmt->execute();

            }
            catch (Exception $e)
            {
                $log_description = sprintf("[DB Internal Logger] Failed to add new system logs to database (database connection dead or something worst?): \n<b>INPUT</b>:\n%s \n<b>EXCEPTION</b>:\n$e", self::ObjectStr($log));
                /// TODO: If this point is reached, then database is down or something bad happened. Handle this somehow...write to a file?
                if( defined('DEBUG') && (DEBUG === true) )
                {
                    echo  ($log_description);
                }
            }
        }

        public function GetTableName(): ?string
        {
            try
            {
                $parent = new ReflectionClass(get_called_class());
                $parent_name = $parent->getShortName();
                $parent_path = $parent->getFileName();

                if( strpos($parent_path, CORE_BASE_PATH) === 0 )
                    return "core_" . $parent_name;
                else if(  strpos($parent_path, APPL_BASE_PATH) === 0 )
                    return "appl_" . $parent_name;
                else if(  strpos($parent_path, MODULES_BASE_PATH) === 0 )
                    return "mod_" . $parent_name;
                else
                    return "unknown_section_" . $parent_name;
            }
            catch (Exception $e)
            {
                return "unknown_section";
            }
        }

        protected function GetTableDataType(): ?string
        {
            try
            {
                # Each database table shall have an identical class with the same structure used to serialize/deserialize data to/from database
                return 'Db\Table\DataTypes\\' . ((new ReflectionClass(get_called_class()))->getShortName()) . "_t";
            }
            catch (Exception $e)
            {
                return "Db\Table\DataTypes\\" . "unknown";
            }
        }

        private function ExecutePdoStmt(PDOStatement $pdo_stmt): bool
        {
            if( defined('DB_QUERY_STATS') && DB_QUERY_STATS )
            {
                # Call PRE-Execution Hook
                $this->CallHook_BeforeQuery();
            }

            $result =  $pdo_stmt->execute();

            if( defined('DB_QUERY_STATS') && DB_QUERY_STATS )
            {
                # Call POST-Execution Hook
                $this->CallHook_AfterQuery($pdo_stmt->queryString);
            }

            return $result;
        }

    }/*class*/
}/*namespace*/