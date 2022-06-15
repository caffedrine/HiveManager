<?php

namespace Db\Table\DataTypes
{
    class logger_system_t
    {
        public $id = null;
        public $date = "";
        public $site_section = "";
        public $component = "";
        public $category = "";
        public $gravity = "";
        public $tags = "";
        public $description = "";
        public $call_stack = "";
        public $raw_data = "";
    }
}

namespace Db\Table
{
    require_once __DIR__ . "/Database.php";
    use Db\Database;

    class logger_system extends Database
    {
        private static $instance = null;

        /** @noinspection PhpMissingParentConstructorInspection */
        private function __construct()
        {
        }

        public static function getInstance(): self
        {
            if( (!self::$instance) || (!(self::$instance instanceof self)))
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function GetRecordsDateOrdered($limit, $offset = 0)
        {
            return $this->GetAllRecords($limit, $offset, "id", "DESC");
        }

        public function GetRecordsCountOlderThan($logs_before_this_date)
        {
            return $this->GetRecordsCountByKeyVal( "date",  $logs_before_this_date, "<");
        }

        public function DeleteAllOlderThan($logs_before_this_date)
        {
            return $this->DeleteAllByKeyVal("date", $logs_before_this_date, null, "<");
        }

        public function GetLogsCountInTimeInterval($from, $to, $limit = null, $offset = null)
        {
            $Clauses = array();
            $Ranges = array(
                array(
                    "element_name" => "date",
                    "from" => $from,
                    "to" => $to)
            );

            return $this->GetRecordsCountInRanges($Ranges, $Clauses, $limit, $offset);
        }

        public function GetLogsInTimeInterval($from, $to, $limit = null, $offset = null)
        {
            $Clauses = array();
            $Ranges = array(
                array(
                    "element_name" => "date",
                    "from" => $from,
                    "to" => $to),
            );

            return $this->GetRecordsInRanges($Ranges, $Clauses, $limit, null, null, $offset);
        }

        public function DeleteAllOrderedByDateAsc($limit)
        {
            return $this->DeleteAllRecords($limit, "date", "ASC");
        }

        public function GetLogsCountGeneratedAfterDateTime($date_time)
        {
            return $this->GetRecordsCountByKeyVal("date", $date_time, ">=");
        }
    }
}