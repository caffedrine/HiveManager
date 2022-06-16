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

<!-- Modal with dataset -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol>
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
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<div>
    <h1>Senzor <?=$sensor->serial_number?>, situat in <?=Db\Table\clusters::getInstance()->GetRecordById($sensor->cluster_id)->title?></h1>
    <hr>

    <div class="row mt-3 justify-content-between align-items-center">
        <div class="col-auto">
            History:
            <div class="btn-group" role="group" aria-label="Select history">
                <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio1">6 Hours</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio2">24 Hours</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio3">7 days</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off" checked>
                <label class="btn btn-sm btn-outline-primary" for="btnradio4">30 Days</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio5">3 Months</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio6" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio6">6 Months</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio7" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio7">Current year</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio8" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio8">1 Year</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio9" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio9">2 Years</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio10" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio10">All dates</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio11" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio11">Custom range</label>
            </div>
        </div>

        <div class="col-auto">
            <div id="modal-dataset">
                <!-- Button trigger modal dataset -->
                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Show datasets
                </button>
            </div>
        </div>

    </div>


    <div class="forms-box-full mt-3">
        <canvas id="hiveSensorChart"></canvas>
    </div>

    <script>
        const labels = [
            <?php
            if( !empty($sensor_data) )
            {
                /** @var  $data  Db\Table\DataTypes\hives_data_t*/
                foreach($sensor_data as $data)
                {
                    echo "'{$data->datetime}',\n";
                }
            }
            ?>
        ];

        const data =
        {
            labels: labels,
            datasets: [{
                label: 'Weight [gm]',
                yAxisID: 'y',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: [
                    <?php
                    if( !empty($sensor_data) )
                    {
                        /** @var  $data  Db\Table\DataTypes\hives_data_t*/
                        foreach($sensor_data as $data)
                        {
                            echo "{$data->weight_gm},\n";
                        }
                    }
                    ?>
                ]
            },
            {
                label: 'Voltage [mv]',
                yAxisID: 'y1',
                hidden: true,
                backgroundColor: 'rgb(133,201,255)',
                borderColor: 'rgb(133,201,255)',
                data: [
                    <?php
                    if( !empty($sensor_data) )
                    {
                        /** @var  $data  Db\Table\DataTypes\hives_data_t*/
                        foreach($sensor_data as $data)
                        {
                            echo "{$data->volts_mv},\n";
                        }
                    }
                    ?>
                ]
            }] // datasets
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                interaction:
                {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Chart.js Line Chart - Multi Axis'
                    }
                },
                scales:
                {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            // Include a dollar sign in the ticks
                            callback: function(value, index, ticks) {
                                return value + " gm";
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        ticks: {
                            // Include a dollar sign in the ticks
                            callback: function(value, index, ticks) {
                                return value + " mV";
                            }
                        },
                        // grid line settings
                        grid: {
                            drawOnChartArea: false, // only want the grid lines for one axis to show up
                        },
                    },
                }
            },
            legend:
            {
                display: true,
                onClick: function(event, legendItem)
                {
                    console.log("tralala");

                    var y_axis_id = myChart.data.datasets[legendItem.datasetIndex].yAxisID;
                    if(y_axis_id.startsWith('TOGGLE'))
                    {
                        //find by name yaxis
                        for(i=0;i<myChart.options.scales.yAxes.length;i++){
                            if(myChart.options.scales.yAxes[i].id==y_axis_id)
                            {
                                myChart.options.scales.yAxes[i].display = !myChart.options.scales.yAxes[i].display;
                                myChart.data.datasets[legendItem.datasetIndex].hidden = !myChart.data.datasets[legendItem.datasetIndex].hidden;
                                myChart.update();
                            }
                        }
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('hiveSensorChart'),
            config
        );

    </script>

</div>

<?php require_once "footer.php"; ?>

