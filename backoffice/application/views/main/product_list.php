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
                                <h4 class="card-title"><?php echo $this->lang->line("product_list"); ?></h4>
                                <?php
                                    }else{
                                ?>
                                <h4 class="card-title"><?php echo $this->lang->line("product_list"); ?> <a href="<?= site_url(); ?>Product/add"><i class="fa fa-plus-circle add-icon"></i></a></h4>
                                <?php
                                    }
                                ?>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="product_dt">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php echo $this->lang->line("image"); ?></th>
                                                    <th><?php echo $this->lang->line("name"); ?></th>
                                                    <th><?php echo $this->lang->line("price"); ?></th>
                                                    <th><?php echo $this->lang->line("action"); ?></th>
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
		$('#product_dt').dataTable().fnClearTable();
    	$('#product_dt').dataTable().fnDestroy();
        $('#product_dt').DataTable({
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
                'url': address + "Product/get_product",
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
                {"data": "count", "orderable": false},
                {"data": "image"},
                {"data": "name"},
                {"data": "price"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function delete_product(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_product = new FormData();
            delete_product.set('access_token', $("#access_token").val());
            delete_product.set('id', id);

            axios.post(address + 'Product/delete_product' , delete_product, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Product');
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

    function enable_product(id){
        var enable_product = new FormData();
        enable_product.set('access_token', $("#access_token").val());
        enable_product.set('product_id', id);
        enable_product.set('is_active', 1);

        axios.post(address + 'Product/manage_product' , enable_product, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response_with_timer('<?php echo $this->lang->line("update_success"); ?>', true, 'Product');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function disable_product(id){
        if (confirm("Are you sure you want to disabled the product ?")) {
            var disable_product = new FormData();
            disable_product.set('access_token', $("#access_token").val());
            disable_product.set('product_id', id);
            disable_product.set('is_active', 0);

            axios.post(address + 'Product/manage_product' , disable_product, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("update_success"); ?>', true, 'Product');
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