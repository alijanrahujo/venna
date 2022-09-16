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
                                    $user_type = $this->user_profile_info['user_type'];
                                    $group_id = $this->user_profile_info['group_id'];
                                    $company_id = $this->user_profile_info['company_id'];
                                    $company_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($company_id));

                                    if($user_type == "ADMIN" && $group_id == 1){
                                ?>
                                <h4 class="card-title"><?php echo $this->lang->line("package_list"); ?> <a href="<?= site_url(); ?>Package/add/<?php echo $this->uri->segment(3); ?>"><i class="fa fa-plus-circle add-icon"></i></a> <a class="btn btn-success" style="float: right;" href="#" onclick="back_to_company(); return false"><?php echo $this->lang->line("back"); ?></a></h4>
                                <?php
                                    }else{
                                ?>
                                <h4 class="card-title"><?php echo $this->lang->line("package_list"); ?> <a href="<?= site_url(); ?>Package/add/<?php echo $company_id; ?>"><i class="fa fa-plus-circle add-icon"></i></a></h4>
                                <?php
                                    }
                                ?>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="package_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("country"); ?></th>
                                                    <th><?php echo $this->lang->line("name"); ?></th>
                                                    <th><?php echo $this->lang->line("quantity"); ?></th>
                                                    <th><?php echo $this->lang->line("unit_price"); ?></th>
                                                    <th><?php echo $this->lang->line("grand_total"); ?></th>
                                                    <th><?php echo $this->lang->line("action"); ?></th>
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
        window.location.href = "<?= site_url(); ?>Company";
    }

	$(document).ready(function(){
        drawtable();
	});

	function drawtable(){
		$('#package_dt').dataTable().fnClearTable();
    	$('#package_dt').dataTable().fnDestroy();
        $('#package_dt').DataTable({
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
                'url': address + "Package/get_package",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: $("#company_id").val()
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
                {"data": "country_name", "orderable": false},
                {"data": "name"},
                {"data": "quantity"},
                {"data": "unit_price"},
                {"data": "grand_total"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function delete_package(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_package = new FormData();
            delete_package.set('access_token', $("#access_token").val());
            delete_package.set('id', id);

            axios.post(address + 'Package/delete_package' , delete_package, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Package');
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