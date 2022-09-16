<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="purchase_package_form">
                            <?php
                                $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                            ?>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit Purchase Package</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Package</label>
                                                    <select class="form-control" name="package_id">
                                                        <?php
                                                            foreach($package as $row_package){
                                                        ?>
                                                        <option value="<?php echo $row_package['id']; ?>" <?php if($row_package['id'] == $edit['package_id']){ echo "selected"; } ?>><?php echo $row_package['name']; ?></option>
                                                        <?php
                                                            }
                                                        ?>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $edit['company_id'], 'active' => 1));
                                            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

                                            if($company_type == "FIXED"){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" value="<?php echo $edit['amount']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }else{
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Point</label>
                                                    <input type="text" class="form-control" name="subtotal" value="<?php echo $edit['subtotal']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">PV</label>
                                                    <input type="text" class="form-control" name="pv" value="<?php echo $edit['pv']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Company ?</label>
                                                    <select class="form-control" name="is_company">
                                                        <option value="1" <?php if($edit['is_company'] == 1){ echo "selected"; } ?>>Yes</option>
                                                        <option value="0" <?php if($edit['is_company'] == 0){ echo "selected"; } ?>>No</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            if($company_type == "FIXED"){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Voucher Code</label>
                                                    <input type="text" class="form-control" name="voucher_code">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <br>
                                        <button type="button" class="btn btn-success" id="back"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="purchase_package_id" name="purchase_package_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Order/package";
    });

    $('#purchase_package_form').submit(function(e) {
        e.preventDefault();

        var edit_purchase_package = new FormData(this);
        edit_purchase_package.set('access_token', $("#access_token").val());
        edit_purchase_package.set('update_by', $("#insert_by").val());

        axios.post(address + 'Order/update_purchase_package' , edit_purchase_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('Update Success !', true, 'Order/package');
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