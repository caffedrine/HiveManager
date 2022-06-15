<?php

require_once "./appl/config.php";
require_once CORE_INCLUDE_PATH  . "/classes/StdTypes.php";
require_once CORE_DATABASE_PATH . "/Database.php";
require_once CORE_DATABASE_PATH . "/Database.logger_system.php";
require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";
require_once APPL_DATABASE_PATH . "/Database.clusters_sensors.php";
require_once APPL_DATABASE_PATH . "/Database.clusters_data.php";
require_once APPL_DATABASE_PATH . "/Database.hives_data.php";

class SensorDataReceiver
{
    private static $instance = null;

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

    public function Receive(string $sensor_serial_number, string $data, string $signature): StdRet
    {
        $ret = new StdRet(true, "");

        # Validate signature

        # Validate sensor ID
        if( $ret->GetStatus() )
        {
            if( !Utils_IsAlphanumeric($sensor_serial_number) || strlen($sensor_serial_number) != 10 )
            {
                $ret->SetStatusMessage(false, "invalid serial number");
            }
        }

        # Validate data
        if( $ret->GetStatus() )
        {
            if( !IsValidBase64Encoded(urldecode($data)) )
            {
                $ret->SetStatusMessage(false, "invalid base64 data");
            }
        }

        # Forward data to specific receivers
        if( $ret->GetStatus() )
        {
            $data_arr = array();
            parse_str(base64_decode(urldecode($data), true), $data_arr);

            if( strtoupper($sensor_serial_number[0]) === "S")
            {
                $ret = $this->ProcessHiveSensorData($sensor_serial_number, $data_arr);
            }
            else if( strtoupper($sensor_serial_number[0]) === "C" )
            {
                $ret = $this->ProcessClusterSensorData($sensor_serial_number, $data_arr);
            }
            else
            {
                $ret->SetStatusMessage(false, "invalid serial number type byte");
            }
        }

        return $ret;
    }

    private function ProcessClusterSensorData($sensor_serial_number, array $data): StdRet
    {
        $ret = new StdRet(true, "");
        $sensor = new Db\Table\DataTypes\clusters_sensors_t();
        $sensor_data = new Db\Table\DataTypes\clusters_data_t();

        # Check if sensor is found in database
        if( $ret->GetStatus() )
        {
            if( !($sensor = Db\Table\clusters_sensors::getInstance()->GetBySerialNumber($sensor_serial_number)) )
            {
                $ret->SetStatusMessage(false, "sensor not found in database");
            }
        }

        # Check if all input data is present
        if( $ret->GetStatus() )
        {
            if( !isset($data['volts_mv'], $data['temperature_deg'], $data['humidity_rh']) )
            {
                $ret->SetStatusMessage(false, "not all cluster data were found");
            }
        }

        # Validate input data and insert to database
        if( $ret->GetStatus() )
        {
            $sensor_data->id = null;
            $sensor_data->cluster_sensor_id = $sensor->id;
            $sensor_data->datetime = Utils_GetCurrentDateTimeStr();

            $sensor_data->volts_mv = is_numeric($data['volts_mv'])?(int)$data['volts_mv']:-1;
            $sensor_data->temperature_deg = is_numeric($data['temperature_deg'])?(float)$data['temperature_deg']:-999;
            $sensor_data->humidity_rh = is_numeric($data['humidity_rh'])?(int)$data['humidity_rh']:-1;

            if( !Db\Table\clusters_data::getInstance()->Add($sensor_data) )
            {
                $ret->SetStatusMessage(false, "failed to store received data");
            }
        }

        return $ret;
    }

    private function ProcessHiveSensorData($sensor_serial_number, array $data): StdRet
    {
        $ret = new StdRet(true, "");
        $sensor = new Db\Table\DataTypes\hives_sensors_t();
        $sensor_data = new Db\Table\DataTypes\hives_data_t();

        # Check if sensor is found in database
        if( $ret->GetStatus() )
        {
            if( !($sensor = Db\Table\hives_sensors::getInstance()->GetBySerialNumber($sensor_serial_number)) )
            {
                $ret->SetStatusMessage(false, "sensor not found in database");
            }
        }

        # Check if all input data is present
        if( $ret->GetStatus() )
        {
            if( !isset($data['volts_mv'], $data['weight_gm']) )
            {
                $ret->SetStatusMessage(false, "not all cluster data were found");
            }
        }

        # Validate input data and insert to database
        if( $ret->GetStatus() )
        {
            $sensor_data->id = null;
            $sensor_data->hive_sensor_id = $sensor->id;
            $sensor_data->datetime = Utils_GetCurrentDateTimeStr();

            $sensor_data->volts_mv = is_numeric($data['volts_mv'])?(int)$data['volts_mv']:-1;
            $sensor_data->weight_gm = is_numeric($data['weight_gm'])?(float)$data['weight_gm']:-1;

            if( !Db\Table\hives_data::getInstance()->Add($sensor_data) )
            {
                $ret->SetStatusMessage(false, "failed to store received data");
            }
        }

        return $ret;
    }
}