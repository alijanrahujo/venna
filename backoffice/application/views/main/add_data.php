<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="agent_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_agent"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $this->uri->segment(3); ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Upline</label>
                                                    <select class="form-control" id="referral_id" name="referral_id" required>
                                                        <option value="0">Select Upline</option>
                                                        <?php
                                                            $member_list = $this->Api_Model->get_rows(TBL_USER, "*", array('company_id' => $this->uri->segment(3), 'active' => 1, 'user_type' => "AGENT"));

                                                            foreach($member_list as $row_member){
                                                        ?>
                                                        <option value="<?php echo $row_member['id']; ?>"><?php echo $row_member['fullname']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Country</label>
                                                    <select class="form-control" id="country_id" name="country_id" required onchange="display_package(this)">
                                                        <option value="0">Select Country</option>
                                                        <?php
                                                            $country_list = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $this->uri->segment(3), 'active' => 1));

                                                            foreach($country_list as $row_country){
                                                        ?>
                                                        <option value="<?php echo $row_country['id']; ?>"><?php echo $row_country['name']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Package</label>
                                                    <select class="form-control" name="package_id" id="package_id" disabled>
                                                        
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("fullname"); ?></label>
                                                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("username"); ?></label>
                                                    <input type="text" class="form-control" id="username" name="username" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Phone No</label>
                                                    <input type="phone" class="form-control" id="phone_no" name="phone_no" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">IC No</label>
                                                    <input type="number" class="form-control" id="ic_no" name="ic_no" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("email_address"); ?></label>
                                                    <input type="email" class="form-control" id="email" name="email" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        <h4 class="card-title">Optional</h4>
                                        <br>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Voucher ?</label>
                                                    <select class="form-control" name="is_voucher" id="is_voucher" onchange="select_voucher(this)">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="row" id="voucher_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Voucher</label>
                                                    <select class="form-control" id="voucher_id" name="voucher_id" required>
                                                        <option value="0">Select Voucher</option>
                                                        <?php
                                                            $voucher_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT, "*", array('company_id' => $this->uri->segment(3), 'active' => 1, 'balance_quantity !=' => 0));

                                                            foreach($voucher_list as $row_voucher){
                                                        ?>
                                                        <option value="<?php echo $row_voucher['id']; ?>"><?php echo $row_voucher['code']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <?php
                                            $voucher_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT, "*", array('company_id' => $this->uri->segment(3), 'active' => 1, 'balance_quantity !=' => 0));

                                            if(!empty($voucher_list)){
                                        ?>
                                        <div class="row" id="balance_voucher_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Balance Voucher ?</label>
                                                    <select class="form-control" name="is_balance_voucher" id="is_balance_voucher" onchange="select_balance_voucher(this)">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>

                                        <div class="row" id="balance_voucher_input_box" style="display: none;">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Voucher</label>
                                                    <div class="row voucher_input_fields_wrap">
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="voucher_package_id" name="voucher_package_id[]">
                                                                <option value="0">Select Voucher Package</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" class="form-control" id="attr[0][voucher_package_quantity]" name="attr[0][voucher_package_quantity]" placeholder="Qty">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button class="btn btn-success add_voucher_button">Add</button>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Address</label>
                                                    <input type="text" class="form-control" id="address" name="address">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">City</label>
                                                    <input type="text" class="form-control" id="city" name="city">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">State</label>
                                                    <input type="text" class="form-control" id="state" name="state">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Postcode</label>
                                                    <input type="number" class="form-control" id="postcode" name="postcode">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Bank Name</label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Account Name</label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Account No</label>
                                                    <input type="number" class="form-control" id="account_no" name="account_no">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Total Stock</label>
                                                    <input type="number" class="form-control" id="total_stock" name="total_stock">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">CB Point</label>
                                                    <input type="text" class="form-control" id="cb_point" name="cb_point">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">DRB</label>
                                                    <input type="text" class="form-control" id="drb" name="drb">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">RB</label>
                                                    <div class="row input_fields_wrap">
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="rb_package_id" name="rb_package_id[]">
                                                                <option value="0">Select RB Package</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" class="form-control" id="attr[0][rb_quantity]" name="attr[0][rb_quantity]" placeholder="Qty">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button class="btn btn-success add_field_button">Add</button>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Balance Commision</label>
                                                    <input type="text" class="form-control" id="balance_commision" name="balance_commision">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    function select_voucher(data){
        if(data.value == 1){
            $("#balance_voucher_box").css("display", "block");
            $("#voucher_box").css("display", "block");
        }else{
            $("#balance_voucher_box").css("display", "none");
            $("#voucher_box").css("display", "none");
        }
    }

    function select_balance_voucher(data){
        if(data.value == 1){
            $("#balance_voucher_input_box").css("display", "block");

            var get_package = new FormData();
            get_package.set('company_id', $("#company_id").val());
            get_package.set('country_id', $("#country_id").val());

            axios.post(address + 'Forms/get_package' , get_package, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    package_list = response.data.data;
                    if (package_list == "") {
                        $("#voucher_package_id").prop("disabled", true);
                        $('#voucher_package_id').empty().append($('<option value="0">').text("No Package Found"));
                    } else {
                        $("#voucher_package_id").prop("disabled", false);
                        $('#voucher_package_id').empty().append($('<option value="0">').text("Select Package"));
                        for (var i = 0; i < package_list.length; i++) {
                            $('#voucher_package_id').append($('<option value="' + package_list[i].id + '">').text(package_list[i].name));
                        }
                    }
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });
        }else{
            $("#balance_voucher_input_box").css("display", "none");
        }
    }

    var package_list = [];

    $(document).ready(function(){
        $("input[name='pincode']").on('input', function(e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });
        $("input[name='cfm_pincode']").on('input', function(e) {
            $(this).val($(this).val().replace(/[^0-9]/g, ''));
        });

        var html = "";
        var add_button = $(".add_field_button");
        var add_voucher_button = $(".add_voucher_button");
        var x = 0;

        $(add_button).on("click",function(e){
            e.preventDefault();
            
            x++; //text box increment
            html = "";

            html += '<div class="col-md-8" id="box1_fields_wrap_' + x + '">';
            html += '<select class="form-control" id="package_id_box_' + x + '" name="rb_package_id[]">';
            html += '</select>';
            html += '</div>';
            html += '<div class="col-md-2" id="box2_fields_wrap_' + x + '">';
            html += '<input type="number" id="attr[' + x + '][rb_quantity]" class="form-control" name="attr[' + x + '][rb_quantity]" placeholder="Qty">';
            html += '</div>';
            html += '<div class="col-md-2" id="box3_fields_wrap_' + x + '">';
            html += '<button type="button" onclick="remove_box(' + x + ')" class="btn btn-danger remove_field_button">Remove</button>';
            html += '</div>';
            $(".input_fields_wrap").append(html); //add input box
            
            var get_package = new FormData();
            get_package.set('access_token', $("#access_token").val());
            get_package.set('company_id', $("#company_id").val());
            get_package.set('country_id', $("#country_id").val());

            axios.post(address + 'Forms/get_package' , get_package, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    $("#package_id_box_" + x).html("");
                    var package_option = "";
                    package_list = response.data.data;
                    $.each(package_list, function(i, data2) {
                        package_option += '<option value="' + data2.id + '">' + data2.name + '</option>';
                    });
                    $("#package_id_box_" + x).append(package_option);
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });

        });

        var y = 0;

        $(add_voucher_button).on("click",function(e){
            e.preventDefault();
            
            y++; //text box increment
            html = "";

            html += '<div class="col-md-8" id="box1_voucher_fields_wrap_' + y + '">';
            html += '<select class="form-control" id="voucher_package_id_box_' + y + '" name="voucher_package_id[]">';
            html += '</select>';
            html += '</div>';
            html += '<div class="col-md-2" id="box2_voucher_fields_wrap_' + y + '">';
            html += '<input type="number" id="attr[' + y + '][voucher_package_quantity]" class="form-control" name="attr[' + y + '][voucher_package_quantity]" placeholder="Qty">';
            html += '</div>';
            html += '<div class="col-md-2" id="box3_voucher_fields_wrap_' + y + '">';
            html += '<button type="button" onclick="remove_voucher_box(' + y + ')" class="btn btn-danger remove_field_button">Remove</button>';
            html += '</div>';
            $(".voucher_input_fields_wrap").append(html); //add input box
            
            var get_package = new FormData();
            get_package.set('access_token', $("#access_token").val());
            get_package.set('company_id', $("#company_id").val());
            get_package.set('country_id', $("#country_id").val());

            axios.post(address + 'Forms/get_package' , get_package, apiHeader)
            .then(function (response) {
                if(response.data.status == "Success"){
                    $("#voucher_package_id_box_" + y).html("");
                    var package_option = "";
                    package_list = response.data.data;
                    $.each(package_list, function(i, data2) {
                        package_option += '<option value="' + data2.id + '">' + data2.name + '</option>';
                    });
                    $("#voucher_package_id_box_" + y).append(package_option);
                }else{
                    warning_response(response.data.message);
                }
            })
            .catch(function (data) {
                console.log(data);
                error_response();
            });

        });
	});

    function remove_box(id){
        $("#box1_fields_wrap_" + id).remove();
        $("#box2_fields_wrap_" + id).remove();
        $("#box3_fields_wrap_" + id).remove();
    }

    function remove_voucher_box(id){
        $("#box1_voucher_fields_wrap_" + id).remove();
        $("#box2_voucher_fields_wrap_" + id).remove();
        $("#box3_voucher_fields_wrap_" + id).remove();
    }

    function display_package(data){
        var get_package = new FormData();
        get_package.set('company_id', $("#company_id").val());
        get_package.set('country_id', data.value);

        axios.post(address + 'Forms/get_package' , get_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                package_list = response.data.data;
                if (package_list == "") {
                    $("#package_id").prop("disabled", true);
                    $('#package_id').empty().append($('<option value="0">').text("No Package Found"));
                } else {
                    $("#package_id").prop("disabled", false);
                    $('#package_id').empty().append($('<option value="0">').text("Select Package"));
                    for (var i = 0; i < package_list.length; i++) {
                        $('#package_id').append($('<option value="' + package_list[i].id + '">').text(package_list[i].name));
                        $('#rb_package_id').append($('<option value="' + package_list[i].id + '">').text(package_list[i].name));
                    }
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

    $('#agent_form').submit(function(e) {
        e.preventDefault();

        var insert_data = new FormData(this);

        axios.post(address + 'Forms/insert_data' , insert_data, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Forms/add/' + "<?php echo $this->uri->segment(3); ?>");
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