<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("monthly_sales_report"); ?> </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <!-- <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1">
                                                <label for="basicInput" style="margin-top: 10px;">Month</label>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="month" name="month">
                                                        <option value="1" <?php if(date("m") == "01"){ echo "selected"; } ?>>1</option>
                                                        <option value="2" <?php if(date("m") == "02"){ echo "selected"; } ?>>2</option>
                                                        <option value="3" <?php if(date("m") == "03"){ echo "selected"; } ?>>3</option>
                                                        <option value="4" <?php if(date("m") == "04"){ echo "selected"; } ?>>4</option>
                                                        <option value="5" <?php if(date("m") == "05"){ echo "selected"; } ?>>5</option>
                                                        <option value="6" <?php if(date("m") == "06"){ echo "selected"; } ?>>6</option>
                                                        <option value="7" <?php if(date("m") == "07"){ echo "selected"; } ?>>7</option>
                                                        <option value="8" <?php if(date("m") == "08"){ echo "selected"; } ?>>8</option>
                                                        <option value="9" <?php if(date("m") == "09"){ echo "selected"; } ?>>9</option>
                                                        <option value="10" <?php if(date("m") == "10"){ echo "selected"; } ?>>10</option>
                                                        <option value="11" <?php if(date("m") == "11"){ echo "selected"; } ?>>11</option>
                                                        <option value="12" <?php if(date("m") == "12"){ echo "selected"; } ?>>12</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="basicInput" style="margin-top: 10px;">Year</label>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="year" name="year">
                                                        <option value="2021">2021</option>
                                                        <option value="2022">2022</option>
                                                        <option value="2023">2023</option>
                                                        <option value="2024">2024</option>
                                                        <option value="2025">2025</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary" type="button" onclick="search_summary_report()">Submit</button>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div> -->
                                        <br>
                                        <table class="table" id="drawtable">
                                            <thead>
                                                <tr>
                                                    <th>Total Quantity</th>
                                                    <th>Month</th>
                                                    <!-- <th>Package</th>
                                                    <th>Retail</th>
                                                    <th>Amount</th>
                                                    <th>Restock</th> -->
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <!-- <td><span id="total_purchase_package"></span></td>
                                                    <td colspan="3"></td> -->
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        drawtable();
	});

    function search_summary_report(){
        //var month = $("#month").val();
       // var year = $("#year").val();
        drawtable();
    }

	function drawtable(){
       // var month = $("#month").val();
      //  var year = $("#year").val();
		$('#drawtable').dataTable().fnClearTable();
    	$('#drawtable').dataTable().fnDestroy();
        $('#drawtable').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Monthly Sales Report',
                    text: 'Export to Excel',
                    footer: true,
                },
            ],
            'order': [],
            'searching' : false,
            'serverSide': true,
            "bStateSave": true,
            "lengthMenu": [10, 20, 50, 100, 200, 500],
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Report/get_monthly_sales_report",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    //month: month,
                    //year: year
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    $("#total_purchase_package").html(result.data.total_purchase_package);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
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
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "total_quantity", "orderable": false},
                {"data": "month", "orderable": false}
              //  {"data": "package_name", "orderable": false},
              //  {"data": "total_retail_sales", "orderable": false},
              //  {"data": "total_retail_sales_amount", "orderable": false},
              //  {"data": "total_restock", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }
</script>