<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Request Withdraw</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Available Balance</label>
                                                    <input type="text" class="form-control" value="<?php echo $available_balance; ?>" readonly>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Amount</label>
                                                    <input type="text" class="form-control" id="amount" name="amount" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Password</label>
                                                    <input type="password" class="form-control" id="password" name="password" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Withdraw List </h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="withdraw_dt">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Full Name</th>
                                                    <th>Available Balance</th>
                                                    <th>Amount</th>
                                                    <th>Description</th>
                                                    <th>Status</th>
                                                    <th>Request Date</th>
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

<script>
    $(document).ready(function(){
        drawtable();
	});

	function drawtable(){
		$('#withdraw_dt').dataTable().fnClearTable();
    	$('#withdraw_dt').dataTable().fnDestroy();
        $('#withdraw_dt').DataTable({
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
                'url': address + "Withdraw/get_withdraw",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'PENDING'
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
                {"data": "fullname"},
                {"data": "available_balance"},
                {"data": "amount"},
                {"data": "description"},
                {"data": "status"},
                {"data": "insert_time"}
            ]
        });
    }

    $('#form').submit(function(e) {
        e.preventDefault();
        
        var insert_withdraw = new FormData(this);
        insert_withdraw.set('access_token', $("#access_token").val());
        insert_withdraw.set('user_id', $("#id").val());

        axios.post(address + 'Withdraw/insert_withdraw' , insert_withdraw, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Request Success ! Please wait admin for approve within 1 days !', true, 'Withdraw');
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