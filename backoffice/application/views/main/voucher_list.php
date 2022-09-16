<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("big_present_list"); ?> <a href="<?= site_url(); ?>Voucher/add"><i class="fa fa-plus-circle add-icon"></i></a></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="voucher_dt" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Used By</th>
                                                    <th><?php echo $this->lang->line("code"); ?></th>
                                                    <th><?php echo $this->lang->line("free"); ?> <?php echo $this->lang->line("package"); ?></th>
                                                    <th><?php echo $this->lang->line("balance_quantity"); ?></th>
                                                    <th><?php echo $this->lang->line("price"); ?></th>
                                                    <th><?php echo $this->lang->line("total_stock"); ?></th>
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

<div class="modal fade text-left" id="voucherModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Agent Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="ft-x font-medium-2 text-bold-700"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="voucher_form">
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="basicInput">Agent Username</label>
                                <input type="text" class="form-control" id="agent_name" name="agent_name">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h6 id='display' onClick='copyText(this)'></h6>
                        </div>
                    </div>
                    <input type="hidden" name="voucher_id" id="voucher_id">
                    <br>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        drawtable();
	});

	function drawtable(){
		$('#voucher_dt').dataTable().fnClearTable();
    	$('#voucher_dt').dataTable().fnDestroy();
        $('#voucher_dt').DataTable({
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
                'url': address + "Voucher/get_voucher",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    admin_id: '<?php echo $this->user_profile_info['id']; ?>'
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
                {"data": "agent_name", "orderable": false},
                {"data": "code"},
                {"data": "package", "orderable": false},
                {"data": "balance_quantity"},
                {"data": "price"},
                {"data": "total_stock"},
                {"data": "action"}
            ]
        });
    }

    function delete_voucher(id){
        if (confirm("<?php echo $this->lang->line("are_you_sure_you_want_to_delete"); ?> ?")) {
            var delete_voucher = new FormData();
            delete_voucher.set('access_token', $("#access_token").val());
            delete_voucher.set('id', id);

            axios.post(address + 'Voucher/delete_voucher' , delete_voucher, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('<?php echo $this->lang->line("delete_success"); ?>', true, 'Voucher');
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

    function share_link(voucher_id){
        $("#voucher_id").val(voucher_id);
        $("#voucherModal").modal("show");
    }

    function copyText(element) {
        var range, selection, worked;

        if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(element);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();        
            range = document.createRange();
            range.selectNodeContents(element);
            selection.removeAllRanges();
            selection.addRange(range);
        }
        
        try {
            document.execCommand('copy');
            success_response_with_timer("Copied");
        }
        catch (err) {
            alert('unable to copy text');
        }
    }

    $('#voucher_form').submit(function(e) {
        e.preventDefault();

        var check_referral_username = new FormData(this);
        check_referral_username.set('access_token', $("#access_token").val());
        check_referral_username.set('insert_by', $("#insert_by").val());
        check_referral_username.set('user_id', $("#user_id").val());

        axios.post(address + 'Voucher/check_referral_username' , check_referral_username, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $("#display").html(response.data.data);
                // $("#voucherModal").modal("hide");
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