<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="voucher_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_big_present"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <?php
                                            $user_type = $this->user_profile_info['user_type'];
                                            $group_id = $this->user_profile_info['group_id'];
                                            $company_id = $this->user_profile_info['company_id'];

                                            if($group_id == 1 && $user_type == "ADMIN"){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("company"); ?></label>
                                                    <select class="form-control" id="company_id" name="company_id">
                                                        <option value="0"><?php echo $this->lang->line("select_brand"); ?></option>
                                                        <?php
                                                            foreach($company as $row_company){
                                                        ?>
                                                        <option value="<?php echo $row_company['id']; ?>"><?php echo $row_company['name']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }else{
                                        ?>
                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $this->user_profile_info['company_id']; ?>">
                                        <?php
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("country"); ?></label>
                                                    <select class="form-control" id="country_id" name="country_id" onchange="select_brand(this)">
                                                        <option value="0"><?php echo $this->lang->line("select_country"); ?></option>
                                                        <?php
                                                            foreach($country as $row_country){
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
                                                    <label for="basicInput"><?php echo $this->lang->line("package"); ?></label>
                                                    <select class="form-control" id="set_package_id" name="set_package_id" disabled>
                                                        <option value="0">Select Package</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("code"); ?></label>
                                                    <input type="text" class="form-control" id="code" name="code" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("quantity"); ?></label>
                                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("price"); ?></label>
                                                    <input type="text" class="form-control" id="price" name="price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                                            $company_type = isset($company_info['type']) ? $company_info['type'] : "";

                                            if($company_type == "FIXED"){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("total_stock"); ?></label>
                                                    <input type="number" class="form-control" id="total_stock" name="total_stock">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }else{
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Total PV</label>
                                                    <input type="text" class="form-control" id="total_point" name="total_point">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("is_paid_to_company"); ?></label>
                                                    <select class="form-control" id="is_company" name="is_company" required>
                                                        <option value="0"><?php echo $this->lang->line("no"); ?></option>
                                                        <option value="1"><?php echo $this->lang->line("yes"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("free"); ?> <?php echo $this->lang->line("package"); ?></label><br>
                                                    <div id="package_list"></div>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <input type="hidden" name="group_id" value="<?php echo $this->user_profile_info['group_id']; ?>">
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
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
    var package_list = [];

    function select_brand(data){
        var get_package = new FormData();
        get_package.set('access_token', $("#access_token").val());
        get_package.set('company_id', $("#company_id").val());
        get_package.set('country_id', data.value);

        axios.post(address + 'Voucher/get_package' , get_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                package_list = response.data.data;
                if(package_list == ""){
                    $("#set_package_id").prop("disabled", true);
                    $('#set_package_id').empty().append($('<option value="0">').text("No Brand Found"));
                }else{
                    $("#set_package_id").prop("disabled", false);
                    $('#set_package_id').empty().append($('<option value="0">').text("Select Brand"));
                    for (var i = 0; i < package_list.length; i++) {
                        $('#set_package_id').append($('<option value="' + package_list[i].id + '">').text(package_list[i].name));
                    }

                    var get_package = new FormData();
                    get_package.set('access_token', $("#access_token").val());
                    get_package.set('company_id', $("#company_id").val());
                    get_package.set('country_id', data.value);

                    axios.post(address + 'Voucher/get_package' , get_package, apiHeader)
                    .then(function (response) {
                        if(response.data.status == "Success"){
                            display_package(response.data.data);
                        }else{
                            warning_response(response.data.message);
                        }
                    })
                    .catch(function (data) {
                        console.log(data);
                        error_response();
                    });
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
    
    function display_package(json_response){
        $("#package_list").html("");
        var package_list = ""
        $.each(json_response, function(i, data) {
            package_list += '<input type="checkbox" name="package_id[]" value="' + data.id + '">&nbsp;' + data.name + '&nbsp;&nbsp;<input type="text" name="attr[' + data.id + '][price]" placeholder="Price" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[' + data.id + '][quantity]" placeholder="Qty" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>';
        });
        $("#package_list").append(package_list);
    }

    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Voucher";
    });

    $('#voucher_form').submit(function(e) {
        e.preventDefault();

        var insert_voucher = new FormData(this);
        insert_voucher.set('access_token', $("#access_token").val());
        insert_voucher.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Voucher/insert_voucher' , insert_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Voucher');
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