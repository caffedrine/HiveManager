<?php
require_once "header.php";

require_once CORE_INCLUDE_PATH  . "/generic/PrimitiveUtils.php";
require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";
require_once APPL_DATABASE_PATH . "/Database.hives_data.php";
require_once APPL_DATABASE_PATH . "/Database.clusters.php";
require_once APPL_DATABASE_PATH . "/Database.clusters_data.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if( empty($_GET["serial_number"]) || strlen($_GET["serial_number"]) != 10 )
{
    die("invalid sensor serial number");
}

$sensor = Db\Table\hives_sensors::getInstance()->GetBySerialNumber($_GET["serial_number"]);
if( $sensor === null )
{
    die("sensor not found in database");
}

$sensor_data = Db\Table\hives_data::getInstance()->GetBySensorID($sensor->id);
?>

<div>
    <h1>Senzor <?=$sensor->serial_number?>, situat in <?=Db\Table\clusters::getInstance()->GetRecordById($sensor->cluster_id)->title?></h1>
    <br>
    <ul>
        <?php
        if( !empty($sensor_data) )
        {
            /** @var  $data  Db\Table\DataTypes\hives_data_t*/
            foreach($sensor_data as $data)
            {
                echo sprintf("<li>[%s] volts_mv=%s weight_gm=%s</li>\n", $data->datetime, $data->volts_mv, $data->weight_gm);
            }
        }
        ?>
    </ul>
</div>

<?php require_once "footer.php"; ?>

