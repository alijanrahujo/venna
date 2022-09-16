<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("withdrawal_list"); ?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="withdrawal_dt">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Agent</th>
                                                    <th>Amount</th>
                                                    <th>Service Charge</th>
                                                    <th>Final Amount</th>
                                                    <th>Remark</th>
                                                    <th>Status</th>
                                                    <th>Datetime</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <?php
                                            if($this->uri->segment(3) != ""){
                                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                                            }else{
                                                $id = $this->user_profile_info['company_id'];
                                            }
                                        ?>
                                        <input type="hidden" id="company_id" value="<?php echo $id; ?>">
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
    function back_to_company(){
        window.location.href = "<?= site_url(); ?>Admin/withdraw";
    }

	$(document).ready(function(){
        drawtable();
	});

	function drawtable(){
		$('#withdrawal_dt').dataTable().fnClearTable();
    	$('#withdrawal_dt').dataTable().fnDestroy();
        $('#withdrawal_dt').DataTable({
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
                'url': address + "Admin/get_withdrawal_list",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: $("#company_id").val(),
                    status: "PENDING"
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
                {"data": "type", "orderable": false},
                {"data": "agent_name", "orderable": false},
                {"data": "amount", "orderable": false},
                {"data": "service_charge", "orderable": false},
                {"data": "final_amount", "orderable": false},
                {"data": "remark", "orderable": false},
                {"data": "status", "orderable": false},
                {"data": "insert_time", "orderable": false},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function approve_withdraw(id){
        var approve_withdraw = new FormData();
        approve_withdraw.set('access_token', $("#access_token").val());
        approve_withdraw.set('id', id);

        axios.post(address + 'Admin/approve_withdraw' , approve_withdraw, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response_with_timer('Approved !', true, 'Admin/withdraw');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function reject_withdraw(id){
        if (confirm("Are you sure you want to reject ?")) {
            var reject_withdraw = new FormData();
            reject_withdraw.set('access_token', $("#access_token").val());
            reject_withdraw.set('id', id);

            axios.post(address + 'Admin/reject_withdraw' , reject_withdraw, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Rejected !', true, 'Admin/withdraw');
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