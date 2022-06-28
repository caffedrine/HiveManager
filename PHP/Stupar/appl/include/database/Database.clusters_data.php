<?php

namespace Db\Table\DataTypes
{
    class clusters_data_t
    {
        public ?int $id = null;
        public string $datetime;
        public int $cluster_sensor_id;
        public int $volts_mv;
        public float $temperature_deg;
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

        public function GetBySensorId(int $sensor_id)
        {
            return $this->GetRecordsByKeyVal("cluster_sensor_id", $sensor_id);
        }

        public function GetAllBySensorID_HourlyAvg(int $sensor_id, string $past_date_limit, $limit = null): array
        {
            $CustomQuery = "";
            $CustomQuery .= "SELECT CAST(AVG(`temperature_deg`) AS INT), CAST(AVG(`humidity_rh`) AS INT), CAST(AVG(`volts_mv`) AS INT), DATE_FORMAT(`datetime`, '%Y-%m-%d %H:00:00') FROM `" . $this->GetTableName() . "`";
            $CustomQuery .= " WHERE (`cluster_sensor_id` = :sensor_id AND `datetime` >= :past_limit)";
            # Order by date
            $CustomQuery .= " GROUP BY DATE(`datetime`), HOUR(`datetime`) ORDER BY `datetime` ASC";

            if (!empty($limit) && is_numeric($limit) && ($limit > 0))
            {
                $CustomQuery .= " LIMIT $limit";
            }

            $CustomQueryParams = array
            (
                'sensor_id' => $sensor_id,
                'past_limit' => $past_date_limit
            );

            $results = $this->ExecuteCustomQuery($CustomQuery, $CustomQueryParams);

            if (!empty($results))
            {
                $retArr = array();
                foreach ($results as $resultObj)
                {
                    $res = new \ArrayObject();
                    $res->temperature_deg = $resultObj[0];
                    $res->humidity_rh = $resultObj[1];
                    $res->volts_mv = $resultObj[2];
                    $res->datetime = $resultObj[3];
                    $retArr[] = $res;
                }

                //var_dump($retArr);

                return $retArr;
            }

            return array();
        }

        public function GetAllBySensorID_DailyAvg(int $sensor_id, string $past_date_limit, $limit = null): array
        {
            $CustomQuery = "";
            $CustomQuery .= "SELECT CAST(AVG(`temperature_deg`) AS INT), CAST(AVG(`humidity_rh`) AS INT), CAST(AVG(`volts_mv`) AS INT), DATE_FORMAT(`datetime`, '%Y-%m-%d') FROM `" . $this->GetTableName() . "`";
            $CustomQuery .= " WHERE (`cluster_sensor_id` = :sensor_id AND `datetime` >= :past_limit)";
            # Order by date
            $CustomQuery .= " GROUP BY DATE(`datetime`), DAY(`datetime`) ORDER BY `datetime` ASC";

            if (!empty($limit) && is_numeric($limit) && ($limit > 0))
            {
                $CustomQuery .= " LIMIT $limit";
            }

            $CustomQueryParams = array
            (
                'sensor_id' => $sensor_id,
                'past_limit' => $past_date_limit
            );

            $results = $this->ExecuteCustomQuery($CustomQuery, $CustomQueryParams);

            if (!empty($results))
            {
                $retArr = array();
                foreach ($results as $resultObj)
                {
                    $res = new \ArrayObject();
                    $res->temperature_deg = $resultObj[0];
                    $res->humidity_rh = $resultObj[1];
                    $res->volts_mv = $resultObj[2];
                    $res->datetime = $resultObj[3];
                    $retArr[] = $res;
                }

                //var_dump($retArr);

                return $retArr;
            }

            return array();
        }

        public function GetAllBySensorID_MonthlyAvg(int $sensor_id, string $past_date_limit, $limit = null): array
        {
            $CustomQuery = "";
            $CustomQuery .= "SELECT CAST(AVG(`temperature_deg`) AS INT), CAST(AVG(`humidity_rh`) AS INT), CAST(AVG(`volts_mv`) AS INT), DATE_FORMAT(`datetime`, '%b %Y') FROM `" . $this->GetTableName() . "`";
            $CustomQuery .= " WHERE (`cluster_sensor_id` = :sensor_id AND `datetime` >= :past_limit)";
            # Order by date
            $CustomQuery .= " GROUP BY DATE(DATE_FORMAT(`datetime`, '%Y-%m-1')), MONTH(`datetime`) ORDER BY `datetime` ASC";

            if (!empty($limit) && is_numeric($limit) && ($limit > 0))
            {
                $CustomQuery .= " LIMIT $limit";
            }

            $CustomQueryParams = array
            (
                'sensor_id' => $sensor_id,
                'past_limit' => $past_date_limit
            );

            $results = $this->ExecuteCustomQuery($CustomQuery, $CustomQueryParams);

            if (!empty($results))
            {
                $retArr = array();
                foreach ($results as $resultObj)
                {
                    $res = new \ArrayObject();
                    $res->temperature_deg = $resultObj[0];
                    $res->humidity_rh = $resultObj[1];
                    $res->volts_mv = $resultObj[2];
                    $res->datetime = $resultObj[3];
                    $retArr[] = $res;
                }

                //var_dump($retArr);

                return $retArr;
            }

            return array();
        }
    }
}