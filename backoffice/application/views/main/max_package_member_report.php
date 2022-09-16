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
                                        <table class="table" id="member_report_dt">
                                            <thead>
                                                <tr>
                                                    <th>Upline</th>
                                                    <th>Agent</th>
                                                    <th>Email</th>
                                                    <th>Phone No</th>
                                                    <th>In Stock</th>
                                                    <th>Balance Stock</th>
                                                    <th>Join Date</th>
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
		$('#member_report_dt').dataTable().fnClearTable();
    	$('#member_report_dt').dataTable().fnDestroy();
        $('#member_report_dt').DataTable({
            'dom': 'lBfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Agent Report',
                    text: 'Export to Excel'
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
                'url': address + "Report/get_high_package_member_report",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>'
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
                {"data": "upline_name", "orderable": false},
                {"data": "agent_name", "orderable": false},
                {"data": "email", "orderable": false},
                {"data": "phone_no", "orderable": false},
                {"data": "in_stock", "orderable": false},
                {"data": "total_stock", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }
</script>