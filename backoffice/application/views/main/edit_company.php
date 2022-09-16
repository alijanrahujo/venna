<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="company_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo $this->lang->line("edit_company"); ?></h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("type"); ?></label>
                                                    <select class="form-control" id="type" name="type" required>
                                                        <option value="1" <?php if($edit['type'] == "FIXED"){ echo "selected"; } ?>><?php echo $this->lang->line("fixed"); ?></option>
                                                        <option value="2" <?php if($edit['type'] == "FLAT"){ echo "selected"; } ?>><?php echo $this->lang->line("dynamic"); ?></option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("name"); ?></label>
                                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("reg_no"); ?></label>
                                                    <input type="text" class="form-control" id="reg_no" name="reg_no" value="<?php echo $edit['reg_no']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("email_address"); ?></label>
                                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $edit['email']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("phone_no"); ?></label>
                                                    <input type="text" class="form-control" id="phone_no" name="phone_no" value="<?php echo $edit['phone_no']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("address"); ?></label>
                                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $edit['address']; ?>">
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
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("bank_name"); ?></label>
                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo $user['bank_name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("account_name"); ?></label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name" value="<?php echo $user['account_name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("account_no"); ?></label>
                                                    <input type="number" class="form-control" id="account_no" name="account_no" value="<?php echo $user['account_no']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("min"); ?> <?php echo $this->lang->line("withdraw"); ?></label>
                                                    <input type="text" class="form-control" id="min_withdraw" name="min_withdraw" value="<?php echo $edit['min_withdraw']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("break_away"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <input type="text" class="form-control" id="break_away_bonus" name="break_away_bonus" value="<?php echo $edit['break_away_bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("cross_over"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <input type="text" class="form-control" id="cross_over_bonus" name="cross_over_bonus" value="<?php echo $edit['cross_over_bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Any <?php echo $this->lang->line("cross_over"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <input type="text" class="form-control" id="any_cross_over_bonus" name="any_cross_over_bonus" value="<?php echo $edit['any_cross_over_bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("drb"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <input type="text" class="form-control" id="drb_bonus" name="drb_bonus" value="<?php echo $edit['drb_bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("drb"); ?> <?php echo $this->lang->line("limit"); ?></label>
                                                    <input type="number" class="form-control" id="drb_limit" name="drb_limit" value="<?php echo $edit['drb_limit']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("first"); ?> <?php echo $this->lang->line("smart_partner"); ?></label>
                                                    <input type="number" class="form-control" id="first_smart_partner" name="first_smart_partner" value="<?php echo $edit['first_smart_partner']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("smart_partner"); ?> <?php echo $this->lang->line("bonus"); ?></label>
                                                    <input type="text" class="form-control" id="smart_partner_bonus" name="smart_partner_bonus" value="<?php echo $edit['smart_partner_bonus']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("min"); ?> <?php echo $this->lang->line("monthly"); ?> <?php echo $this->lang->line("mds"); ?></label>
                                                    <input type="text" class="form-control" id="min_mdb_qty" name="min_mdb_qty" value="<?php echo $edit['min_mdb_qty']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("min"); ?> <?php echo $this->lang->line("quarterly"); ?> <?php echo $this->lang->line("mds"); ?></label>
                                                    <input type="text" class="form-control" id="min_quarterly_qty" name="min_quarterly_qty" value="<?php echo $edit['min_quarterly_qty']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("rb"); ?> <?php echo $this->lang->line("voucher"); ?> <?php echo $this->lang->line("percentage"); ?></label>
                                                    <input type="text" class="form-control" id="rb_voucher_qty" name="rb_voucher_qty" value="<?php echo $edit['rb_voucher_qty']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("rb"); ?> <?php echo $this->lang->line("voucher"); ?> <?php echo $this->lang->line("value"); ?></label>
                                                    <input type="text" class="form-control" id="rb_voucher_value" name="rb_voucher_value" value="<?php echo $edit['rb_voucher_value']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            if($edit['mms_level'] == 0){
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput"><?php echo $this->lang->line("mms"); ?> <?php echo $this->lang->line("level"); ?></label>
                                                    <input type="number" class="form-control" id="mms_level" name="mms_level" value="<?php echo $edit['mms_level']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Withdrawal Charge Type</label>
                                                    <select class="form-control" name="withdrawal_charge_type">
                                                        <option value="amount">Amount</option>
                                                        <option value="percentage">Percentage</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Withdrawal Charge Amount</label>
                                                    <input type="text" class="form-control" id="withdrawal_charge_amount" name="withdrawal_charge_amount" value="<?php echo $edit['withdrawal_charge_amount']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Is Infinity</label>
                                                    <select class="form-control" id="is_infinity_level" name="is_infinity_level">
                                                        <option value="0" <?php if($edit['is_infinity_level'] == 0){ echo "selected"; } ?>>No</option>
                                                        <option value="1" <?php if($edit['is_infinity_level'] == 1){ echo "selected"; } ?>>Yes</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">CB Point Rate</label>
                                                    <input type="text" class="form-control" id="cb_rate" name="cb_rate" value="<?php echo $edit['cb_rate']; ?>">
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
                            <input type="hidden" name="company_id" value="<?php echo $id; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Company";
    });

    $('#company_form').submit(function(e) {
        e.preventDefault();

        var update_company = new FormData(this);
        update_company.set('access_token', $("#access_token").val());

        axios.post(address + 'Company/update_company' , update_company, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Company');
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