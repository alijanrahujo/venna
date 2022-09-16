<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="file-export">
                <div class="row">
                    <?php
                        if($this->user_profile_info['company_id'] == 2 || $this->user_profile_info['company_id'] == 12){
                    ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("shipment_list"); ?> (Best Express) <a href="<?= site_url(); ?>Order/generate_checked_list" target="_blank" class="btn btn-success">Generate Checked List</a></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="template_shipment_dt">
                                            <thead>
                                                <tr>
                                                    <th>Serial No</th>
                                                    <th>Full name</th>
                                                    <th>Phone Number</th>
                                                    <th>Postcode</th>
                                                    <th>Address(excluding continent, county and city)</th>
                                                    <th>Order weigth(kg)</th>
                                                    <th>order Type</th>
                                                    <th>order name</th>
                                                    <th>waybill#</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("shipment_list"); ?> (<?php echo $this->lang->line("pending"); ?>)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="pending_shipment_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("agent_name"); ?></th>
                                                    <th><?php echo $this->lang->line("product"); ?></th>
                                                    <th><?php echo $this->lang->line("shipment_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("tracking_detail"); ?></th>
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
                    <!-- <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("shipment_list"); ?> (Print)</h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="print_shipment_dt">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Full Name</th>
                                                    <th>Phone Number</th>
                                                    <th>Address</th>
                                                    <th>Product</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo $this->lang->line("shipment_list"); ?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="all_shipment_dt">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("agent_name"); ?></th>
                                                    <th><?php echo $this->lang->line("product"); ?></th>
                                                    <th><?php echo $this->lang->line("shipment_detail"); ?></th>
                                                    <th><?php echo $this->lang->line("tracking_detail"); ?></th>
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
        var company_id = "<?php echo $this->user_profile_info['company_id']; ?>";

        drawtable_pending();
        if(company_id == 2 || company_id == 12){
            drawtable_template_pending();
        }
        // drawtable_print_pending();
        drawtable();
	});

    function drawtable_pending(){
		$('#pending_shipment_dt').dataTable().fnClearTable();
    	$('#pending_shipment_dt').dataTable().fnDestroy();
        $('#pending_shipment_dt').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Shipment List',
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
                'url': address + "Order/get_shipment",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'APPROVE',
                    order_status: 'PLACED',
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
                {"data": "upline_name", "orderable": false},
                {"data": "product_name", "orderable": false},
                {"data": "shipment_detail", "orderable": false},
                {"data": "tracking_detail", "orderable": false},
                {"data": "order_status"},
                {"data": "insert_time"},
                {"data": "action", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }

    function drawtable_template_pending(){
		$('#template_shipment_dt').dataTable().fnClearTable();
    	$('#template_shipment_dt').dataTable().fnDestroy();
        $('#template_shipment_dt').DataTable({
            'dom': 'lBfrtip',
            'lengthMenu': [10, 25, 50, 100],
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Shipment List',
                    text: 'Export to Excel',
                    customize: function (xlsx) {
                        console.log(xlsx);
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var downrows = 4;
                        var clRow = $('row', sheet);
                        //update Row
                        clRow.each(function () {
                            var attr = $(this).attr('r');
                            var ind = parseInt(attr);
                            ind = ind + downrows;
                            $(this).attr("r",ind);
                        });
                
                        // Update  row > c
                        $('row c ', sheet).each(function () {
                            var attr = $(this).attr('r');
                            var pre = attr.substring(0, 1);
                            var ind = parseInt(attr.substring(1, attr.length));
                            ind = ind + downrows;
                            $(this).attr("r", pre + ind);
                        });
                
                        function Addrow(index,data) {
                            msg='<row r="'+index+'">'
                            for(i=0;i<data.length;i++){
                                var key=data[i].k;
                                var value=data[i].v;
                                msg += '<c t="inlineStr" r="' + key + index + '">';
                                msg += '<is>';
                                msg +=  '<t>'+value+'</t>';
                                msg+=  '</is>';
                                msg+='</c>';
                            }
                            msg += '</row>';
                            return msg;
                        }
                
                        //insert
                        var r1 = Addrow(1, [{ k: 'A', v: '' }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
                        var r2 = Addrow(2, [{ k: 'A', v: '' }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
                        var r3 = Addrow(3, [{ k: 'A', v: '' }, { k: 'B', v: '' }, { k: 'C', v: '' }]);
                        
                        sheet.childNodes[0].childNodes[1].innerHTML = r1 + r2+ r3+ sheet.childNodes[0].childNodes[1].innerHTML;
                    }
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
                'url': address + "Order/get_shipment",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    status: 'APPROVE',
                    order_status: 'PLACED',
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
                {"data": "id", "orderable": false},
                {"data": "shipment_fullname", "orderable": false},
                {"data": "shipment_phone_no", "orderable": false},
                {"data": "shipment_postcode", "orderable": false},
                {"data": "shipment_address", "orderable": false},
                {"data": "shipment_weight", "orderable": false},
                {"data": "shipping_order_type", "orderable": false},
                {"data": "shipping_order_item", "orderable": false},
                {"data": "shipping_waybill", "orderable": false},
                {"data": "shipping_remark", "orderable": false}
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
        // $('.buttons-excel').exportData( {
            
        // } );
    }

    // function drawtable_print_pending(){
	// 	$('#print_shipment_dt').dataTable().fnClearTable();
    // 	$('#print_shipment_dt').dataTable().fnDestroy();
    //     $('#print_shipment_dt').DataTable({
    //         'dom': 'Bfrtip',
    //         'buttons': [
    //             {
    //                 extend: 'excelHtml5',
    //                 title: 'Shipment List',
    //                 text: 'Export to Excel'
    //             },
    //             {
    //                 extend: 'pdfHtml5',
    //                 title: 'Shipment List',
    //                 text: 'Export to PDF'
    //             },
    //         ],
    //         'order': [],
    //         'serverSide': true,
    //         "bStateSave": true,
    //         "fnStateSave": function (oSettings, oData) {
    //             localStorage.setItem('offersDataTables', JSON.stringify(oData));
    //         },
    //         "fnStateLoad": function (oSettings) {
    //             return JSON.parse(localStorage.getItem('offersDataTables'));
    //         },
    //         'ajax': {
    //             'url': address + "Order/get_shipment",
    //             "type": "POST",
    //             "data": {
    //                 access_token: $("#access_token").val(),
    //                 language: '<?php echo $this->session->userdata("site_lang"); ?>',
    //                 user_id: '<?php echo $this->session->userdata("user_id"); ?>',
    //                 user_type: '<?php echo $this->session->userdata("user_type"); ?>',
    //                 status: 'APPROVE',
    //                 order_status: 'PLACED',
    //                 group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
    //                 company_id: '<?php echo $this->user_profile_info['company_id']; ?>'
    //             },
    //             dataFilter: function (data) {
    //                 var result = jQuery.parseJSON(data);
    //                 if (result.status == "Failed") {
    //                     Swal.fire({
    //                         title: '<?php echo $this->lang->line("warning"); ?>',
    //                         text: result,
    //                         confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
    //                     })
    //                     return JSON.stringify(result.data);
    //                 }else{
    //                     return JSON.stringify(result.data);
    //                 }
    //             }
    //         },
    //         "language": {
    //             "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
    //             "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
    //             "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
    //             "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
    //             "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
    //             "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
    //             "paginate": {
    //             "previous": "<?php echo $this->lang->line("previous"); ?>",
    //             "next": "<?php echo $this->lang->line("next"); ?>",
    //             }
    //         },
    //         "columns": [
    //             {"data": "id", "orderable": false},
    //             {"data": "shipment_fullname", "orderable": false},
    //             {"data": "shipment_phone_no", "orderable": false},
    //             {"data": "shipment_address", "orderable": false},
    //             {"data": "product_item", "orderable": false},
    //             {"data": "shipping_remark", "orderable": false}
    //         ]
    //     });
    //     $('.dt-buttons').addClass('btn-group');
    //     $('.buttons-excel, .buttons-pdf').addClass('btn btn-primary mb-2');
    // }

	function drawtable(){
		$('#all_shipment_dt').dataTable().fnClearTable();
    	$('#all_shipment_dt').dataTable().fnDestroy();
        $('#all_shipment_dt').DataTable({
            'dom': 'Bfrtip',
            'buttons': [
                {
                    extend: 'excelHtml5',
                    title: 'Shipment List',
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
                'url': address + "Order/get_shipment",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $this->session->userdata("user_id"); ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    order_status: 'SHIPPED',
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
                {"data": "upline_name", "orderable": false},
                {"data": "product_name", "orderable": false},
                {"data": "shipment_detail", "orderable": false},
                {"data": "tracking_detail", "orderable": false},
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
</script>