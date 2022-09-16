<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="package_form">
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Package</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Chinese <?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" name="name" value="<?php echo $edit['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">English <?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" name="english_name" value="<?php echo $edit['english_name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("unit"); ?></label>
                                                    <input type="text" class="form-control" name="unit" value="<?php echo $edit['unit']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("quantity"); ?></label>
                                                    <input type="number" class="form-control" name="quantity" value="<?php echo $edit['quantity']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Free <?php echo $this->lang->line("quantity"); ?></label>
                                                    <input type="number" class="form-control" name="free_quantity" value="<?php echo $edit['free_quantity']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("unit_price"); ?></label>
                                                    <input type="text" class="form-control" name="unit_price" value="<?php echo $edit['unit_price']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("grand_total"); ?></label>
                                                    <input type="text" class="form-control" name="grand_total" value="<?php echo $edit['grand_total']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Box (optional)</label>
                                                    <input type="number" class="form-control" name="box" value="<?php echo $edit['box']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("is_paid_to_company"); ?></label>
                                                    <select class="form-control" id="is_company" name="is_company" required>
                                                        <option value="0" <?php if($edit['is_company'] == 0){ echo "selected"; } ?>><?php echo $this->lang->line("no"); ?></option>
                                                        <option value="1" <?php if($edit['is_company'] == 1){ echo "selected"; } ?>><?php echo $this->lang->line("yes"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("break_away"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <select class="form-control" id="break_away" name="break_away" required>
                                                        <option value="0" <?php if($edit['break_away'] == 0){ echo "selected"; } ?>><?php echo $this->lang->line("no"); ?></option>
                                                        <option value="1" <?php if($edit['break_away'] == 1){ echo "selected"; } ?>><?php echo $this->lang->line("yes"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Free Voucher</label><br>
                                                    <?php
                                                        $voucher_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE_VOUCHER, "*", array('package_id' => $edit['id'], 'product_id' => 0, 'active' => 1));
                                                        $is_select = isset($voucher_package_info['id']) ? true : false;
                                                        $package_quantity = isset($voucher_package_info['id']) ? $voucher_package_info['quantity'] : 0;
                                                        $package_price = isset($voucher_package_info['id']) ? $voucher_package_info['price'] : 0;
                                                    ?>
                                                    <input type="checkbox" name="product_id[]" value="0" <?php if($is_select){ echo "checked"; } ?>>&nbsp;Any Product&nbsp;&nbsp;<input type="text" name="attr[0][price]" placeholder="Price" value="<?php if($package_price != 0){ echo $package_price; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[0][quantity]" placeholder="Qty" value="<?php if($package_quantity != 0){ echo $package_quantity; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
                                                    <?php
                                                        $product = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $this->user_profile_info['company_id'], 'active' => 1, 'is_promotion' => 1));

                                                        foreach($product as $row_product){
                                                            $voucher_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE_VOUCHER, "*", array('package_id' => $edit['id'], 'product_id' => $row_product['id'], 'active' => 1));
                                                            $is_select = isset($voucher_package_info['id']) ? true : false;
                                                            $package_quantity = isset($voucher_package_info['id']) ? $voucher_package_info['quantity'] : 0;
                                                            $package_price = isset($voucher_package_info['id']) ? $voucher_package_info['price'] : 0;
                                                    ?>
                                                    <input type="checkbox" name="product_id[]" value="<?php echo $row_product['id']; ?>" <?php if($is_select){ echo "checked"; } ?>>&nbsp;<?php echo $row_product['name']; ?>&nbsp;&nbsp;<input type="text" name="attr[<?php echo $row_product['id']; ?>][price]" placeholder="Price" value="<?php if($package_price != 0){ echo $package_price; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[<?php echo $row_product['id']; ?>][quantity]" value="<?php if($package_quantity != 0){ echo $package_quantity; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
                                                    <?php
                                                        }
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button type="button" class="btn btn-success" id="back"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="package_id" name="package_id" value="<?php echo $id; ?>">
                            <?php
                                $company_id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $edit['company_id']));
                            ?>
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo $company_id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        var roles_id = "<?php echo $this->user_profile_info['group_id']; ?>";

        if(roles_id == 1){
            window.location.href = "<?= site_url(); ?>Package/view/" + $("#company_id").val();
        }else{
            window.location.href = "<?= site_url(); ?>Package";
        }
    });

    $('#package_form').submit(function(e) {
        e.preventDefault();

        var edit_package = new FormData(this);
        edit_package.set('access_token', $("#access_token").val());

        axios.post(address + 'Package/edit_package' , edit_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Package/edit/' + response.data.data);
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