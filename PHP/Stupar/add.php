<?php

require_once "./appl/config.php";
require_once CORE_INCLUDE_PATH  . "/generic/PrimitiveUtils.php";
require_once CORE_DATABASE_PATH . "/Database.php";
require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";

// Check if GET/POST data exists
if( empty($_GET['stup_id']) || empty($_GET['data']) )
{
    http_response_code(405);
    die("No data set");
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
$line = "[{$today_datetime}] [{$_GET['stup_id']}] " . base64_decode($_GET['data']);

// Append data to a file
$fp = fopen($db_filename, 'ab');
fwrite($fp, trim($line) . "\n");
fclose($fp);

echo "Data successfully added!";