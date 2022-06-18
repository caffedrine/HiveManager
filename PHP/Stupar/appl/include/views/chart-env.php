<?php

/** @var $sensor Db\Table\DataTypes\clusters_sensors_t */
if( !isset($sensor) )
{
    die("error while accessing this view");
}

// Rework data as date => val
$sensor_data = Db\Table\clusters_data::getInstance()->GetBySensorID($sensor->id);

?>

<div class="" id="chart-environment-<?=$sensor->serial_number?>">
    <!-- Chart -->
    <div class="forms-box-full mt-3 pt-1">
        <h5 class="text-center">Date senzor ambiental, de la <?=$sensor_data[0]->datetime?> pana la <?=$sensor_data[count($sensor_data)-1]->datetime?></h5>
        <canvas id="clusterSensorChart_<?=$sensor->serial_number?>"></canvas>
    </div>

    <!-- Chart data for hive -->
    <script>
        const labels_<?=$sensor->serial_number?> =
            [
                <?php
                if( !empty($sensor_data) )
                {
                    /** @var  $data  Db\Table\DataTypes\clusters_data_t*/
                    foreach($sensor_data as $data)
                    {
                        echo "'{$data->datetime}',\n";
                    }
                }
                ?>
            ];

        const data_<?=$sensor->serial_number?> =
            {
                labels: labels_<?=$sensor->serial_number?>,
                datasets: [{
                    //fill: true,
                    label: 'Temperature [°C]',
                    yAxisID: 'y',
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: [
                        <?php
                        if( !empty($sensor_data) )
                        {
                            /** @var  $data  Db\Table\DataTypes\clusters_data_t*/
                            foreach($sensor_data as $data)
                            {
                                echo "{$data->temperature_deg},\n";
                            }
                        }
                        ?>
                    ]
                },
                {
                    label: 'Humidity [%]',
                    yAxisID: 'y1',
                    //hidden: true,
                    backgroundColor: 'rgb(133,201,255)',
                    borderColor: 'rgb(133,201,255)',
                    data: [
                        <?php
                        if( !empty($sensor_data) )
                        {
                            /** @var  $data  Db\Table\DataTypes\clusters_data_t*/
                            foreach($sensor_data as $data)
                            {
                                echo "{$data->humidity_rh},\n";
                            }
                        }
                        ?>
                    ]
                },
                {
                    label: 'Voltage [mV]',
                    yAxisID: 'y2',
                    hidden: true,
                    backgroundColor: 'rgb(107,255,90)',
                    borderColor: 'rgb(107,255,90)',
                    data: [
                        <?php
                        if( !empty($sensor_data) )
                        {
                            /** @var  $data  Db\Table\DataTypes\clusters_data_t*/
                            foreach($sensor_data as $data)
                            {
                                echo "{$data->volts_mv},\n";
                            }
                        }
                        ?>
                    ]
                }
                ] // datasets
            };

        const config_<?=$sensor->serial_number?> =
            {
                type: 'line',
                data: data_<?=$sensor->serial_number?>,
                options:
                    {
                        responsive: true,
                        interaction:
                            {
                                mode: 'index',
                                intersect: false,
                            },
                        stacked: false,
                        plugins: {
                            legend: {
                                // onClick: function(event, legendItem)
                                // {
                                //     var idx = legendItem.datasetIndex;
                                //
                                //     this.options.scales.yAxes[idx].display = !this.options.scales.yAxes[idx].display;
                                //
                                //     var meta = this.getDatasetMeta(idx);
                                //     // See controller.isDatasetVisible comment
                                //     meta.hidden = meta.hidden === null ? !this.data.datasets[idx].hidden : null;
                                //
                                //     this.update();
                                // }
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
                                        callback: function (value, index, ticks)
                                        {
                                            return value + " °C";
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    ticks: {
                                        // Include a dollar sign in the ticks
                                        callback: function (value, index, ticks)
                                        {
                                            return value + " %";
                                        }
                                    },
                                    // grid line settings
                                    grid: {
                                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                                    },
                                },
                                y2: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    ticks: {
                                        // Include a dollar sign in the ticks
                                        callback: function (value, index, ticks)
                                        {
                                            return value + " mV";
                                        }
                                    },
                                    // grid line settings
                                    grid: {
                                        drawOnChartArea: false, // only want the grid lines for one axis to show up
                                    },
                                },
                            },// scales
                    }// options
            };

        const chart_<?=$sensor->serial_number?> = new Chart(
            document.getElementById('clusterSensorChart_<?=$sensor->serial_number?>'),
            config_<?=$sensor->serial_number?>
        );
    </script>
</div>
