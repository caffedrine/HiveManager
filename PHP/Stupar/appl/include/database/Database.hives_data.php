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

        public function GetAllBySensorID(int $sensor_id, string $past_date_limit = null, $limit = null)
        {
            $Clauses = array
            (
                array('key' => 'hive_sensor_id',
                        'comp' => '=',
                        'val' => $sensor_id),
            );

            if( !empty($past_date_limit) )
                $Clauses[] = array('key' => 'datetime',
                    'comp' => '>=',
                    'val' => $past_date_limit);

            return $this->GetRecordsByKeysCompsVals($Clauses, $limit,);
        }

        public function GetAllBySensorID_HourlyAvg(int $sensor_id, string $past_date_limit, $limit = null)
        {
            $CustomQuery = "";
            $CustomQuery .= "SELECT CAST(AVG(`weight_gm`) AS INT), CAST(AVG(`volts_mv`) AS INT), DATE_FORMAT(`datetime`, '%Y-%m-%d %H:00:00') FROM `" . $this->GetTableName() . "`";
            $CustomQuery .= " WHERE (`hive_sensor_id` = :sensor_id AND `datetime` >= :past_limit)";
            # Order by date
            $CustomQuery .= " GROUP BY HOUR(`datetime`) ORDER BY `datetime` ASC";

            if (!empty($limit) && is_numeric($limit) && ($limit > 0))
            {
                $CustomQuery .= " LIMIT $limit";
            }

            $CustomQueryParams = array
            (
                'sensor_id' => $sensor_id,
                'past_limit' => $past_date_limit
            );

            var_dump($CustomQuery);

            $results = $this->ExecuteCustomQuery($CustomQuery, $CustomQueryParams);

            if (!empty($results))
            {
                $retArr = array();
                foreach ($results as $resultObj)
                {
                    $res = new \ArrayObject();
                    $res->weight_gm = $resultObj[0];
                    $res->volts_mv = $resultObj[1];
                    $res->datetime = $resultObj[2];
                    $retArr[] = $res;
                }

                //var_dump($retArr);

                return $retArr;
            }

            return array();
        }

        public function GetAllBySensorID_6hAvg(int $sensor_id, string $past_date_limit = null, $limit = null)
        {
        }
    }
}