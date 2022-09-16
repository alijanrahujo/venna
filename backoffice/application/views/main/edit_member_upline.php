<style>
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 550px;
        min-height: 550px !important;
        overflow-y: auto;
    }
</style>

<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-select2">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit upline</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <form method="POST" id="upline_form">
                                            <table class="table" id="member_dt">
                                                <thead>
                                                    <tr>
                                                        <th>Upline</th>
                                                        <th>Username</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </form>
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
		$('#member_dt').dataTable().fnClearTable();
    	$('#member_dt').dataTable().fnDestroy();
        $('#member_dt').DataTable({
            'order': [],
            'info': false,
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            'drawCallback': function(dt) {
                $('.upline-select2').select2();
            },
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Member/get_brand_member",
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
                {"data": "referral_id", "orderable": false},
                {"data": "username", "orderable": false}
            ]
        });
    }

    $('#upline_form').submit(function(e) {
        e.preventDefault();

        var update_all_upline = new FormData(this);
        update_all_upline.set('access_token', $("#access_token").val());
        update_all_upline.set('company_id', "<?php echo $this->user_profile_info['company_id']; ?>");

        axios.post(address + 'Member/update_brand_all_upline' , update_all_upline, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Member/upline');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });
</script>