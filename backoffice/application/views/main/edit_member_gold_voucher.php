<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <?php
                                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname", array('id' => $user_id, 'active' => 1));
                                    $fullname = isset($user_info['id']) ? $user_info['fullname'] : "";
                                ?>
                                <h4 class="card-title">Edit Gold Voucher (<?php echo $fullname; ?>)</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" id="add_voucher_form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("free"); ?> Voucher</label><br>
                                                    <?php
                                                        foreach($package as $row_package){
                                                            $package_id = $row_package['id'];
                                                    ?>
                                                    <input type="checkbox" name="package_id[]" value="<?php echo $package_id; ?>">&nbsp;<?php echo $row_package['name']; ?>&nbsp;&nbsp;<input type="text" name="attr[<?php echo $package_id; ?>][total_stock]" placeholder="Total Stock" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 100px;">&nbsp;&nbsp;<input type="number" name="attr[<?php echo $package_id; ?>][quantity]" placeholder="Qty" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="text" name="attr[<?php echo $package_id; ?>][price]" placeholder="Price" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 100px;"><br>
                                                    <?php
                                                        }
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-primary" type="submit">Add</button>
                                        <hr><br>
                                    </form>
                                    <form method="POST" id="edit_promotion_form">
                                        <?php
                                            $user_voucher_list = $this->Api_Model->get_rows(TBL_USER_BIG_PRESENT_FREE, "*", array('user_id' => $user_id));
                                            foreach($user_voucher_list as $row_user_voucher){
                                                $voucher_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $row_user_voucher['package_id'], 'active' => 1));
                                                $voucher_name = isset($voucher_package_info['id']) ? $voucher_package_info['name'] : "";
                                        ?>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Package</label>
                                                    <input type="text" class="form-control" readonly value="<?php echo $voucher_name; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Quantity</label>
                                                    <input type="text" class="form-control" name="attr[<?php echo $row_user_voucher['id'] ?>][quantity]" value="<?php echo $row_user_voucher['quantity']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Status</label>
                                                    <select class="form-control" name="attr[<?php echo $row_user_voucher['id'] ?>][active]">
                                                        <option value="0" <?php if($row_user_voucher['active'] == 0){ echo "selected"; } ?>>Deactive</option>
                                                        <option value="1" <?php if($row_user_voucher['active'] == 1){ echo "selected"; } ?>>Active</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <input type="hidden" name="user_voucher_id[]" value="<?php echo $row_user_voucher['id'] ?>">
                                        <?php
                                            }
                                        ?>
                                        <br>
                                        <input type="hidden" id="promotion_user_id" value="<?php echo $user_id; ?>">
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </form>
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
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Member/gold";
    });

    $('#edit_promotion_form').submit(function(e) {
        e.preventDefault();

        var update_gold_voucher = new FormData(this);
        update_gold_voucher.set('access_token', $("#access_token").val());
        update_gold_voucher.set('user_id', $("#promotion_user_id").val());

        axios.post(address + 'Member/update_gold_voucher' , update_gold_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Successfully !', true, 'Member/gold');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    $('#add_voucher_form').submit(function(e) {
        e.preventDefault();

        var insert_gold_voucher = new FormData(this);
        insert_gold_voucher.set('access_token', $("#access_token").val());
        insert_gold_voucher.set('user_id', $("#promotion_user_id").val());

        axios.post(address + 'Member/insert_gold_voucher' , insert_gold_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Successfully !', true, 'Member/golde/' + response.data.data);
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