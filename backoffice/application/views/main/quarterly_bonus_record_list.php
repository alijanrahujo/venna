<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("mds_quarterly_record"); ?></h4>
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
                                                    <select class="form-control" id="month" name="month">
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
                                                <button class="btn btn-primary" type="button" onclick="generate_bonus()">Released</button>
                                            </div>
                                            <div class="col-md-4"></div>
                                        </div>
                                        <br>
                                        <table class="table table-striped table-bordered" id="mdsq_record_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("fullname"); ?></th>
                                                    <th><?php echo $this->lang->line("total_quantity"); ?></th>
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
		$('#mdsq_record_dt').dataTable().fnClearTable();
    	$('#mdsq_record_dt').dataTable().fnDestroy();
        $('#mdsq_record_dt').DataTable({
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
                'url': address + "Bonus/get_quarterly_bonus_record",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    type: 'cross_over'
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
                {"data": "total_quantity"},
                {"data": "bonus"},
                {"data": "insert_time"}
            ]
        });
    }

    function generate_bonus(){
        var month = $("#month").val();

        if (confirm("Are you sure you want to released the bonus start from this month + 3 month ?")) {
            var calculate_quarterly_bonus = new FormData();
            calculate_quarterly_bonus.set('access_token', $("#access_token").val());
            calculate_quarterly_bonus.set('month', month);
            calculate_quarterly_bonus.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

            axios.post(address + 'Order/calculate_quarterly_bonus' , calculate_quarterly_bonus, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Released Successfully !', true, 'Bonus/mdsq');
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
</script>