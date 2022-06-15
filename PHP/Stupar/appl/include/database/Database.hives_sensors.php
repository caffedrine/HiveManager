<?php

namespace Db\Table\DataTypes
{
    class hives_sensors_t
    {
        public ?int $id = null;
        public string $serial_number;
        public string $board_version;
        public string $sw_version;
        public int $cluster_id;
        public string $tags = "";
    }
}

namespace Db\Table
{
    use Db\Database;

    class hives_sensors extends Database
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

        public function GetBySerialNumber(string $serial_number): ?DataTypes\hives_sensors_t
        {
            $results = $this->GetRecordsByKeyVal("serial_number", $serial_number, 1);
            return ((!empty($results) && is_array($results)) ? ($results[0]) : (null));
        }
    }
}