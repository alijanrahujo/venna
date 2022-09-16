<?php

use Rakit\Validation\Rules\Date;

 error_reporting(E_ALL ^ E_NOTICE); ?>
<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <?php if ($this->user_profile_info['company_id'] == 1) { ?>
                            <form method="POST" id="stock_form">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title"><?php echo "Add new promotions" ?></h4>
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <?php $currentDate = new DateTime(); ?>
                                                <input type="hidden" id="cd" name="cd" value="<?php echo date_format($currentDate,'Y-m-d'); ?>">
                                                    <fieldset class="form-group">
                                                        <label for="my_select">Package Name</label>
                                                        <select class="form-control" id="my_select" name="my_select" onchange="send_option();" required>
                                                            <option>Select Package</option>
                                                            <?php
                                                            //----------------------------------------------------------------
                                                            // LIST FILLED FROM DATABASE (ALLEGEDLY).
                                                            $con = mysqli_connect("localhost", "3fs_db", "odu59*D4", "3fs_db");

                                                            $sql = "SELECT * from vny_package where company_id = " . $this->user_profile_info['company_id'];
                                                            $result = mysqli_query($con, $sql);

                                                            //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                                echo "<option value='" . $row["english_name"] . "'>" . $row["english_name"] . "</option>";
                                                            }

                                                            mysqli_free_result($result);
                                                            mysqli_close($con);
                                                            //----------------------------------------------------------------
                                                            ?>
                                                        </select>
                                                        <br />
                                                    </fieldset>
                                                    <?php
                                                    //----------------------------------------------------------------
                                                    // TABLE FILLED FROM DATABASE ACCORDING TO SELECTED OPTION.
                                                    if (isset($_POST["my_option"])) // IF USER SELECTED ANY OPTION.
                                                    {
                                                        echo $_POST["my_option"];
                                                        echo "<br> <br>";
                                                        $con2 = mysqli_connect("localhost", "3fs_db", "odu59*D4", "3fs_db");

                                                        $sql2 = "SELECT * from vny_package where company_id = 1 and english_name='" . $_POST["my_option"] . "'";
                                                        $result2 = mysqli_query($con2, $sql2);

                                                        $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC); ?>
                                                        <fieldset class="form-group">
                                                            <label for="cp">Current Price</label>
                                                            <br>
                                                            <input type="text" class="form-control" id="cp" name="cp" value="<?php echo $row2["grand_total"] ?>" readonly>
                                                        </fieldset>


                                                    <?php
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>

                                                    <fieldset class="form-group">
                                                        <label for="pp">Promotion Price</label>
                                                        <br>
                                                        <input type="text" class="form-control" id="pp" name="pp" required>
                                                    </fieldset>
                                                    <!-- <fieldset class="form-group">
                                                        <label for="ps">Promotion Start</label>
                                                        <br>
                                                        <input type="date" id="ps" name="ps" required>
                                                    </fieldset> -->
                                                    <fieldset class="form-group">
                                                        <label for="pe">Promotion End</label>
                                                        <br>
                                                        <input type="date" id="pe" name="pe" required>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            <br>
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $company_id = $this->user_profile_info['company_id'];
                                ?>
                                <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>">
                                <!-- <input type="hidden" id="my_option" name="my_option" value="<?php $this->$_POST["my_option"]; ?>"> -->
                                <?php if ($_POST["my_option"] == "VIP") { ?>
                                    <input type="hidden" id="pid" name="pid" value="1">
                                <?php } else if ($_POST["my_option"] == "Vice President") { ?>
                                    <input type="hidden" id="pid" name="pid" value="2">
                                <?php } else if ($_POST["my_option"] == "Director") { ?>
                                    <input type="hidden" id="pid" name="pid" value="3">
                                <?php } else  if ($_POST["my_option"] == "Shareholder") { ?>
                                    <input type="hidden" id="pid" name="pid" value="4">
                                <?php } else      if ($_POST["my_option"] == "Co-Founder") { ?>
                                    <input type="hidden" id="pid" name="pid" value="5">
                                <?php } else
                                    if ($_POST["my_option"] == null) {
                                    $_POST["my_option"] = 0;
                                };

                                ?>
                            </form>

                    </div>


                    <form method="post" action="" style="display:none" id="my_form">
                        <input type="text" id="my_option" name="my_option" />
                    </form>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo "Active Promotion List" ?></h4>
                        </div>
                        <div class="card-content ">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table dt-responsive" id="display_active_promotions">
                                        <thead>
                                            <tr>
                                                <th><?php echo "Package"; ?></th>
                                                <th><?php echo "Promotion Price"; ?></th>
                                                <th><?php echo "Promotion Start On"; ?></th>
                                                <th><?php echo "Promotion End On"; ?></th>                                      
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>
        </div>
    </div>
</div>

</form>
</div>

</div>
</section>
</div>
</div>
</div>

<script>
    $('#stock_form').submit(function(e) {
        e.preventDefault();
           
            // var x = date("Y-m-d");
            // var y = document.getElementById("pe").value;
            // var z = document.getElementById("cd").value;
            // if(x>y)
            // alert("End date can't be earlier that Start date!");
            // else
            // if (y<z)
            // alert("End date can't be earlier that today!");
            // else{
            
     

        var add_promotions = new FormData(this);
        add_promotions.set('access_token', $("#access_token").val());
        // manage_promotions.set('promotion_price', $(pp));
        // manage_promotions.set('package_id', $(pp));

        //edit_stock_auto.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Company/add_promotions', add_promotions, apiHeader)
            .then(function(response) {
                if (response.data.status == "Success") {
                    success_response('Success !', true, 'Company/manage_promotions');
                } else {
                    warning_response(response.data.message);
                }
            })
            .catch(function(data) {
                console.log(data);
                error_response();
            });
        }//}
    );
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var company_id = "<?php echo $this->user_profile_info['company_id']; ?>";

        if (company_id == 1) {
            display_active_promotions();
        }



    });

    function send_option() {
        var sel = document.getElementById("my_select");
        var txt = document.getElementById("my_option");
        txt.value = sel.options[sel.selectedIndex].value;
        var frm = document.getElementById("my_form");
        //var frm = new FormData(document.getElementById("my_form"))
        frm.submit();
        // var data = new FormData(document.getElementById("my_form"));
        // var xhr = new XMLHttpRequest();
        // xhr.open("POST", "SERVER-SCRIPT");
        // xhr.send(data);
    }
    //----------------------------------------------------------------


    function display_active_promotions() {
        $('#display_active_promotions').dataTable().fnClearTable();
        $('#display_active_promotions').dataTable().fnDestroy();
        $('#display_active_promotions').DataTable({
            'dom': 'Bfrtip',
            'buttons': [{
                extend: 'excelHtml5',
                title: 'Shipment List',
                text: 'Export to Excel'
            }, ],
            'order': [],
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function(oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function(oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Company/get_promotions_list",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'APPROVE',
                    order_status: 'PLACED',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>'
                },
                dataFilter: function(data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    } else {
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                
                "paginate": {
                    "previous": "<?php echo $this->lang->line("previous"); ?>",
                    "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [{
                    "data": "package_name",
                    "orderable": false
                },
                {
                    "data": "promotion_price",
                    "orderable": false
                },
                {
                    "data": "start_date",
                    "orderable": false
                },
                {
                    "data": "end_date",
                    "orderable": false
                }
                // ,
                // {
                //     "data": "test",
                //     "orderable": false
                // }
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }
</script>