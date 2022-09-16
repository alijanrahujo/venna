<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("order_list"); ?> (<?php echo $this->lang->line("pending"); ?>)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="pending_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?>/<?php echo $this->lang->line("agent_name"); ?></th>
                                                    <th><?php echo $this->lang->line("product"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("order"); ?></th>
                                                    <th><?php echo $this->lang->line("shipment"); ?></th>
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
                                <h4 class="card-title"><?php echo $this->lang->line("order_list"); ?> (<?php echo $this->lang->line("approve"); ?>)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="approve_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?>/<?php echo $this->lang->line("agent_name"); ?></th>
                                                    <th><?php echo $this->lang->line("product"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("order"); ?></th>
                                                    <th><?php echo $this->lang->line("shipment"); ?></th>
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
                                <h4 class="card-title"><?php echo $this->lang->line("order_list"); ?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="all_order_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("referral"); ?>/<?php echo $this->lang->line("agent_name"); ?></th>
                                                    <th><?php echo $this->lang->line("product"); ?></th>
                                                    <th><?php echo $this->lang->line("payment"); ?></th>
                                                    <th><?php echo $this->lang->line("order"); ?></th>
                                                    <th><?php echo $this->lang->line("shipment"); ?></th>
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

<div class="modal fade text-left" id="shipmentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1">Shipment Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="ft-x font-medium-2 text-bold-700"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="shipment_form">
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="basicInput">Delivery Company</label>
                                <input type="text" class="form-control" id="delivery_company" name="delivery_company">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="basicInput">Tracking No</label>
                                <input type="text" class="form-control" id="tracking_no" name="tracking_no">
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="basicInput">Tracking Url</label>
                                <input type="text" class="form-control" id="tracking_url" name="tracking_url">
                            </fieldset>
                        </div>
                    </div>
                    <input type="hidden" name="order_id" id="order_id">
                    <br>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        drawtable_pending();
        drawtable_approve();
        drawtable();
	});

    function drawtable_pending(){
		$('#pending_order_dt').dataTable().fnClearTable();
    	$('#pending_order_dt').dataTable().fnDestroy();
        $('#pending_order_dt').DataTable({
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
                'url': address + "Order/get_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'PENDING',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
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
                {"data": "agent_name", "orderable": false},
                {"data": "product_name", "orderable": false},
                {"data": "payment_status"},
                {"data": "status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
    }

    function drawtable_approve(){
		$('#approve_order_dt').dataTable().fnClearTable();
    	$('#approve_order_dt').dataTable().fnDestroy();
        $('#approve_order_dt').DataTable({
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
                'url': address + "Order/get_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'APPROVE',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
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
                {"data": "agent_name", "orderable": false},
                {"data": "product_name", "orderable": false},
                {"data": "payment_status"},
                {"data": "status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
    }

	function drawtable(){
		$('#all_order_dt').dataTable().fnClearTable();
    	$('#all_order_dt').dataTable().fnDestroy();
        $('#all_order_dt').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Order List',
                    text: 'Export to Excel'
                },
            ],
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
                'url': address + "Order/get_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
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
                {"data": "agent_name", "orderable": false},
                {"data": "product_name", "orderable": false},
                {"data": "payment_status"},
                {"data": "status"},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }

    function show_order_receipt(id){
        var view_receipt = new FormData();
        view_receipt.set('access_token', $("#access_token").val());
        view_receipt.set('id', id);

        axios.post(address + 'Order/view_order_receipt' , view_receipt, apiHeader)
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
        var check_product_order = new FormData();
        check_product_order.set('access_token', $("#access_token").val());
        check_product_order.set('id', id);

        axios.post(address + 'Order/check_product_order' , check_product_order, apiHeader)
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

        axios.post(address + 'Order/approve_product_order' , approve_order, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response_with_timer('<?php echo $this->lang->line("approved"); ?>', true, 'Order');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function update_order_status(id){
        var get_shipment_status = new FormData();
        get_shipment_status.set('access_token', $("#access_token").val());
        get_shipment_status.set('order_id', id);

        axios.post(address + 'Order/get_shipment_status' , get_shipment_status, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $("#order_id").val(id);
                $("#delivery_company").val(response.data.data.delivery_company);
                $("#tracking_no").val(response.data.data.tracking_no);
                $("#tracking_url").val(response.data.data.tracking_url);
                $("#shipmentModal").modal("show");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    $('#shipment_form').submit(function(e) {
        e.preventDefault();

        var update_shipment = new FormData(this);
        update_shipment.set('access_token', $("#access_token").val());
        update_shipment.set('update_by', $("#insert_by").val());

        axios.post(address + 'Order/update_order_status' , update_shipment, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Order/shipment');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });
    
    function cancel_order(id){
        if (confirm("Are you sure you want to cancel the selected order ?")) {
            var cancel_retail_order = new FormData();
            cancel_retail_order.set('access_token', $("#access_token").val());
            cancel_retail_order.set('order_id', id);

            axios.post(address + 'Order/cancel_retail_order' , cancel_retail_order, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Cancelled !', true, 'Order');
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
    
    function refund_stock_order(id){
        if (confirm("Are you sure you want to cancel and refund stock to selected order ?")) {
            var refund_stock_order = new FormData();
            refund_stock_order.set('access_token', $("#access_token").val());
            refund_stock_order.set('order_id', id);

            axios.post(address + 'Order/refund_stock_order' , refund_stock_order, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Cancelled !', true, 'Order');
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

    function revert_cancel_order(id){
        if (confirm("Are you sure you want to revert to selected order ?")) {
            var revert_cancel_order = new FormData();
            revert_cancel_order.set('access_token', $("#access_token").val());
            revert_cancel_order.set('order_id', id);

            axios.post(address + 'Order/revert_cancel_order' , revert_cancel_order, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    success_response_with_timer('Reverted !', true, 'Order');
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