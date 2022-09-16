<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Promotion Voucher</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form method="POST" id="add_promotion_form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("free"); ?> Voucher</label><br>
                                                    <?php
                                                        foreach($voucher as $row_voucher){
                                                            $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name", array('id' => $row_voucher['product_id'], 'active' => 1));
                                                            $product_id = isset($product_info['id']) ? $product_info['id'] : 0;
                                                            $voucher_name = isset($product_info['id']) ? $product_info['name'] : "Any Product";
                                                    ?>
                                                    <input type="checkbox" name="product_id[]" value="<?php echo $product_id; ?>">&nbsp;<?php echo $voucher_name; ?>&nbsp;&nbsp;<input type="text" name="attr[<?php echo $product_id; ?>][price]" placeholder="Price" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[<?php echo $product_id; ?>][quantity]" placeholder="Qty" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
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
                                            $user_voucher_list = $this->Api_Model->get_rows(TBL_USER_VOUCHER, "*", array('user_id' => $user_id));
                                            foreach($user_voucher_list as $row_user_voucher){
                                                $voucher_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE_VOUCHER, "*", array('product_id' => $row_user_voucher['product_id'], 'active' => 1));
                                                $voucher_product_id = isset($voucher_package_info['id']) ? $voucher_package_info['product_id'] : 0;
                                                $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name", array('id' => $voucher_product_id, 'active' => 1));
                                                $voucher_name = isset($product_info['id']) ? $product_info['name'] : "Any Product";
                                        ?>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Voucher</label>
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
        window.location.href = "<?= site_url(); ?>Member/promotion";
    });

    $('#edit_promotion_form').submit(function(e) {
        e.preventDefault();

        var update_promotion_voucher = new FormData(this);
        update_promotion_voucher.set('access_token', $("#access_token").val());
        update_promotion_voucher.set('user_id', $("#promotion_user_id").val());

        axios.post(address + 'Member/update_promotion_voucher' , update_promotion_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Successfully !', true, 'Member/promotion');
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });
    
    $('#add_promotion_form').submit(function(e) {
        e.preventDefault();

        var insert_promotion_voucher = new FormData(this);
        insert_promotion_voucher.set('access_token', $("#access_token").val());
        insert_promotion_voucher.set('user_id', $("#promotion_user_id").val());

        axios.post(address + 'Member/insert_promotion_voucher' , insert_promotion_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Add Successfully !', true, 'Member/promotion');
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