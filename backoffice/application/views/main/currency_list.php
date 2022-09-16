<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("currency_list"); ?> <a href="<?= site_url(); ?>Currency/add/<?php echo $this->uri->segment(3); ?>"><i class="fa fa-plus-circle add-icon"></i> <a class="btn btn-success" style="float: right;" href="#" onclick="back_to_currency(); return false"><?php echo $this->lang->line("back"); ?></a></a></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="currency_dt" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php echo $this->lang->line("name"); ?></th>
                                                    <th><?php echo $this->lang->line("code"); ?></th>
                                                    <th><?php echo $this->lang->line("exchange_rate"); ?> (1 - ???)</th>
                                                    <th><?php echo $this->lang->line("action"); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                        <?php
                                            $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
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
    function back_to_currency(){
        window.location.href = "<?= site_url(); ?>Company";
    }

	$(document).ready(function(){
        drawtable();
	});

	function drawtable(){
		$('#currency_dt').dataTable().fnClearTable();
    	$('#currency_dt').dataTable().fnDestroy();
        $('#currency_dt').DataTable({
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
                'url': address + "Currency/get_currency",
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
                {"data": "count", "orderable": false},
                {"data": "name"},
                {"data": "code"},
                {"data": "exchange_rate"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function delete_currency(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_currency = new FormData();
            delete_currency.set('access_token', $("#access_token").val());
            delete_currency.set('id', id);

            axios.post(address + 'Currency/delete_currency' , delete_currency, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Currency/view/' + "<?php echo $this->uri->segment(3); ?>");
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