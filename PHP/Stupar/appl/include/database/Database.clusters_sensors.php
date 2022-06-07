<?php

namespace Db\Table\DataTypes
{
    class clusters_sensors_t
    {
        public ?int $id = null;
        public string $serial_number;
        public string $board_version;
        public string $sw_version;
        public string $cluster_id;
        public string $tags;
    }
}

namespace Db\Table
{
    use Db\Database;

    class clusters_sensors extends Database
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