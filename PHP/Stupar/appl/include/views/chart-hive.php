<?php

/** @var $sensor Db\Table\DataTypes\hives_sensors_t */
if( !isset($sensor) )
{
    die("error while accessing this view");
}

// Rework data as date => val
$sensor_data = Db\Table\hives_data::getInstance()->GetAllBySensorID_HourlyAvg($sensor->id, StdDateTime::Curr()->SubstractDays(30)->GetDateTime());

?>

<div class="" id="chart-hive-<?=$sensor->serial_number?>">
    <!-- Chart -->
    <div class="forms-box-full mt-3 pt-1">
        <?php if(!empty($sensor_data)) { ?>
        <h5 class="text-center">Date senzor stup, de la <?=$sensor_data[0]->datetime?> pana la <?=$sensor_data[count($sensor_data)-1]->datetime?></h5>
        <canvas id="hiveSensorChart_<?=$sensor->serial_number?>"></canvas>

            <!-- Menu for large screens -->
            <div class="d-none d-md-none d-lg-block text-center">
                <div class="btn-group" role="group" aria-label="Select history">
                    <input type="checkbox" class="btn-check" name="btncheckbox" id="btncheckbox1" autocomplete="off">
                    <label class="btn btn-sm btn-outline-secondary" for="btncheckbox1">Weight</label>

                    <input type="checkbox" class="btn-check" name="btncheckbox" id="btncheckbox2" autocomplete="off">
                    <label class="btn btn-sm btn-outline-secondary" for="btncheckbox2">Temperature</label>

                    <input type="checkbox" class="btn-check" name="btncheckbox" id="btncheckbox3" autocomplete="off">
                    <label class="btn btn-sm btn-outline-secondary" for="btncheckbox3">Humidity</label>

                    <input type="checkbox" class="btn-check" name="btncheckbox" id="btncheckbox4" autocomplete="off" checked>
                    <label class="btn btn-sm btn-outline-secondary" for="btncheckbox4">Voltage hive</label>

                    <input type="checkbox" class="btn-check" name="btncheckbox" id="btncheckbox5" autocomplete="off">
                    <label class="btn btn-sm btn-outline-secondary" for="btncheckbox5">Voltage env</label>
                </div>
            </div>

            <!-- dropdown menu for mobile -->
            <select class="form-select d-lg-none" aria-label="Default select example" multiple>
                <option value="1">6 Hours</option>
                <option value="2">24 Hours</option>
                <option value="3">7 Days</option>
                <option value="4" selected>30 Days</option>
                <option value="5">3 Months</option>
            </select>
        <?php } else { ?>
            <p>Nu exista date pentru acest stup</p>
        <?php }?>
    </div>

    <!-- Chart data for hive -->
    <script>
        const labels_<?=$sensor->serial_number?> =
        [
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

        const data_<?=$sensor->serial_number?> =
            {
                labels: labels_<?=$sensor->serial_number?>,
                datasets: [{
                    //fill: true,
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
            document.getElementById('hiveSensorChart_<?=$sensor->serial_number?>'),
            config_<?=$sensor->serial_number?>
        );
    </script>
</div>
