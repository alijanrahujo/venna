<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <?php
                                    $group_id = $this->user_profile_info['group_id'];
                                    if($group_id == 1){
                                ?>
                                <h4 class="card-title"><?php echo $this->lang->line("agent_list"); ?> <a href="<?= site_url(); ?>Member/add"><i class="fa fa-plus-circle add-icon"></i></a></h4>
                                <?php
                                    }else{
                                ?>
                                <h4 class="card-title"><?php echo $this->lang->line("agent_list"); ?> </h4>
                                <?php
                                    }
                                ?>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <?php
                                        if($this->user_profile_info['company_id'] != 0){
                                    ?>
                                    Key In Data : <a target="_blank" href="<?= site_url(); ?>Forms/add/<?php echo $this->user_profile_info['company_id']; ?>"><?= site_url(); ?>Forms/add/<?php echo $this->user_profile_info['company_id']; ?></a>&nbsp;<a class="btn btn-success" href="<?= site_url() . "Member/upline"; ?>">Update All Upline</a>&nbsp;<a class="btn btn-danger" href="<?= site_url() . "Member/cwallet"; ?>">Insert Cash Wallet</a>&nbsp;<a class="btn btn-info" href="<?= site_url() . "Member/cb"; ?>">Insert CB Point</a>&nbsp;<a class="btn btn-success" href="<?= site_url() . "Member/drb"; ?>">Insert DRB</a>
                                    <br><br>
                                    <?php
                                        }
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="member_dt">
                                            <thead>
                                                <tr>
                                                    <th>Username</th>
                                                    <th>Fullname</th>
                                                    <th>Package</th>
                                                    <th>Upline</th>
                                                    <th>IC</th>
                                                    <th>Join Date</th>
                                                    <th>Action</th>
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
		$('#member_dt').dataTable().fnClearTable();
    	$('#member_dt').dataTable().fnDestroy();
        $('#member_dt').DataTable({
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
                'url': address + "Member/get_member",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
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
                {"data": "username"},
                {"data": "fullname"},
                {"data": "package", "orderable": false},
                {"data": "referral_id"},
                {"data": "ic"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
    }
    
    function delete_member(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_member = new FormData();
            delete_member.set('access_token', $("#access_token").val());
            delete_member.set('id', id);

            axios.post(address + 'Member/delete_member' , delete_member, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Member');
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