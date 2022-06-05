<?php

namespace Db\Table\DataTypes
{
    class hives_data_t
    {
        public ?int $id = null;
        public int $hive_id;
        public string $datetime;
        public int $voltage_mv;
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
    }
}