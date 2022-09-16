<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("agent_report"); ?> </h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-md-2"></div>
                                            <div class="col-md-1">
                                                <label for="basicInput" style="margin-top: 10px;">From</label>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <input type="date" class="form-control" id="from_date" name="from_date">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-1">
                                                <label for="basicInput" style="margin-top: 10px;">to</label>
                                            </div>
                                            <div class="col-md-2">
                                                <fieldset class="form-group">
                                                    <input type="date" class="form-control" id="to_date" name="to_date">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary" type="button" onclick="search_member_report()">Submit</button>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                        <br>
                                        <table class="table" id="wablaster_report_dt">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Check</th>
                                                    <th>Target</th>
                                                    <th>Source</th>
                                                    <th>Status</th>
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

    function search_member_report(){
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        drawtable(from_date, to_date);
    }

	function drawtable(from_date, to_date){
        if(typeof from_date == "undefined" && typeof to_date == "undefined"){
            var is_search = 0;
        }else{
            var is_search = 1;
        }
		$('#wablaster_report_dt').dataTable().fnClearTable();
    	$('#wablaster_report_dt').dataTable().fnDestroy();
        $('#wablaster_report_dt').DataTable({
            'dom': 'lBfrtip',
            'buttons': [
                {
                    extend: 'csvHtml5',
                    title: 'Agent Report',
                    text: 'Export to CSV'
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
                'url': address + "Report/get_member_report",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    is_search: is_search,
                    from_date: from_date,
                    to_date: to_date
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
                {"data": "wa_no", "orderable": false},
                {"data": "wa_check", "orderable": false},
                {"data": "wa_target", "orderable": false},
                {"data": "wa_source", "orderable": false},
                {"data": "wa_status", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }
</script>