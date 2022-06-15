<?php

namespace Db\Table\DataTypes
{
    class hives_data_t
    {
        public ?int $id = null;
        public string $datetime;
        public int $hive_sensor_id;
        public int $volts_mv;
        public int $weight_gm;
    }
}

namespace Db\Table
{
    use Db\Database;

    class hives_data extends Database
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

        public function GetBySensorID(int $sensor_id)
        {
            return $this->GetRecordsByKeyVal("hive_sensor_id", $sensor_id);
        }
    }
}