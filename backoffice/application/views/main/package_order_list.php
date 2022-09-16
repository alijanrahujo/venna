<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("package_order_list"); ?> (<?php echo $this->lang->line("pending"); ?>)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="pending_package_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?></th>
                                                    <th><?php echo $this->lang->line("agent_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("package_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("package"); ?></th>
                                                    <th><?php echo $this->lang->line("datetime"); ?></th>
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("package_order_list"); ?> (<?php echo $this->lang->line("approve"); ?>)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="approve_package_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?></th>
                                                    <th><?php echo $this->lang->line("agent_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("package_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("package"); ?></th>
                                                    <th><?php echo $this->lang->line("datetime"); ?></th>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("package_order_list"); ?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="all_package_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?></th>
                                                    <th><?php echo $this->lang->line("agent_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("package_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("package"); ?></th>
                                                    <th><?php echo $this->lang->line("datetime"); ?></th>
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

<div class="modal fade text-left" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1"><?php echo $this->lang->line("payment_receipt"); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="ft-x font-medium-2 text-bold-700"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <img id="payment_receipt" style="width: 100%;">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        drawtable();
        drawtable_pending();
        drawtable_approve();
	});

	function drawtable_pending(){
		$('#pending_package_order_dt').dataTable().fnClearTable();
    	$('#pending_package_order_dt').dataTable().fnDestroy();
        $('#pending_package_order_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_package_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    admin_id: '<?php echo $this->user_profile_info['id']; ?>',
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
                {"data": "referral_name"},
                {"data": "agent_detail"},
                {"data": "package_detail"},
                {"data": "payment_status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function drawtable_approve(){
		$('#approve_package_order_dt').dataTable().fnClearTable();
    	$('#approve_package_order_dt').dataTable().fnDestroy();
        $('#approve_package_order_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_package_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    admin_id: '<?php echo $this->user_profile_info['id']; ?>',
                    status: "APPROVE"
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
                {"data": "referral_name"},
                {"data": "agent_detail"},
                {"data": "package_detail"},
                {"data": "payment_status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function drawtable(){
		$('#all_package_order_dt').dataTable().fnClearTable();
    	$('#all_package_order_dt').dataTable().fnDestroy();
        $('#all_package_order_dt').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Package Order List',
                    text: 'Export to Excel'
                },
            ],
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_package_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
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
                {"data": "referral_name"},
                {"data": "agent_detail"},
                {"data": "package_detail"},
                {"data": "payment_status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }

    function show_payment_receipt(id){
        var view_receipt = new FormData();
        view_receipt.set('access_token', $("#access_token").val());
        view_receipt.set('id', id);

        axios.post(address + 'Order/view_payment_receipt' , view_receipt, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $("#payment_receipt").attr("src", response.data.data.payment_receipt);
                $("#paymentModal").modal("show");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function approve_order(id){
        var check_package_order = new FormData();
        check_package_order.set('access_token', $("#access_token").val());
        check_package_order.set('id', id);

        axios.post(address + 'Order/check_package_order' , check_package_order, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                if(response.data.data.payment_status == "UNPAID"){
                    Swal.fire({
                        text: "It is an unpaid order, do you want to approve ?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '<?php echo $this->lang->line("yes"); ?>'
                    }).then((result) => {
                        if (result.value) {
                            proceed_approve_order(id);
                        }
                    })
                }else{
                    proceed_approve_order(id);
                }
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function proceed_approve_order(id){
        var approve_order = new FormData();
        approve_order.set('access_token', $("#access_token").val());
        approve_order.set('id', id);

        axios.post(address + 'Order/approve_package_order' , approve_order, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response_with_timer('<?php echo $this->lang->line("approved"); ?>', true, 'Order/package');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }
    
    function cancel_order(id){
        if (confirm("Are you sure you want to cancel the selected package order ?")) {
            var cancel_package_order = new FormData();
            cancel_package_order.set('access_token', $("#access_token").val());
            cancel_package_order.set('order_id', id);

            axios.post(address + 'Order/cancel_package_order' , cancel_package_order, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Cancelled !', true, 'Order/package');
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

    function refund_package_order(id){
        if (confirm("Are you sure you want to cancel the selected order ?")) {
            var refund_package_order = new FormData();
            refund_package_order.set('access_token', $("#access_token").val());
            refund_package_order.set('package_id', id);

            axios.post(address + 'Order/refund_package_order' , refund_package_order, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Cancelled !', true, 'Order/package');
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