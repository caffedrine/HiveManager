<?php

namespace Db\Table\DataTypes
{
    class hive_clusters_t
    {
        public ?int $id = null;
        public string $title;
        public string $description;
    }
}

namespace Db\Table
{
    use Db\Database;

    class hive_clusters extends Database
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