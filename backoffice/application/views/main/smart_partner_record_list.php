<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("smart_partner_record"); ?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-1">
                                                <label for="basicInput" style="margin-top: 10px;">Month</label>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <select class="form-control" id="month" name="month" onchange="check_is_generate_smart_partner(this)">
                                                        <option value="1">Jan</option>
                                                        <option value="2">Feb</option>
                                                        <option value="3">Mar</option>
                                                        <option value="4">Apr</option>
                                                        <option value="5">May</option>
                                                        <option value="6">Jun</option>
                                                        <option value="7">Jul</option>
                                                        <option value="8">Aug</option>
                                                        <option value="9">Sep</option>
                                                        <option value="10">Oct</option>
                                                        <option value="11">Nov</option>
                                                        <option value="12">Dec</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-primary" type="button" id="generate_btn" onclick="generate_smart_partner()"><span id="smart_partner_btn">Generate</span></button>
                                                <button class="btn btn-primary" type="button" id="released_btn" onclick="released_smart_partner()" style="display: none;"><span id="smart_partner_btn">Released</span></button>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <br>
                                        <table class="table table-striped table-bordered" id="sm_record_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("fullname"); ?></th>
                                                    <th><?php echo $this->lang->line("company_sales"); ?></th>
                                                    <th><?php echo $this->lang->line("total"); ?></th>
                                                    <th><?php echo $this->lang->line("pass_up"); ?></th>
                                                    <th><?php echo $this->lang->line("grand_total"); ?></th>
                                                    <th><?php echo $this->lang->line("group_sales"); ?></th>
                                                    <th><?php echo $this->lang->line("bonus_per_box"); ?></th>
                                                    <th><?php echo $this->lang->line("bonus"); ?></th>
                                                    <th><?php echo $this->lang->line("datetime"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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

	function drawtable(){
		$('#sm_record_dt').dataTable().fnClearTable();
    	$('#sm_record_dt').dataTable().fnDestroy();
        $('#sm_record_dt').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Smart Partner Bonus',
                    text: 'Export to Excel'
                },
            ],
            'order': [],
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Bonus/get_smart_partner_record",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>'
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
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
                {"data": "fullname"},
                {"data": "company_sales_after_bonus"},
                {"data": "total_sales"},
                {"data": "sales_pass_up"},
                {"data": "grand_sales"},
                {"data": "group_sales"},
                {"data": "bonus_per_box"},
                {"data": "bonus"},
                {"data": "insert_time"}
            ]
        });

        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }

    function generate_smart_partner(){
        var month = $("#month").val();

        if (confirm("Are you sure you want to generate the bonus ?")) {
            var proceed_smart_partner = new FormData();
            proceed_smart_partner.set('access_token', $("#access_token").val());
            proceed_smart_partner.set('month', month);
            proceed_smart_partner.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

            axios.post(address + 'Order/proceed_smart_partner' , proceed_smart_partner, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Released Successfully !', true, 'Bonus/club');
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });
        }
    }

    function released_smart_partner(){
        var month = $("#month").val();

        if (confirm("Are you sure you want to released the bonus ?")) {
            var released_smart_partner = new FormData();
            released_smart_partner.set('access_token', $("#access_token").val());
            released_smart_partner.set('month', month);
            released_smart_partner.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

            axios.post(address + 'Order/released_smart_partner' , released_smart_partner, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Released Successfully !', true, 'Bonus/club');
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });
        }
    }

    function check_is_generate_smart_partner(data){
        var month = data.value;

        var check_is_generate_smart_partner = new FormData();
        check_is_generate_smart_partner.set('access_token', $("#access_token").val());
        check_is_generate_smart_partner.set('month', month);
        check_is_generate_smart_partner.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

            axios.post(address + 'Order/check_is_generate_smart_partner' , check_is_generate_smart_partner, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    if(response.data.data == 1){
                        $("#generate_btn").hide();
                        $("#released_btn").show();
                        $("#smart_partner_btn").html("Released");
                    }else if(response.data.data == 0){
                        $("#generate_btn").show();
                        $("#released_btn").hide();
                        $("#smart_partner_btn").html("Generate");
                    }else{
                        $("#generate_btn").hide();
                        $("#released_btn").hide();
                    }
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });
    }
</script>