<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="basic-input">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="member_form">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Personal Profile</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
										<div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Username</label>
                                                    <input type="text" class="form-control" id="first_name" readonly value="<?php echo $edit['username']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Package</label>
													<?php
														$package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $edit['rank']));
													?>
                                                    <input type="text" class="form-control" id="last_name" readonly value="<?php echo $package_info['name']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Full Name</label>
                                                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $edit['fullname']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Gender</label>
                                                    <select class="form-control" id="gender" name="gender">
                                                        <option value="1" <?php if($edit['gender'] == 1){ echo "selected"; }; ?>>Male</option>
                                                        <option value="2" <?php if($edit['gender'] == 2){ echo "selected"; }; ?>>Female</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">IC</label>
                                                    <input type="number" class="form-control" id="ic" name="ic" value="<?php echo $edit['ic']; ?>" required>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Address Line 1</label>
                                                    <input type="text" class="form-control" id="address_line1" name="address_line1" value="<?php echo $edit['address_line1']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Address Line 2</label>
                                                    <input type="text" class="form-control" id="address_line2" name="address_line2" value="<?php echo $edit['address_line2']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Postcode</label>
                                                    <input type="number" class="form-control" id="postcode" name="postcode" value="<?php echo $edit['postcode']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">City</label>
                                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $edit['city']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">State</label>
                                                    <input type="text" class="form-control" id="state" name="state" value="<?php echo $edit['state']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Country</label>
                                                    <input type="text" class="form-control" id="country" name="country" value="Malaysia" readonly>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Email Address</label>
                                                    <input type="email" class="form-control" id="email_address" name="email_address" required value="<?php echo $edit['email']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Phone No</label>
                                                    <input type="phone" class="form-control" id="phone_no" name="phone_no" required value="<?php echo $edit['phone_no']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="hr-left-20 hr-right-20">
                                <div class="card-header">
                                    <h4 class="card-title">Bank Details</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Bank Name</label>
                                                    <select class="form-control" name="bank_name">
                                                        <option value="AmBank" <?php if($edit['bank_name'] == "AmBank"){ echo "selected"; } ?>>AmBank</option>
                                                        <option value="Alliance Bank" <?php if($edit['bank_name'] == "Alliance Bank"){ echo "selected"; } ?>>Alliance Bank</option>
                                                        <option value="Affin Bank" <?php if($edit['bank_name'] == "Affin Bank"){ echo "selected"; } ?>>Affin Bank</option>
                                                        <option value="Agrobank" <?php if($edit['bank_name'] == "Agrobank"){ echo "selected"; } ?>>Agrobank</option>
                                                        <option value="Bank Rakyat" <?php if($edit['bank_name'] == "Bank Rakyat"){ echo "selected"; } ?>>Bank Rakyat</option>
                                                        <option value="CIMB Bank" <?php if($edit['bank_name'] == "CIMB Bank"){ echo "selected"; } ?>>CIMB Bank</option>
                                                        <option value="Citibank" <?php if($edit['bank_name'] == "Citibank"){ echo "selected"; } ?>>Citibank</option>
                                                        <option value="DBS Bank" <?php if($edit['bank_name'] == "DBS Bank"){ echo "selected"; } ?>>DBS Bank</option>
                                                        <option value="Hong Leong Bank" <?php if($edit['bank_name'] == "Hong Leong Bank"){ echo "selected"; } ?>>Hong Leong Bank</option>
                                                        <option value="HSBC Bank" <?php if($edit['bank_name'] == "HSBC Bank"){ echo "selected"; } ?>>HSBC Bank</option>
                                                        <option value="Maybank" <?php if($edit['bank_name'] == "Maybank"){ echo "selected"; } ?>>Maybank</option>
                                                        <option value="OCBC Bank" <?php if($edit['bank_name'] == "OCBC Bank"){ echo "selected"; } ?>>OCBC Bank</option>
                                                        <option value="Public Bank" <?php if($edit['bank_name'] == "Public Bank"){ echo "selected"; } ?>>Public Bank</option>
                                                        <option value="POSB Bank" <?php if($edit['bank_name'] == "POSB Bank"){ echo "selected"; } ?>>POSB Bank</option>
                                                        <option value="RHB Bank" <?php if($edit['bank_name'] == "RHB Bank"){ echo "selected"; } ?>>RHB Bank</option>
                                                        <option value="UOB Bank" <?php if($edit['bank_name'] == "UOB Bank"){ echo "selected"; } ?>>UOB Bank</option>                    
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Account Name</label>
                                                    <input type="text" class="form-control" id="account_name" name="account_name" value="<?php echo $edit['account_name']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Account No</label>
                                                    <input type="number" class="form-control" id="account_no" name="account_no" value="<?php echo $edit['account_no']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="hr-left-20 hr-right-20">
                                <div class="card-header">
                                    <h4 class="card-title">Beneficiary Details</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Full Name</label>
                                                    <input type="text" class="form-control" id="b_name" name="b_name" value="<?php echo $edit['b_name']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">IC</label>
                                                    <input type="text" class="form-control" id="b_ic" name="b_ic" value="<?php echo $edit['b_ic']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Phone No</label>
                                                    <input type="phone" class="form-control" id="b_phone_no" name="b_phone_no" value="<?php echo $edit['b_phone_no']; ?>">
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset class="form-group">
                                                    <label for="basicInput">Relationship</label>
                                                    <input type="text" class="form-control" id="b_relationship" name="b_relationship" value="<?php echo $edit['b_relationship']; ?>">
                                                </fieldset>
                                            </div>
                                        </div>
                                        <br>
                                        <button class="btn btn-success" id="back" type="button">Back</button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit">Submit</button>&nbsp;&nbsp;<button class="btn btn-danger" type="button" onclick="download_invoice()">Download Invoice</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo $edit['id']; ?>">
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $("#back").click(function(){
        window.location.href = "<?= site_url(); ?>Member";
    });

    function download_invoice(){
        var user_id = $("#user_id").val();

        window.open('<?= site_url(); ?>Member/generate/' + user_id, '_blank');
    }

    $('#member_form').submit(function(e) {
        e.preventDefault();

        var update_member = new FormData(this);
        update_member.set('access_token', $("#access_token").val());
        update_member.set('user_id', $("#user_id").val());
        update_member.set('update_by', $("#insert_by").val());

        axios.post(address + 'Member/update_member' , update_member, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Profile');
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