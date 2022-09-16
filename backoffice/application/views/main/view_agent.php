<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.8/themes/default/style.min.css" />
<style>
    .top-banner-bar {
        width: 100%;
        text-align: center;
        background-color: black;
        color: white;
        font-size: 14px;
        padding: 8px 20px;
        font-weight: 600;
        /* display: flex;
        flex-direction: row; */
        -webkit-box-pack: justify;
        justify-content: space-between
    }
    .jstree-default .jstree-closed>.jstree-ocl {
        background: url("<?= site_url(); ?>img/add.jpeg") 0px 0px no-repeat ! important;
    }

    .jstree-default .jstree-open>.jstree-ocl {
        background: url("<?= site_url(); ?>img/minus.png") 0px 0px no-repeat ! important;
    }
</style>

<div class="main-panel">
    <div class="main-content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="justified-tabs">
                <div class="row match-height">
                    <div class="col-xl-12 col-lg-12">
                        <div class="card">
                            <?php
                                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $edit['company_id'], 'active' => 1));
                                $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";

                                if($edit['profile_image'] == "" || $edit['profile_image'] == NULL){
                                    if($company_subdomain == "sangrila"){
                                        $profile_image = DISPLAY_PATH . "img/branding-logo.jpg";
                                    }else{
                                        $profile_image = DISPLAY_PATH . "img/default-profile.png";
                                    }
                                }else{
                                    $profile_image = DISPLAY_PATH . "img/profile/" . $edit['profile_image'];
                                }

                                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname", array('id' => $edit['referral_id'], 'active' => 1));
                                $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
                                $referral_id = isset($referral_info['id']) ? $referral_info['id'] : "";
                                $encrypt_referral_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($referral_id));

                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $edit['package_id'], 'active' => 1));
                                $package_name = isset($package_info['id']) ? $package_info['name'] : "";

                                $agent_voucher_id = isset($edit['voucher_id']) ? $edit['voucher_id'] : 0;
                                $agent_is_voucher = isset($edit['is_voucher']) ? $edit['is_voucher'] : 0;

                                if($agent_is_voucher == 1){
                                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, type", array('id' => $agent_voucher_id));
                                    if(isset($voucher_info['id']) && $voucher_info['id'] > 0){
                                        if($voucher_info['type'] == "BIG_PRESENT"){
                                            $promotion_tag = "(大礼包)";
                                        }else{
                                            $promotion_tag = "(半门槛)";
                                        }
                                    }else{
                                        $promotion_tag = "";
                                    }
                                }else{
                                    $promotion_tag = "";
                                }
                            ?>
                            <div class="top-banner-bar">
                                <div class="row">
                                    <div class="col-md-2 col-6">Current rank: <?php echo $package_name; ?></div>
                                    <div class="col-md-2 col-6">Downlines: <?php echo $total_organization; ?></div>
                                    <?php
                                        if($this->user_profile_info['company_id'] == 12){
                                    ?>
                                    <div class="col-md-2 col-6">Point Balance: <?php echo $point_balance; ?></div>
                                    <?php
                                        }else{
                                    ?>
                                    <div class="col-md-2 col-6">Stock Balance: <?php echo $stock_balance; ?></div>
                                    <?php
                                        }
                                    ?>
                                    <div class="col-md-2 col-6">Cash Balance(PV): <?php echo number_format($total_wallet, 2); ?></div>
                                    <div class="col-md-2 col-6">Date: <?php echo date("d-m-Y H:ia", strtotime($edit['insert_time'])); ?></div>
                                    <div class="col-md-2 col-6">Package: <?php echo $package_name . " " . $promotion_tag; ?></div>
                                </div>
                            </div>
                            <div class="card-header">
                                <!-- <h4 class="card-title">
                                    <p><?php echo $edit['username']; ?> | 
                                    <?php
                                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $edit['package_id'], 'active' => 1));
                                        $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                                        echo $package_name;
                                    ?>
                                    </p>
                                </h4> -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div style="width: 100%; display: flex;">
                                            <div style="width: 25%">
                                                <img src="<?php echo $profile_image; ?>" height="90" width="90" style="object-fit: contain; border-radius: 32px;">
                                            </div>
                                            <div style="width: 75%">
                                                <h6><b><?php echo $edit['fullname']; ?></b></h6>
                                                <p>
                                                    <span>Username: <?php echo $edit['username']; ?></span><br>
                                                    <span>IC No: <?php echo $edit['ic']; ?></span><br>
                                                    <?php
                                                        if($referral_fullname == "sangrila001"){
                                                    ?>
                                                    <span>Upline: <a style="text-decoration: underline;"><?php echo $referral_fullname; ?></a></span>
                                                    <?php
                                                        }else{
                                                    ?>
                                                    <span>Upline: <a style="text-decoration: underline;" href="<?php echo site_url() . "Member/view/" . $encrypt_referral_id; ?>"><?php echo $referral_fullname; ?></a></span>
                                                    <?php
                                                        }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6><b>Contact Info</b></h6>
                                        <p>
                                            <span>Phone: <?php echo $edit['phone_no']; ?></span><br>
                                            <span>E-mail: <?php echo $edit['email']; ?></span><br>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <h6><b>Bank Details</b></h6>
                                        <p>
                                            <span>Bank Name: <?php echo $edit['bank_name']; ?></span><br>
                                            <span>Bank Account No: <?php echo $edit['account_no']; ?></span><br>
                                            <span>Bank Account Holder: <?php echo $edit['account_name']; ?></span><br>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="agent-details-tab" data-toggle="tab" href="#agent-details" aria-controls="agent-details" aria-expanded="true"><?php echo $this->lang->line("agent_details"); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="address-tab" data-toggle="tab" href="#address" aria-controls="address" aria-expanded="false"><?php echo $this->lang->line("address_book"); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" aria-controls="bank" aria-expanded="false"><?php echo $this->lang->line("bank_details"); ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="network-tab" data-toggle="tab" href="#network" aria-controls="network">Network</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="restock-history-tab" data-toggle="tab" href="#restock-history" aria-controls="restock-history">Restock History</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="stock-record-tab" data-toggle="tab" href="#stock-record" aria-controls="stock-record">Stock Record</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="shipment-record-tab" data-toggle="tab" href="#shipment-record" aria-controls="shipment-record">Shipment History</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="retail-order-tab" data-toggle="tab" href="#retail-order" aria-controls="retail-order">Retail Order</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="cash-wallet-tab" data-toggle="tab" href="#cash-wallet" aria-controls="cash-wallet">Cash Wallet</a>
                                        </li>
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" id="agent-report-tab" data-toggle="tab" href="#agent-report" aria-controls="agent-report">Sales Report</a>
                                        </li> -->
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active show" id="agent-details" aria-labelledby="agent-details-tab" aria-expanded="true">
                                            <section id="basic-input">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <form method="POST" id="profile_form">
                                                            <div class="card">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <input type="hidden" name="company_id" value="<?php echo $edit['company_id']; ?>">
                                                                        <?php
                                                                            if($edit['company_id'] == 2){
                                                                        ?>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">Is Paid to Company</label>
                                                                                    <select class="form-control" id="is_company" name="is_company">
                                                                                        <option value="0" <?php if($edit['is_company'] == 0){ echo "selected"; } ?>>No</option>
                                                                                        <option value="1" <?php if($edit['is_company'] == 1){ echo "selected"; } ?>>Yes</option>
                                                                                    </select>
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                            }
                                                                        ?>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("email_address"); ?></label>
                                                                                    <input type="text" class="form-control" name="email" value="<?php echo $edit['email']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("username"); ?></label>
                                                                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $edit['username']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("ic_no"); ?></label>
                                                                                    <input type="number" class="form-control" id="ic" name="ic" value="<?php echo $edit['ic']; ?>" onclick="remove_empty_space(this)">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("phone_no"); ?></label>
                                                                                    <input type="number" class="form-control" id="phone_no" name="phone_no" value="<?php echo $edit['phone_no']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("select_upline"); ?></label>
                                                                                    <select class="select2 form-control" id="referral_id" name="referral_id">
                                                                                        <option value="0">Select Referral</option>
                                                                                        <?php
                                                                                            foreach($member as $row_member){
                                                                                        ?>
                                                                                        <option value="<?php echo $row_member['id']; ?>" <?php if($row_member['id'] == $edit['referral_id']){ echo "selected"; } ?>><?php echo $row_member['username']; ?></option>
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
                                                                                    <label for="basicInput"><?php echo $this->lang->line("password"); ?></label>
                                                                                    <input type="password" class="form-control" id="password" name="password">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("cfm_password"); ?></label>
                                                                                    <input type="password" class="form-control" id="cfm_password" name="cfm_password">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">Join Date</label>
                                                                                    <input type="date" class="form-control" id="insert_time" name="insert_time" value="<?php echo date("Y-m-d", strtotime($edit['insert_time'])); ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                        <button class="btn btn-success" onclick="back_member()" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="tab-pane" id="address" role="tabpanel" aria-labelledby="address-tab" aria-expanded="false">
                                            <section id="basic-input">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <form method="POST" id="address_form">
                                                            <div class="card">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">Address</label>
                                                                                    <input type="text" class="form-control" id="address_line1" name="address_line1" value="<?php echo $edit['address_line1']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">City</label>
                                                                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $edit['city']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">State</label>
                                                                                    <input type="text" class="form-control" id="state" name="state" value="<?php echo $edit['state']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput">Postcode</label>
                                                                                    <input type="number" class="form-control" id="postcode" name="postcode" value="<?php echo $edit['postcode']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                        <button class="btn btn-success" onclick="back_member()" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="tab-pane" id="bank" role="tabpanel" aria-labelledby="bank-tab" aria-expanded="false">
                                            <section id="basic-input">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <form method="POST" id="bank_form">
                                                            <div class="card">
                                                                <div class="card-content">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("bank_name"); ?></label>
                                                                                    <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo $edit['bank_name']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("account_name"); ?></label>
                                                                                    <input type="text" class="form-control" id="account_name" name="account_name" value="<?php echo $edit['account_name']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <fieldset class="form-group">
                                                                                    <label for="basicInput"><?php echo $this->lang->line("account_no"); ?></label>
                                                                                    <input type="number" class="form-control" id="account_no" name="account_no" value="<?php echo $edit['account_no']; ?>">
                                                                                </fieldset>
                                                                            </div>
                                                                        </div>
                                                                        <br>
                                                                        <button class="btn btn-success" onclick="back_member()" type="button"><?php echo $this->lang->line("back"); ?></button>&nbsp;&nbsp;<button class="btn btn-primary" type="submit"><?php echo $this->lang->line("submit"); ?></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="tab-pane" id="network" role="tabpanel" aria-labelledby="network-tab" aria-expanded="false">
                                            <br>
                                            <div id="SimpleJSTree"></div>
                                        </div>
                                        <div class="tab-pane" id="restock-history" role="tabpanel" aria-labelledby="restock-history-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table dt-responsive" id="package_order_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Order ID</th>
                                                            <th><?php echo $this->lang->line("package_detail"); ?></th>
                                                            <th>Quantity</th>
                                                            <th>Status</th>
                                                            <th><?php echo $this->lang->line("datetime"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="stock-record" role="tabpanel" aria-labelledby="stock-record-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table dt-responsive" id="stock_record_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Order ID</th>
                                                            <th>Description</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th><?php echo $this->lang->line("datetime"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="shipment-record" role="tabpanel" aria-labelledby="shipment-record-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table dt-responsive" id="shipment_record_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Order ID</th>
                                                            <th>Product</th>
                                                            <th>Subtotal</th>
                                                            <th>Tracking Detail</th>
                                                            <th>Payment</th>
                                                            <th><?php echo $this->lang->line("datetime"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="retail-order" role="tabpanel" aria-labelledby="retail-order-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table dt-responsive" id="retail_record_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Order ID</th>
                                                            <th>Product</th>
                                                            <th>Subtotal</th>
                                                            <th>Tracking Detail</th>
                                                            <th>Payment</th>
                                                            <th><?php echo $this->lang->line("datetime"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="cash-wallet" role="tabpanel" aria-labelledby="cash-wallet-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" id="wallet_record_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th><?php echo $this->lang->line("description"); ?></th>
                                                            <th>Transaction</th>
                                                            <th><?php echo $this->lang->line("datetime"); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <!--<div class="tab-pane" id="agent-report" role="tabpanel" aria-labelledby="agent-report-tab" aria-expanded="false">
                                            <br>
                                            <div class="table-responsive">
                                                <div class="row">
                                                    <div class="col-md-3"></div>
                                                    <div class="col-md-1">
                                                        <label for="basicInput" style="margin-top: 10px;">Year</label>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <fieldset class="form-group">
                                                            <select class="form-control" id="year" name="year">
                                                                <option value="2021">2021</option>
                                                                <option value="2022">2022</option>
                                                                <option value="2023">2023</option>
                                                                <option value="2024">2024</option>
                                                                <option value="2025">2025</option>
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button class="btn btn-primary" type="button" onclick="search_member_report()">Submit</button>
                                                    </div>
                                                    <div class="col-md-3"></div>
                                                </div>
                                                <table class="table table-striped table-bordered" id="agent_record_dt" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Month</th>
                                                            <th><?php echo $this->lang->line("action"); ?></th>
                                                        </tr>
                                                        <tr>
                                                            <td>1</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(1);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(2);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(3);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>4</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(4);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>5</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(5);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>6</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(6);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>7</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(7);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>8</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(8);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>9</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(9);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>10</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(10);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>11</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(11);">Generate</button></td>
                                                        </tr>
                                                        <tr>
                                                            <td>12</td>
                                                            <td><button type="button" class="btn btn-success" onclick="generate_report(12);">Generate</button></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>-->
                                        <?php
                                            $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
                                        ?>
                                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $id; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel1"><?php echo $this->lang->line("payment_receipt"); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="ft-x font-medium-2 text-bold-700"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <img id="payment_receipt" style="width: 100%;">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        drawtable_package();
        drawtable_stock();
        drawtable_shipment();
        drawtable_retail();
        drawtable_wallet();
        get_tree_view();
	});

    function drawtable_package(){
		$('#package_order_dt').dataTable().fnClearTable();
    	$('#package_order_dt').dataTable().fnDestroy();
        $('#package_order_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_package_order",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $id; ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    is_agent: 1
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "order_id", "orderable": false},
                {"data": "package_detail", "orderable": false},
                {"data": "amount", "orderable": false},
                {"data": "status", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
    }

    function drawtable_stock(){
		$('#stock_record_dt').dataTable().fnClearTable();
    	$('#stock_record_dt').dataTable().fnDestroy();
        $('#stock_record_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_stock_record",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $id; ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>'
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "order_id", "orderable": false},
                {"data": "description", "orderable": false},
                {"data": "amount", "orderable": false},
                {"data": "order_status", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
    }

    function drawtable_shipment(){
		$('#shipment_record_dt').dataTable().fnClearTable();
    	$('#shipment_record_dt').dataTable().fnDestroy();
        $('#shipment_record_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_shipment",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $id; ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    is_agent: 1
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "shipping_order_id", "orderable": false},
                {"data": "product_item", "orderable": false},
                {"data": "total_price", "orderable": false},
                {"data": "tracking_detail", "orderable": false},
                {"data": "shipping_payment", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
    }

    function drawtable_retail(){
		$('#retail_record_dt').dataTable().fnClearTable();
    	$('#retail_record_dt').dataTable().fnDestroy();
        $('#retail_record_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Order/get_shipment",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $id; ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>',
                    is_referral: 1
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "shipping_order_id", "orderable": false},
                {"data": "product_item", "orderable": false},
                {"data": "total_price", "orderable": false},
                {"data": "tracking_detail", "orderable": false},
                {"data": "shipping_payment", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
    }

    function drawtable_wallet(){
		$('#wallet_record_dt').dataTable().fnClearTable();
    	$('#wallet_record_dt').dataTable().fnDestroy();
        $('#wallet_record_dt').DataTable({
            'order': [],
            'searching': false,
            'serverSide': true,
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem('offersDataTables', JSON.stringify(oData));
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse(localStorage.getItem('offersDataTables'));
            },
            'ajax': {
                'url': address + "Member/get_cash_wallet_record",
                "type": "POST",
                "data": {
                    access_token: $("#access_token").val(),
                    language: '<?php echo $this->session->userdata("site_lang"); ?>',
                    user_id: '<?php echo $id; ?>',
                    user_type: '<?php echo $this->session->userdata("user_type"); ?>',
                    group_id: '<?php echo $this->user_profile_info['group_id']; ?>',
                    company_id: '<?php echo $this->user_profile_info['company_id']; ?>'
                },
                dataFilter: function (data) {
                    var result = jQuery.parseJSON(data);
                    if (result.status == "Failed") {
                        Swal.fire({
                            title: '<?php echo $this->lang->line("warning"); ?>',
                            text: result,
                            confirmButtonText: '<?php echo $this->lang->line("ok"); ?>'
                        })
                        return JSON.stringify(result.data);
                    }else{
                        return JSON.stringify(result.data);
                    }
                }
            },
            "language": {
                "lengthMenu": "<?php echo $this->lang->line("showing"); ?>" + " _MENU_ " + "<?php echo $this->lang->line("entries"); ?>",
                "info": "<?php echo $this->lang->line("showing"); ?>" + " _PAGE_ / _PAGES_ " + "<?php echo $this->lang->line("pages"); ?>",
                "infoEmpty": "<?php echo $this->lang->line("record_not_found"); ?>",
                "zeroRecords": "<?php echo $this->lang->line("record_not_found"); ?>",
                "infoFiltered": "(" + "<?php echo $this->lang->line("filter_form"); ?>" + " _MAX_ " + "<?php echo $this->lang->line("total_records"); ?>" + ")",
                "search": "<?php echo $this->lang->line("filter_search"); ?>" + ":",
                "paginate": {
                "previous": "<?php echo $this->lang->line("previous"); ?>",
                "next": "<?php echo $this->lang->line("next"); ?>",
                }
            },
            "columns": [
                {"data": "wallet_type", "orderable": false},
                {"data": "description", "orderable": false},
                {"data": "transaction", "orderable": false},
                {"data": "insert_time", "orderable": false}
            ]
        });
    }

    function get_downline_tree(id){
        var check_upline_username = new FormData();
        check_upline_username.set('access_token', $("#access_token").val());
        check_upline_username.set('upline_id', id);

        axios.post(address + 'Member/check_upline_username' , check_upline_username, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                var upline_id = response.data.data;
                window.location.href = "<?= site_url(); ?>Member/view/" + upline_id;
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function get_tree_view(){
        var get_tree_view = new FormData();
        get_tree_view.set('access_token', $("#access_token").val());
        get_tree_view.set('insert_by', $("#insert_by").val());
        get_tree_view.set('id', '<?php echo $id; ?>');

        axios.post(address + 'Member/get_tree_view' , get_tree_view, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $('#SimpleJSTree').jstree({
                    'core': {
                        // 'data': jsondata
                        'data': response.data.data.chart_data
                    },
                    types: {
                        "root": {
                            "icon" : "fa fa-user"
                        },
                        "default" : {
                            "icon" : "fa fa-user"
                        }
                    },
                    plugins: ["types"]
                }).on('open_node.jstree', function (e, data) { data.instance.set_icon(data.node, "fa fa-user"); 
                }).on('close_node.jstree', function (e, data) { data.instance.set_icon(data.node, "fa fa-user"); 
                });

                $('#SimpleJSTree').on("changed.jstree", function (e, data) {
                    var i, j;
                    for (i = 0, j = data.selected.length; i < j; i++) {
                        var nodeId = data.selected[i];
                        get_downline_tree(nodeId);
                    }
                })
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function back_member(){
        window.location.href = "<?= site_url(); ?>Member";
    }

    function remove_empty_space(data){
        var old_string = data.value;
        var new_string = old_string.replace(/\s+/g, '');
        $("#ic").val(new_string);
    }

    $('#profile_form').submit(function(e) {
        e.preventDefault();

        var update_member = new FormData(this);
        update_member.set('access_token', $("#access_token").val());
        update_member.set('insert_by', $("#insert_by").val());
        update_member.set('user_id', $("#user_id").val());
        update_member.set('step', 1);

        axios.post(address + 'Member/update_member' , update_member, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Member/view/' + "<?php echo $this->uri->segment(3); ?>");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    $('#address_form').submit(function(e) {
        e.preventDefault();

        var update_address = new FormData(this);
        update_address.set('access_token', $("#access_token").val());
        update_address.set('insert_by', $("#insert_by").val());
        update_address.set('user_id', $("#user_id").val());
        update_address.set('step', 2);

        axios.post(address + 'Member/update_member' , update_address, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Member/view/' + "<?php echo $this->uri->segment(3); ?>");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    $('#bank_form').submit(function(e) {
        e.preventDefault();

        var update_bank = new FormData(this);
        update_bank.set('access_token', $("#access_token").val());
        update_bank.set('insert_by', $("#insert_by").val());
        update_bank.set('user_id', $("#user_id").val());
        update_bank.set('step', 3);

        axios.post(address + 'Member/update_member' , update_bank, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                success_response('<?php echo $this->lang->line("update_success"); ?>', true, 'Member/view/' + "<?php echo $this->uri->segment(3); ?>");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    });

    function show_order_receipt(id){
        var view_receipt = new FormData();
        view_receipt.set('access_token', $("#access_token").val());
        view_receipt.set('id', id);

        axios.post(address + 'Order/view_order_receipt' , view_receipt, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                $("#payment_receipt").attr("src", response.data.data.payment_receipt);
                $("#paymentModal").modal("show");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }

    function generate_report(month){
        var select_year = $("#year").val();
        var user_id = $("#user_id").val();

        var generate_report = new FormData();
        generate_report.set('access_token', $("#access_token").val());
        generate_report.set('month', month);
        generate_report.set('year', select_year);
        generate_report.set('user_id', $("#user_id").val());

        axios.post(address + 'Member/generate_report' , generate_report, apiHeader)
        .then(function (response) {
            if(response.data.status == "Success"){
                window.open("<?= DISPLAY_PATH; ?>" + "img/report/summary" + month + "_" + select_year + "_" + user_id + ".pdf", '_blank');
                // success_response("Generate Successfully !");
            }else{
                warning_response(response.data.message);
            }
        })
        .catch(function (data) {
            console.log(data);
            error_response();
        });
    }
</script>