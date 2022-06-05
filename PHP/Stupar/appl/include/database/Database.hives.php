<?php

namespace Db\Table\DataTypes
{
    class hives_t
    {
        public ?int $id = null;
        public string $serial_number = "";
        public int $cluster_id;
        public float $coords_lat = 0;
        public float $coords_long = 0;
        public string $board_version = "";
        public int $owner_uid = -1;
        public string $tags = "";
    }
}

namespace Db\Table
{
    use Db\Database;

    class hives extends Database
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