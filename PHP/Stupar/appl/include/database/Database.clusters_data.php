<?php

namespace Db\Table\DataTypes
{
    class clusters_data_t
    {
        public ?int $id = null;
        public string $datetime;
        public int $cluster_sensor_id;
        public int $voltage_mv;
        public int $humidity_rh;
    }
}

namespace Db\Table
{
    use Db\Database;

    class clusters_data extends Database
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
    }
}