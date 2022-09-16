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
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_big_present"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("code"); ?></label>
                                                    <input type="text" class="form-control" id="code" name="code" value="<?php echo $edit['code']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("quantity"); ?></label>
                                                    <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $edit['balance_quantity']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("price"); ?></label>
                                                    <input type="text" class="form-control" id="price" name="price" value="<?php echo $edit['price']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            $company_id = $edit['company_id'];
                                            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                                            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

                                            if($company_type == "FIXED"){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("total_stock"); ?></label>
                                                    <input type="number" class="form-control" id="total_stock" name="total_stock" value="<?php echo $edit['total_stock']; ?>">
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
                                                    <input type="text" class="form-control" id="total_point" name="total_point" value="<?php echo $edit['total_point']; ?>">
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
                                                        <option value="0" <?php if($edit['is_company'] == 0){ echo "selected"; } ?>><?php echo $this->lang->line("no"); ?></option>
                                                        <option value="1" <?php if($edit['is_company'] == 1){ echo "selected"; } ?>><?php echo $this->lang->line("yes"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("package"); ?></label><br>
                                                    <?php
                                                        $package = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('active' => 1, 'company_id' => $company_id, 'country_id' => $edit['country_id']));

                                                        foreach($package as $row_package){
                                                            $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_PACKAGE, "*", array('package_id' => $row_package['id'], 'big_present_id' => $edit['id'], 'active' => 1));
                                                            $is_select = isset($big_present_package_info['id']) ? true : false;
                                                            $package_quantity = isset($big_present_package_info['id']) ? $big_present_package_info['quantity'] : 0;
                                                            $package_price = isset($big_present_package_info['id']) ? $big_present_package_info['price'] : 0;
                                                    ?>
                                                    <input type="checkbox" name="package_id[]" value="<?php echo $row_package['id']; ?>" <?php if($is_select){ echo "checked"; } ?>>&nbsp;<?php echo $row_package['name']; ?>&nbsp;&nbsp;<input type="text" name="attr[<?php echo $row_package['id']; ?>][price]" placeholder="Price" value="<?php if($package_price != 0){ echo $package_price; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[<?php echo $row_package['id']; ?>][quantity]" value="<?php if($package_quantity != 0){ echo $package_quantity; } ?>" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
                                                    <?php
                                                        }
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <input type="hidden" name="voucher_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Voucher";
    });

    $('#voucher_form').submit(function(e) {
        e.preventDefault();

        var update_voucher = new FormData(this);
        update_voucher.set('access_token', $("#access_token").val());

        axios.post(address + 'Voucher/update_voucher' , update_voucher, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Voucher');
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