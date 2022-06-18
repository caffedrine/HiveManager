<?php


?>

<div class="row mt-3 justify-content-between align-items-center just">
    <div class="col-auto">
        <!-- Menu for large screens -->
        <div class="d-none d-md-none d-lg-block">
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
                <label class="btn btn-sm btn-outline-primary" for="btnradio10">All data</label>

                <input type="radio" class="btn-check" name="btnradio" id="btnradio11" autocomplete="off">
                <label class="btn btn-sm btn-outline-primary" for="btnradio11">Custom range</label>
            </div>
        </div>

        <!-- dropdown menu for mobile -->
        <select class="form-select d-lg-none" aria-label="Default select example">
            <option value="1">6 Hours</option>
            <option value="2">24 Hours</option>
            <option value="3">7 Days</option>
            <option value="4" selected>30 Days</option>
            <option value="5">3 Months</option>
            <option value="6">6 Months</option>
            <option value="7">Current year</option>
            <option value="8">1 Year</option>
            <option value="9">2 Years</option>
            <option value="10">All data</option>
            <option value="11">Custom range</option>
        </select>

    </div>

    <div class="col-auto">
        <div>
            <div id="modal-dataset">
                <!-- Button trigger modal dataset -->
                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Show datasets
                </button>
            </div>
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
        </div>
    </div>
</div>
