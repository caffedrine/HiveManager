<?php
require_once "header.php";

require_once CORE_INCLUDE_PATH  . "/generic/PrimitiveUtils.php";
require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";
require_once APPL_DATABASE_PATH . "/Database.hives_data.php";
require_once APPL_DATABASE_PATH . "/Database.clusters.php";
require_once APPL_DATABASE_PATH . "/Database.clusters_data.php";
require_once APPL_DATABASE_PATH . "/Database.clusters_sensors.php";

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

?>

<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Title -->
    <h1>Senzor <?=$sensor->serial_number?>, situat in <?=Db\Table\clusters::getInstance()->GetRecordById($sensor->cluster_id)->title?></h1> <hr>

    <!-- Chart timescale selector -->
    <?php include APPL_INCLUDE_PATH . "/views/chart-timescale-selector.php" ?>

    <!-- Hive chart -->
    <?php include APPL_INCLUDE_PATH . "/views/chart-hive.php" ?>

    <!-- Env chart -->
    <?php // Rework data as date => val
        $amb_sensors = Db\Table\clusters_sensors::getInstance()->GetAllByClusterId($sensor->cluster_id);
        if( !empty($amb_sensors) )
        {
            /** @var $sensor Db\Table\DataTypes\clusters_sensors_t */
            foreach($amb_sensors as $sensor)
            {
                include APPL_INCLUDE_PATH . "/views/chart-env.php";
            }
        }
    ?>
</div>

<br>

<?php require_once "footer.php"; ?>

