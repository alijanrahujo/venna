<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="package_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("add_package"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Country</label>
                                                    <select class="form-control" name="country_id" required>
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
                                                    <label for="basicInput">Chinese <?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="name" name="name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">English <?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="english_name" name="english_name" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("unit"); ?></label>
                                                    <input type="text" class="form-control" id="unit" name="unit" required>
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
                                                    <label for="basicInput">Free <?php echo $this->lang->line("quantity"); ?></label>
                                                    <input type="number" class="form-control" id="free_quantity" name="free_quantity">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("unit_price"); ?></label>
                                                    <input type="text" class="form-control" id="unit_price" name="unit_price" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("grand_total"); ?></label>
                                                    <input type="text" class="form-control" id="grand_total" name="grand_total" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Box (optional)</label>
                                                    <input type="number" class="form-control" id="box" name="box">
                                                </fieldset>
                                            </div>
                                        </div>
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
                                                    <label for="basicInput"><?php echo $this->lang->line("break_away"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <select class="form-control" id="break_away" name="break_away" required>
                                                        <option value="0"><?php echo $this->lang->line("no"); ?></option>
                                                        <option value="1"><?php echo $this->lang->line("yes"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("free"); ?> Voucher</label><br>
                                                    <input type="checkbox" name="product_id[]" value="0">&nbsp;Any Product&nbsp;&nbsp;<input type="text" name="attr[0][price]" placeholder="Price" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[0][quantity]" placeholder="Qty" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
                                                    <?php
                                                        foreach($product as $row_product){
                                                    ?>
                                                    <input type="checkbox" name="product_id[]" value="<?php echo $row_product['id'] ?>">&nbsp;<?php echo $row_product['name'] ?>&nbsp;&nbsp;<input type="text" name="attr[<?php echo $row_product['id'] ?>][price]" placeholder="Price" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;">&nbsp;&nbsp;<input type="number" name="attr[<?php echo $row_product['id'] ?>][quantity]" placeholder="Qty" style="border-radius: 5px; border: 1px solid #e0e0e0; width: 80px;"><br>
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
                            <input type="hidden" id="company_id" name="company_id" value="<?php echo $id; ?>">
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
            window.location.href = "<?= site_url(); ?>Package/view/" + "<?php echo $this->uri->segment(3); ?>";
        }else{
            window.location.href = "<?= site_url(); ?>Package";
        }
    });

    $('#package_form').submit(function(e) {
        e.preventDefault();

        var insert_package = new FormData(this);
        insert_package.set('access_token', $("#access_token").val());
        insert_package.set('insert_by', $("#insert_by").val());

        axios.post(address + 'Package/insert_package' , insert_package, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("add_success"); ?>', true, 'Package/view/' + response.data.data);
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