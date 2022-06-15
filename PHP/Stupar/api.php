<?php

require_once "./appl/config.php";
require_once CORE_INCLUDE_PATH  . "/generic/PrimitiveUtils.php";
require_once CORE_INCLUDE_PATH  . "/generic/InputValidators.php";
require_once APPL_INCLUDE_PATH  . "/SensorDataReceiver.php";

//https://stupar.254.ro/api/v1/add?sensor_id=C000000001&data=dm9sdHNfbXY9MzY3OCZ0ZW1wZXJhdHVyZV9kZWc9MjguMzQmaHVtaWRpdHlfcmg9NjU%3D&signature=dummy
//https://stupar.254.ro/api/v1/add?sensor_id=S000000001&data=dm9sdHNfbXY9NDU2NyZ3ZWlnaHRfZ209MTI4OA%3D%3D&signature=dummy

// Check if GET/POST data exists
if( empty($_GET['sensor_id']) || empty($_GET['data']) || empty($_GET['signature']) )
{
    http_response_code(405);
    die(json_encode(array("error" => "no data set"), JSON_PRETTY_PRINT));
}

// Validate data
if( !Utils_IsAlphanumericRestricted($_GET['sensor_id'], 10) || !Utils_IsAlphanumericExtended(urldecode($_GET['data']), 4096) || !Utils_IsAlphanumericRestricted($_GET['signature'], 128) )
{
    http_response_code(405);
    die(json_encode(array("error" => "invalid input data"),JSON_PRETTY_PRINT));
}

// Check if file with logs for today exists
$today_date = Utils_GetCurrentDateStr();
$today_datetime = Utils_GetCurrentDateTimeStr();
$db_filename = "data/{$today_date}.txt";

// Create records file for today if not exists
if( !is_file($db_filename) )
{
    file_put_contents($db_filename, "");
}

// Build log data
$line = "[{$today_datetime}] [{$_GET['sensor_id']}] " . base64_decode($_GET['data']);

// Append data to a file
$fp = fopen($db_filename, 'ab');
fwrite($fp, trim($line) . "\n");
fclose($fp);

// Process incoming sensor data
$return = SensorDataReceiver::getInstance()->Receive($_GET['sensor_id'], $_GET['data'], $_GET['signature']);
if( !$return->GetStatus() )
{
    die(json_encode(array("error" => $return->GetMessage()), JSON_PRETTY_PRINT));
}

die(json_encode(array("result" => "OK"), JSON_PRETTY_PRINT));