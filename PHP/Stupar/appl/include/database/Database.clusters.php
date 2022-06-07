<?php

namespace Db\Table\DataTypes
{
    class clusters_t
    {
        public ?int $id = null;
        public string $title;
        public string $description;
        public float $coord_lat;
        public float $coord_long;
        public int $owner_uid;
    }
}

namespace Db\Table
{
    use Db\Database;

    class clusters extends Database
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