<?php require_once "header.php";?>

<?php
    require_once APPL_DATABASE_PATH . "/Database.hives_sensors.php";
    require_once APPL_DATABASE_PATH . "/Database.clusters_sensors.php";
    require_once APPL_DATABASE_PATH . "/Database.clusters.php";
?>

<div class="mt-4 pl-4">
    <h1>Senzori ambientali:</h1>
    <ul>
        <?php
        $sensors = Db\Table\clusters_sensors::getInstance()->GetAllRecordsOrdered();

        if( !empty($sensors) )
        {
            /** @var $sensor Db\Table\DataTypes\clusters_sensors_t */
            foreach($sensors  as $sensor)
            {
                echo "<li>{$sensor->serial_number} (" . Db\Table\clusters::getInstance()->GetRecordById($sensor->cluster_id)->title . ")</li>\n";
            }
        }
        ?>
    </ul>

    <h1>Senzori stupi:</h1>
    <ul>
        <?php
            $sensors = Db\Table\hives_sensors::getInstance()->GetAllRecordsOrdered();

            if( !empty($sensors) )
            {
                /** @var $sensor Db\Table\DataTypes\hives_sensors_t */
                foreach($sensors  as $sensor)
                {
                   echo "<li><a href='view-sensor?serial_number={$sensor->serial_number}'>{$sensor->serial_number}</a> (" . Db\Table\clusters::getInstance()->GetRecordById($sensor->cluster_id)->title . ")</li>\n";
                }
            }
        ?>
    </ul>
</div>


<?php require_once "footer.php"; ?>