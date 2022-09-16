 <?php if ($this->user_profile_info['company_id'] == 7)  {?>  
<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="stock_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("manage_stock_auto"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">                                      
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Agent Username / Agent Phone Number</label>
                                                    <select class="form-control" id="type" name="type" required>
                                                        <option value="1">Username</option>
                                                        <option value="0">Phone Number</option>
                                                    </select>
                                                    <br>
                                                    <input type="text" class="form-control" id="ausername" name="ausername" required>
                                                </fieldset>
                                            </div>
                                        </div>

                                    <!--      <b>OR</b>

                                        </br></br>

                                       <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Phone Number</label>
                                                    <input type="text" class="form-control" id="aphone_no" name="aphone_no">
                                                </fieldset>
                                            </div>
                                        </div> -->
                                            
                                        <br>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $company_id = $this->user_profile_info['company_id'];
                            ?>
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>">
                        </form>
                    </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"><?php echo "User List"?></h4>
                            </div>
                            <div class="card-content ">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table dt-responsive" id="display_user_list">
                                            <thead>
                                                <tr>
                                                    <th><?php echo $this->lang->line("username"); ?></th>
                                                    <th><?php echo $this->lang->line("phone_no"); ?></th>
                                                    <th><?php echo $this->lang->line("address"); ?></th>
                                                    <th><?php echo "Email" ?></th>
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
                                </div>
                            </div>
                   
                        </form>     
                    </div>     

                </div>
            </section>
        </div>
    </div>
</div><?php } ?>

<script>
    $('#stock_form').submit(function(e) {
        e.preventDefault();

        if (company_id == 7){

        var edit_stock_auto = new FormData(this);
        edit_stock_auto.set('access_token', $("#access_token").val());
        //edit_stock_auto.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Company/edit_stock_auto' , edit_stock_auto, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Success !', true, 'Company/stock_auto');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
   }
    });
</script>

<script type="text/javascript">
	$(document).ready(function(){
        var company_id = "<?php echo $this->user_profile_info['company_id']; ?>";
        
        if (company_id == 7){display_user_list();}

        
        
	});


    function display_user_list(){
        $('#display_user_list').dataTable().fnClearTable();
    	$('#display_user_list').dataTable().fnDestroy();
        $('#display_user_list').DataTable({
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
                'url': address + "Company/get_user_list",
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
                {"data": "username", "orderable": false},
                {"data": "phone_no", "orderable": false},
                {"data": "address", "orderable": false},
                {"data": "email", "orderable": false},
            ]
        });
        $('.dt-buttons').addClass('btn-group');
        $('.buttons-excel').addClass('btn btn-primary mb-2');
    }
</script>