<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'vendor/autoload.php';

class Order extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/order_list");
    }

    public function package(){
        $this->load(ADMIN_URL . "/package_order_list");
    }

    public function shipment(){
        $this->load(ADMIN_URL . "/shipment_list");
    }

    public function get_package_order(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : 0;
        $status = isset($this->request_data['status']) ? $this->request_data['status'] : "";

        $result = array();
        $result['draw'] = $draw;

        if($user_type == "ADMIN" && $group_id == 1){
            $where_query = array('active' => 1);
            if($status != ""){
                $where_query['status'] = $status;
            }
        }else if($user_type == "ADMIN" && $group_id != 1){
            $where_query = array('active' => 1, 'company_id' => $company_id);
            if($status != ""){
                $where_query['status'] = $status;
            }
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_PURCHASE_PACKAGE, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_PURCHASE_PACKAGE, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }else{
            foreach ($order as $row) {
                if ($row['column'] == 0) {
                    $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " total_quantity ".$row['dir'] : $order_query.", total_quantity ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " payment_status ".$row['dir'] : $order_query.", payment_status ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " status ".$row['dir'] : $order_query.", status ".$row['dir'];
                }else if ($row['column'] == 5) {
                    $order_query = $order_query == "" ? " order_status ".$row['dir'] : $order_query.", order_status ".$row['dir'];
                }else if ($row['column'] == 6) {
                    $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $package_order_list = $this->Api_Model->get_datatables_list(TBL_PURCHASE_PACKAGE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query);
        $counting = 0;

        foreach ($package_order_list as $row) {
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, is_voucher, voucher_id, fullname, username, email, phone_no, referral_id, company_id", array('id' => $row['user_id'], 'active' => 1));
            $is_voucher = $user_info['is_voucher'];
            $package_id = $user_info['package_id'];
            $fullname = $user_info['fullname'];
            $username = $user_info['username'];
            $is_restock = $row['is_restock'];
            $restock_quantity = $row['quantity'];
            $restock_point = $row['point'];
            $restock_subtotal = $row['subtotal'];
            $restock_package = $row['package_id'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($is_restock == 1){
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $restock_package, 'active' => 1));
                $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                $package_quantity = $restock_quantity;
                $package_total = $restock_subtotal;
                if($company_type == "FIXED"){
                    $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "";
                    $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";
                }else{
                    $package_price = $restock_point;
                    $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";
                }
            }else{
                if($is_voucher == 1){
                    $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $user_info['voucher_id']));
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $big_present_info['package_id'], 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                    $package_quantity = isset($big_present_info['id']) ? $big_present_info['total_stock'] : "";
                    if($company_type == "FLAT"){
                        $package_price = isset($big_present_info['id']) ? $big_present_info['price'] : "";
                    }else{
                        $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "";
                    }
                    $package_total = isset($big_present_info['id']) ? $big_present_info['price'] : "";
                    $package_unit = isset($big_present_info['id']) ? $big_present_info['unit'] : "";
                }else{
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $user_info['package_id'], 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                    $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : "";
                    $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "";
                    $package_total = isset($package_info['id']) ? $package_info['grand_total'] : "";
                    $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";
                }
            }

            // get referral info
            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname", array('id' => $user_info['referral_id'], 'active' => 1));
            if(isset($referral_info['id']) && $referral_info['id'] > 0){
                $referral_username = $referral_info['username'];
                $referral_fullname = $referral_info['fullname'];
            }else{
                $referral_username = "";
                $referral_fullname = "";
            }

            $row['referral_name'] = $fullname . " (" . $username . ")";
            $row['agent_detail'] = $fullname . " (" . $username . ")" . "<br>" . $user_info['email'] . "<br>" . $user_info['phone_no'];
            $row['package_detail'] = $package_name . "<br>" . $package_quantity . " x " . $package_price . " = " . $package_total;
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));

            $payment_status = $row['payment_status'];
            if($payment_status == "UNPAID"){
                $row['payment_status'] = '<span class="badge bg-light-danger mb-1 mr-2">' . $payment_status . '</span>';
            }else{
                $row['payment_status'] = '<span class="badge bg-light-success mb-1 mr-2">' . $payment_status . '</span>&nbsp;<a href="#" onclick="show_payment_receipt(' . $row['id'] . '); return false;"><u>View</u></a>';
            }

            $btn = "";

            $order_status = $row['status'];
            if($order_status == "PENDING"){
                $row['order_status'] = '<span class="badge bg-light-warning mb-1 mr-2">' . $order_status . '</span>';
                $btn .= "<a href='#' class='btn-sm btn-success' style='border:none;' onclick='approve_order(" . $row['id'] . ")'>" . "Approve" . "</a>";
            }else if($order_status == "APPROVE"){
                $row['order_status'] = '<span class="badge bg-light-success mb-1 mr-2">' . $order_status . '</span>';
            }else if($order_status == "REJECT"){
                $row['order_status'] = '<span class="badge bg-light-danger mb-1 mr-2">' . $order_status . '</span>';
            }
            
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function view_payment_receipt(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $package_order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $id, 'active' => 1));
        $payment_receipt = $package_order_info['payment_receipt'];
        $package_order_info['payment_receipt'] = DISPLAY_PATH . "img/package_receipt/" . $payment_receipt;

        $json['response_data'] = $package_order_info;
        $this->load->view("output/success_response", $json);
    }

    public function view_order_receipt(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $id, 'active' => 1));
        $payment_receipt = $order_info['payment_receipt'];
        $order_info['payment_receipt'] = DISPLAY_PATH . "img/order_receipt/" . $payment_receipt;

        $json['response_data'] = $order_info;
        $this->load->view("output/success_response", $json);
    }

    public function check_package_order(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $package_order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $id, 'active' => 1));
        if(isset($package_order_info['id']) && $package_order_info['id'] > 0){
            $payment_status = $package_order_info['payment_status'];

            $data = array(
                'payment_status' => $payment_status
            );

            $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Package Order";
            $this->load->view("output/error_response", $data);
        }
    }

    public function check_product_order(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $payment_status = $order_info['payment_status'];

            $data = array(
                'payment_status' => $payment_status
            );

            $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Order";
        }
    }

    public function approve_package_order(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $package_order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $id, 'active' => 1));
        if(isset($package_order_info['id']) && $package_order_info['id'] > 0){
            $purchase_id = $package_order_info['id'];
            $referral_id = $package_order_info['referral_id'];
            $user_id = $package_order_info['user_id'];
            $company_id = $package_order_info['company_id'];
            $package_id = $package_order_info['package_id'];
            $is_voucher = $package_order_info['is_voucher'];
            $is_restock = $package_order_info['is_restock'];
            $restock_quantity = $package_order_info['quantity'];
            $restock_point = $package_order_info['point'];
            $is_paid_to_company = $package_order_info['is_company'];

            if($is_voucher == 1){
                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, voucher_id", array('id' => $user_id, 'active' => 1));
                $voucher_id = isset($user_info['id']) ? $user_info['voucher_id'] : 0;
            }else{
                $voucher_id = 0;
            }

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = $company_info['type'];
            
            // stock to rb voucher percentage
            $rb_voucher_percentage = $company_info['rb_voucher_qty'];

            // per rb voucher value
            $rb_voucher_value = $company_info['rb_voucher_value'];

            // check is rb is active
            if($rb_voucher_percentage != 0){
                $is_active_rb = true;
            }else{
                $is_active_rb = false;
            }

            // check is tuo li feature is active
            if($company_info['break_away_bonus'] != 0.00){
                $is_active_break_away = true;
            }else{
                $is_active_break_away = false;
            }


            // check is yue ji feature is active
            if($company_info['cross_over_bonus'] != 0.00){
                // if restock, yue ji will be disabled
                if($is_restock == 1){
                    $is_active_cross_over = false;
                }else{
                    $is_active_cross_over = true;
                }
            }else{
                $is_active_cross_over = false;
            }

            if($company_info['smart_partner_bonus'] != "0.00"){
                $is_active_smart_partner = true;
            }else{
                $is_active_smart_partner = false;
            }

            // get package details
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
            $package_name = isset($package_info['id']) ? $package_info['name'] : "";
            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
            $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
            $package_grand_total = isset($package_info['id']) ? $package_info['grand_total'] : 0;

            $big_present_log_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT_LOG, "*", "WHERE user_id = '$user_id' AND active = '1' ORDER BY id DESC");
            $voucher_log_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "*", array('package_id' => $package_id, 'voucher_id' => $voucher_id, 'active' => 1, 'register_user_id' => $user_id));
            if(isset($big_present_log_info['id']) && $big_present_log_info['id'] > 0){
                $big_present_id = $big_present_log_info['big_present_id'];
                $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $big_present_id, 'active' => 1));
                
                $total_quantity = $big_present_info['total_stock'];
                $grand_total = $big_present_info['price'];
            }else if(isset($voucher_log_info['id']) && $voucher_log_info['id'] > 0){
                $voucher_id = $voucher_log_info['voucher_id'];
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                
                $total_quantity = $voucher_info['total_stock'];
                $grand_total = $voucher_info['price'];
            }else{
                if($is_restock == 1){
                    $total_quantity = $restock_quantity;
                    $grand_total = $restock_point;
                }else{
                    $total_quantity = $package_quantity;
                    $grand_total = $package_grand_total;
                }
            }

            $data_update = array(
                'status' => "APPROVE",
                'approved_at' => $this->update_time
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $id, 'active' => 1), $data_update);

            // approve upgrade package
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $user_id, 'active' => 1));
            $current_package_id = isset($member_info['id']) ? $member_info['package_id'] : 0;
            if($package_id > $current_package_id){
                $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_UPGRADE, "*", array('user_id' => $user_id, 'status' => "PENDING", 'active' => 1));
                $data_upgrade = array('status' => "APPROVE");
                $this->Api_Model->update_data(TBL_UPGRADE, array('user_id' => $user_id, 'status' => "PENDING", 'active' => 1), $data_upgrade);
                $data_user_upgrade = array('package_id' => $package_id);
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_upgrade);
            }

            if($company_type == "FIXED"){
                if($is_paid_to_company == 1){
                    $available_stock_balance = $this->check_stock_balance_post($user_id);

                    $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");
                    $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit");
                }else{
                    $available_stock_balance = $this->check_stock_balance_post($referral_id);
                
                    if($available_stock_balance == $total_quantity){
                        // deduct stock from member acc
                        $this->proceed_stock_post($referral_id, $company_id, $purchase_id, $total_quantity, "debit");

                        // insert stock into current acc
                        $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");

                        // update total stock into referral acc
                        $this->update_member_stock_post($available_stock_balance, $total_quantity, $referral_id, "debit");

                        // update total stock into current acc
                        $available_stock_balance = $this->check_stock_balance_post($user_id);
                        $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit", true);
                    }else{
                        if($available_stock_balance < $total_quantity){
                            $data['message'] = "Insufficient Stock ! Please Inform to restock !";
                            $this->load->view("output/error_response", $data);
                        }else{
                            $this->proceed_stock_post($referral_id, $company_id, $purchase_id, $total_quantity, "debit");
                            $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");
                            $this->update_member_stock_post($available_stock_balance, $total_quantity, $referral_id, "debit");
                            $available_stock_balance = $this->check_stock_balance_post($user_id);
                            $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit", true);
                        }
                    }
                }

                if($is_active_smart_partner){
                    // check is available for smart partner and update become smart partner
                    $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
                    if(in_array($package_id, $package_id_arr)){
                        $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'is_smart_partner' => 1));
                        $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                        $is_available_smart_partner = $this->check_is_smart_partner_available($user_id);
                        $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $user_id);
                    }
                }

                // give cb point
                $cb_point = $total_quantity / 10;
                $this->give_cb_point($user_id, $package_name, $cb_point, $purchase_id);

                // if active rb, rb voucher process here
                if($is_active_rb){
                    $this->proceed_rb_voucher_post($rb_voucher_percentage, $rb_voucher_value, $total_quantity, $package_price, $user_id, $company_id, $package_id, $package_name);
                }

                // check is current package member got upline or not
                $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                $referral_id = $member_info['referral_id'];
                $user_package = $member_info['package_id'];

                // get upline info
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id", array('id' => $referral_id, 'active' => 1));
                $referral_package = isset($referral_info['id']) ? $referral_info['package_id'] : 0;

                $yue_ji_log_info = $this->Api_Model->get_rows_info(TBL_YUE_JI_LOG, "id", array('active' => 1, 'referral_id' => $referral_id, 'user_id' => $user_id)); 
                $referral_is_cross_bonus = isset($yue_ji_log_info['id']) ? 1 : 0;

                if($referral_id != 0){
                    $current_month = date("m");
                    $current_year = date("Y");
                    
                    $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
                    if($is_restock == 1 && $is_max_package){
                        $is_got_referral_two_max_package = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away", false, true);

                        if($is_max_package || $is_got_referral_two_max_package){
                            // restock break away process here
                            $this->check_break_away_restock_requirement("package", $purchase_id, $user_id);
                        }
                    }else{
                        if($referral_package < $user_package){
                            // check if the referral got find 2 max package
                            $is_got_referral_two_max_package = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away");

                            // check is the referral got referral 1 max package and group sales is max package quantity
                            $personal_group_total_conditions = $this->check_break_away_team_total_box($user_id, $current_month, $current_year);

                            if($is_got_referral_two_max_package || $personal_group_total_conditions['data']['status']){
                                // break away process here
                                if($is_active_break_away){
                                    $this->check_break_away_requirement("package", $purchase_id, $user_id);
                                }
                            }else{
                                // cross over process here
                                if($is_active_cross_over){
                                    if($referral_is_cross_bonus == 0){
                                        $this->check_cross_away_requirement("package", $purchase_id, $user_id);
                                    }else{
                                        $data_purchase_package_update = array('is_paid' => 1);
                                        $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                    }
                                }else{
                                    $data_purchase_package_update = array('is_paid' => 1);
                                    $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                }
                            }
                        }else if($is_active_break_away){
                            $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
                            $personal_group_total_conditions = $this->check_break_away_team_total_box($member_info['id'], $current_month, $current_year, "", true);

                            if($is_max_package){
                                // break away process here
                                $this->check_break_away_requirement("package", $purchase_id, $user_id);
                            }else if($personal_group_total_conditions['data']['status']){
                                $this->check_break_away_requirement("package", $purchase_id, $user_id, true);
                            }
                        }
                    }
                }
            }else{
                if($is_paid_to_company == 1){
                    $available_point_balance = $this->check_point_balance_post($user_id);

                    $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");
                    $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit");

                    // quantity stock
                    $available_stock_balance = $this->check_stock_balance_post($user_id);

                    $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");
                    $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit");
                }else{
                    $available_point_balance = $this->check_point_balance_post($referral_id);
                
                    if($available_point_balance == $grand_total){
                        // deduct stock from member acc
                        $this->proceed_point_post($referral_id, $company_id, $purchase_id, $grand_total, "debit");

                        // insert stock into current acc
                        $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");

                        // update total stock into referral acc
                        $this->update_member_point_post($available_point_balance, $grand_total, $referral_id, "debit");

                        // update total stock into current acc
                        $available_point_balance = $this->check_point_balance_post($user_id);
                        $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit", true);
                    }else{
                        if($available_point_balance < $grand_total){
                            $data['message'] = "Insufficient Point ! Please Inform to restock !";
                            $this->load->view("output/error_response", $data);
                        }else{
                            $this->proceed_point_post($referral_id, $company_id, $purchase_id, $grand_total, "debit");
                            $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");
                            $this->update_member_point_post($available_point_balance, $grand_total, $referral_id, "debit");
                            $available_point_balance = $this->check_point_balance_post($user_id);
                            $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit", true);
                        }
                    }

                    // quantity stock
                    $available_stock_balance = $this->check_stock_balance_post($referral_id);
                
                    if($available_stock_balance == $total_quantity){
                        // insert stock into current acc
                        $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");

                        // update total stock into current acc
                        $available_stock_balance = $this->check_stock_balance_post($user_id);
                        $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit", true);
                    }else{
                        $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, "credit");
                        $available_stock_balance = $this->check_stock_balance_post($user_id);
                        $this->update_member_stock_post($available_stock_balance, $total_quantity, $user_id, "credit", true);
                    }
                }
            }

            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Invalid Package Order";
            $this->load->view("output/error_response", $data);
        }
    }

    public function proceed_rb_voucher_post($rb_voucher_percentage, $rb_voucher_value, $package_quantity, $package_price, $user_id, $company_id, $package_id, $package_name){
        // insert rb voucher if have
        $rb_voucher_convert_qty = $rb_voucher_percentage / 100;
        $rb_voucher_convert_value = $rb_voucher_value / 100;
        $rb_quantity = $package_quantity * $rb_voucher_convert_qty;
        $rb_value_price = $package_price * $rb_voucher_convert_value;
        $rb_actual_price = $package_price - $rb_value_price;
        // check if the rb is < 1rb
        $is_insert_rb = ($rb_quantity < 1) ? 0 : 1;

        $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "*", array('user_id' => $user_id, 'company_id' => $company_id, 'package_id' => $package_id));
        $rb_current_quantity = isset($rb_voucher_info['quantity']) ? $rb_voucher_info['quantity'] : 0;
        $rb_voucher_id = isset($rb_voucher_info['id']) ? $rb_voucher_info['id'] : 0;
        $rb_new_quantity = $rb_current_quantity + $rb_quantity;

        $data_rb_voucher = array(
            'company_id' => $company_id,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'cost_price' => $package_price,
            'quantity' => $rb_quantity,
            'value_price' => $rb_value_price,
            'actual_price' => $rb_actual_price
        );

        if($is_insert_rb == 1){
            if($rb_current_quantity == 0){
                $rb_voucher_id = $this->Api_Model->insert_data(TBL_RB_VOUCHER, $data_rb_voucher);
            }else{
                $data_exist_voucher = array(
                    'quantity' => $rb_new_quantity
                );
                $this->Api_Model->update_data(TBL_RB_VOUCHER, array('id' => $rb_voucher_id, 'active' => 1), $data_exist_voucher);
            }
            
            $rb_voucher_balance = $this->check_rb_balance_post($rb_voucher_id, $user_id);
            $new_voucher_balance = $rb_voucher_balance + $rb_quantity;

            $data_rb_wallet = array(
                'rb_voucher_id' => $rb_voucher_id,
                'user_id' => $user_id,
                'description' => $package_name,
                'credit' => $rb_quantity,
                'balance' => $new_voucher_balance
            );
            $this->Api_Model->insert_data(TBL_RB_WALLET, $data_rb_wallet);
        }
    }

    public function approve_product_order(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $rb_voucher_id = $order_info['rb_voucher_id'];
            $total_price = $order_info['total_price'];
            $delivery_fee = $order_info['delivery_fee'];
            $order_subtotal = $total_price - $delivery_fee;
            $order_type = $order_info['type'];
            $is_restock = $order_info['is_restock'];
            $order_id = $order_info['id'];
            $purchase_user_id = $order_info['user_id'];
            $referral_id = $this->get_referral_id($purchase_user_id);    
        
            if($purchase_user_id == 0){
                $user_id = $referral_id;
            }else{
                $user_id = $purchase_user_id;
            }
            
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
            $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = $company_info['type'];

            $data_update = array(
                'status' => "APPROVE"
            );
            $this->Api_Model->update_data(TBL_ORDER, array('id' => $id, 'active' => 1), $data_update);

            $total_quantity = $order_info['total_quantity'];
            $total_price = $order_info['total_price'];
            $original_price = $order_info['original_price'];
            if($company_type == "FIXED"){
                if($order_type == "normal"){
                    $total_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }else if($order_type == "restock"){
                    $total_balance = $this->check_stock_balance_post($referral_id);
                    $deduct_user_id = $referral_id;
                }else if($order_type == "rb"){
                    $total_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }else if($order_type == "drb"){
                    $total_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }
                
                if($order_type == "normal" || $order_type == "restock" || $order_type == "rb" || $order_type == "drb"){
                    $new_balance = $total_balance - $total_quantity;

                    $data_stock = array(
                        'user_id' => $deduct_user_id,
                        'company_id' => $company_id,
                        'order_id' => $order_id,
                        'description' => "Product Shipment",
                        'debit' => $total_quantity,
                        'balance' => $new_balance
                    );
                    if($is_restock == 1 || $purchase_user_id == 0){
                        $data_stock['description'] = "Retail Order";
                    }
                    $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                    // update total quantity to agent acc
                    $data_user_update = array(
                        'total_stock' => $new_balance
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $deduct_user_id, 'active' => 1), $data_user_update);

                    // give cb point
                    $cb_point = $total_quantity / 10;
                    $this->give_cb_point($user_id, "Stock Repurchase", $cb_point, $order_id);

                    // if order type is rb
                    if($order_type == "rb"){
                        $rb_voucher_balance = $this->check_rb_balance_post($rb_voucher_id, $user_id);
                        $new_voucher_balance = $rb_voucher_balance - $total_quantity;
                        $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "id, package_id, quantity", array('id' => $rb_voucher_id, 'active' => 1));
                        $rb_voucher_package_id = isset($rb_voucher_info['id']) ? $rb_voucher_info['package_id'] : 0;

                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $rb_voucher_package_id, 'active' => 1));
                        $package_name = isset($package_info['id']) ? $package_info['name'] : "";
    
                        // update quantity of rb voucher
                        $data_rb_voucher = array('quantity' => $new_voucher_balance);
                        $this->Api_Model->update_data(TBL_RB_VOUCHER, array('id' => $rb_voucher_id, 'active' => 1), $data_rb_voucher);

                        // deduct into rb wallet
                        $data_rb_wallet = array(
                            'rb_voucher_id' => $rb_voucher_id,
                            'user_id' => $user_id,
                            'description' => "RB Redemption of " . $package_name,
                            'debit' => $total_quantity,
                            'balance' => $new_voucher_balance
                        );
                        $this->Api_Model->insert_data(TBL_RB_WALLET, $data_rb_wallet);
                    }

                    if($order_type == "drb"){
                        $drb_balance = $this->check_drb_balance_post($user_id);
                        $new_drb_balance = $drb_balance - $order_subtotal;

                        $data_drb = array(
                            'day' => date("d"),
                            'month' => date("m"),
                            'year' => date("Y"),
                            'company_id' => $company_id,
                            'user_id' => $user_id,
                            'total_quantity' => 0,
                            'price' => "0.00",
                            'description' => "Product Redemption",
                            'bonus' => $order_subtotal,
                            'is_deduct' => 1
                        );
                        $this->Api_Model->insert_data(TBL_DRB_REPORT, $data_drb);

                        $this->deduct_drb($user_id, $order_subtotal);
                    }
                }
            }else{
                if($order_type == "normal"){
                    $total_point_balance = $this->check_point_balance_post($user_id);
                    $total_stock_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }else if($order_type == "restock"){
                    $total__point_balance = $this->check_point_balance_post($referral_id);
                    $total_stock_balance = $this->check_stock_balance_post($referral_id);
                    $deduct_user_id = $referral_id;
                }else if($order_type == "rb"){
                    $total_point_balance = $this->check_point_balance_post($user_id);
                    $total_stock_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }else if($order_type == "drb"){
                    $total_point_balance = $this->check_point_balance_post($user_id);
                    $total_stock_balance = $this->check_stock_balance_post($user_id);
                    $deduct_user_id = $user_id;
                }

                $new_point_balance = $total_point_balance - $original_price;
                $new_stock_balance = $total_stock_balance - $total_quantity;

                $data_point = array(
                    'company_id' => $company_id,
                    'user_id' => $user_id,
                    'order_id' => $order_id,
                    'description' => "Product Shipment",
                    'debit' => $original_price,
                    'balance' => $new_point_balance
                );
                if($is_restock == 1 || $purchase_user_id == 0){
                    $data_point['description'] = "Retail Order";
                }
                $this->Api_Model->insert_data(TBL_POINT, $data_point);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_point' => $new_point_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);

                // update quantity of agent
                $data_stock = array(
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                    'order_id' => $order_id,
                    'description' => "Product Shipment",
                    'debit' => $total_quantity,
                    'balance' => $new_stock_balance
                );
                if($is_restock == 1 || $purchase_user_id == 0){
                    $data_stock['description'] = "Retail Order";
                }
                $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_stock' => $new_stock_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $deduct_user_id, 'active' => 1), $data_user_update);
            }

            $json['response_data'] = $data_update;
            $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Order";
            $this->load->view("output/error_response", $data);
        }
    }

    public function check_cross_away_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        // type => package/order
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id, is_voucher, voucher_id', array('id' => $user_id, 'active' => 1));
        $member_package_id = isset($member['id']) ? $member['package_id'] : 0;
        $company_id = isset($member['id']) ? $member['company_id'] : 0;
        $country_id = isset($member['id']) ? $member['country_id'] : 0;
        $referral_id = isset($member['id']) ? $member['referral_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, cross_over_bonus, any_cross_over_bonus", array('id' => $company_id, 'active' => 1));
        $cross_over_bonus_rate = isset($company_info['id']) ? $company_info['cross_over_bonus'] : "0.00";
        $any_cross_over_bonus = isset($company_info['id']) ? $company_info['any_cross_over_bonus'] : "0.00";

        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1), "id", "DESC", 1);
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        if($any_cross_over_bonus != "0.00"){
            $this->proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, 1);
        }else{
            if($cross_over_bonus_rate != "0.00"){
                if($member_package_id == $package_id){
                    $this->proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id);
                }
            }
        }
    }

    public function proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, $is_any_cross_over = 0){
        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $member_package_id, 'active' => 1));
        $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;

        $insert_name = $this->get_purchase_display_data($user_id);
        if($type == "package"){
            $total_stock = $this->get_team_break_away_total_box($member['is_voucher'], $member['voucher_id'], $member['package_id'], $member['company_id'], $member['country_id']);

            // whenever which package is greater than current referral package
            if($is_any_cross_over == 1){
                $cross_over_bonus = $total_stock * $any_cross_over_bonus;
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $any_cross_over_bonus . " per box";
            }else{
                // only allow small referral max package
                $cross_over_bonus = $total_stock * $cross_over_bonus_rate;
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $cross_over_bonus_rate . " per box";
            }
            
            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "cross_over", $cross_over_remark, $cross_over_bonus);
            $data_purchase_package_update = array(
                // 'is_paid' => 1,
                'wallet_id' => $wallet_id
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

            $data_yue_ji_log = array(
                'referral_id' => $referral_id,
                'user_id' => $user_id
            );
            $this->Api_Model->insert_data(TBL_YUE_JI_LOG, $data_yue_ji_log);
        }
    }

    public function check_break_away_restock_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");
        
        $two_max_package_conditions = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away", false, true);

        if($two_max_package_conditions){
            $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $user_id, 'active' => 1));
            $company_id = isset($member['id']) ? $member['company_id'] : 0;
            $country_id = isset($member['id']) ? $member['country_id'] : 0;
            $referral_id = isset($member['id']) ? $member['referral_id'] : 0;
            $member_package_id = isset($member['id']) ? $member['package_id'] : 0;

            $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
            $package_id = implode("','", $package_id_arr);
            $purchase_package_list = $this->Api_Model->get_all_sql(TBL_PURCHASE_PACKAGE, 'id, user_id, wallet_id', "WHERE active = '1' AND referral_id = '" . $member['id'] . "' AND MONTH(insert_time) = '$current_month' AND YEAR(insert_time) = '$current_year' AND package_id IN ('" . $package_id . "') ORDER BY id ASC");

            // if got find under referral is 2 max package
            if(count($purchase_package_list) > 1){
                if($type == "package"){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $company_id, 'active' => 1));
                    $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                    //  check if self got fulfil the requirement for max package
                    $is_self_max_package = $this->check_is_package_max_package($member_package_id, $company_id, $country_id, "break_away");

                    if($is_self_max_package && $break_away_bonus_rate != "0.00"){
                        foreach($purchase_package_list as $row_purchase_package){
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, wallet_id, user_id', array('referral_id' => $member['id'], 'active' => 1, 'id' => $row_purchase_package['id']), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $order_wallet_id = $purchase_package_info['wallet_id'];

                            $data_wallet_update = array(
                                'active' => 0
                            );
                            $this->Api_Model->update_data(TBL_WALLET, array('id' => $order_wallet_id, 'active' => 1), $data_wallet_update);

                            $insert_name = $this->get_purchase_display_data($user_id);

                            $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            // remove cross over comm if have take before
                            $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                            if($check_is_got_cross_over_comm['data']['status']){
                                $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                                
                                $data_wallet_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                                $data_yue_ji_log_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                            }

                            // give break away bonus to referral that one time restock become max package
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                            $wallet_id = $this->give_cash_wallet_comm($member['id'], $order_user_user_id, "break_away", $break_away_remark, $break_away_bonus);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                            $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                            $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                            $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                        }
                    }
                }
            }
        }
    }

    public function check_break_away_requirement($type = "", $order_id, $user_id, $is_personal_group_sales = false){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");
        // type => package/order
        if($is_personal_group_sales){
            $break_away_conditions = false;
        }else{
            $break_away_conditions = $this->check_upline_is_max_package($user_id);
        }
        $two_max_package_conditions = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away");
        $personal_group_total_conditions = $this->check_break_away_team_total_box($user_id, $current_month, $current_year);

        if(($break_away_conditions || $two_max_package_conditions || $personal_group_total_conditions['data']['status']) && $type != ""){
            $company_id = $this->get_company_id($user_id);
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus, smart_partner_bonus", array('id' => $company_id, 'active' => 1));
            $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";
            $smart_partner_bonus = isset($company_info['id']) ? $company_info['smart_partner_bonus'] : "0.00";
            // first conditions, self must is max package, and referral is max package also
            if($break_away_conditions){
                $fulfil_break_away_upline_id = $this->get_fulfil_upline_post($user_id, "break_away");
                if($fulfil_break_away_upline_id != 0){
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                    $referral_id = $this->get_referral_id($user_id);

                    // check is referral got more than 3 organization then pass 1 to upline
                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
                    $package_id_arr = $this->get_conditions_package_id_array($referral_info['company_id'], "break_away");
                    $package_id = implode("','", $package_id_arr);
                    $total_referral_info = $this->Api_Model->get_info_sql(TBL_USER, "id, COUNT(*) as total_referral", "WHERE active = '1' AND referral_id = '$referral_id' AND package_id IN ('" . $package_id . "')");
                    $total_referral = $total_referral_info['total_referral'];
                    
                    $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, COUNT(*) as total_wallet_record", array('to_user_id' => $referral_id, 'MONTH(insert_time)' => $current_month));

                    if($total_referral > 2){
                        $allow_to_take_bonus = $total_referral - 1;
                        $total_wallet_record = $wallet_info['total_wallet_record'];
                        if($total_wallet_record == $allow_to_take_bonus){
                            $referral_upline_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $referral_id, 'active' => 1));
                            $upline_id = $referral_upline_info['referral_id'];

                            $upline_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, company_id, country_id, package_id', array('id' => $upline_id, 'active' => 1));
                            $is_max_package = $this->check_is_package_max_package($upline_info['package_id'], $upline_info['company_id'], $upline_info['country_id'], "break_away");
                            if($is_max_package){
                                $referral_upline_id = $upline_id;
                            }else{
                                $referral_upline_id = $this->get_fulfil_upline_post($upline_id, "break_away");
                            }

                            $insert_name = $this->get_purchase_display_data($user_id);
                            if($type == "package"){
                                $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                                $break_away_bonus = $total_stock * $break_away_bonus_rate;
                                $break_away_remark = "Break Away Pension (" . strtoupper($month) . ") | : Pension by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                                if($break_away_bonus != "0.00"){
                                    $wallet_id = $this->give_cash_wallet_comm($referral_upline_id, $user_id, "break_away", $break_away_remark, $break_away_bonus);
                                    $data_purchase_package_update = array(
                                        'is_paid' => 1,
                                        'wallet_id' => $wallet_id
                                    );
                                    $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

                                    // check is available for smart partner and update become smart partner
                                    if($smart_partner_bonus != "0.00"){
                                        $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_upline_id, 'is_smart_partner' => 1));
                                        $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                        $is_available_smart_partner = $this->check_is_smart_partner_available($referral_upline_id);
                                        $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_upline_id);
                                    }
                                }
                            }
                        }
                    }else{
                        $insert_name = $this->get_purchase_display_data($user_id);
                        if($type == "package"){
                            $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                            $break_away_bonus = $total_stock * $break_away_bonus_rate;
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                            if($break_away_bonus != "0.00"){
                                $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus);
                                $data_purchase_package_update = array(
                                    'is_paid' => 1,
                                    'wallet_id' => $wallet_id
                                );
                                $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

                                // check is available for smart partner and update become smart partner
                                if($smart_partner_bonus != "0.00"){
                                    $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                                    $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                    $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                                    $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                                }
                            }
                        }
                    }
                }
            }else if($two_max_package_conditions){
                $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $user_id, 'active' => 1));
                $company_id = isset($member['id']) ? $member['company_id'] : 0;
                $country_id = isset($member['id']) ? $member['country_id'] : 0;
                $referral_id = isset($member['id']) ? $member['referral_id'] : 0;
                $package_id = isset($member['id']) ? $member['package_id'] : 0;

                $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
                $package_id = implode("','", $package_id_arr);
                $purchase_package_list = $this->Api_Model->get_all_sql(TBL_PURCHASE_PACKAGE, 'id, user_id', "WHERE active = '1' AND is_paid = '0' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$current_month' AND YEAR(insert_time) = '$current_year' AND package_id IN ('" . $package_id . "') ORDER BY id ASC");
                
                // if got find under referral is 2 max package
                if(count($purchase_package_list) > 1){
                    if($type == "package"){
                        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $company_id, 'active' => 1));
                        $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                        //  check if self got fulfil the requirement for max package
                        $is_self_max_package = $this->check_is_package_max_package($package_id, $company_id, $country_id, "break_away");

                        if(!$is_self_max_package && $break_away_bonus_rate != "0.00"){
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, user_id, SUM(amount) as total_qty_amount', array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $total_stock = $purchase_package_info['total_qty_amount'] / 2;
                            $insert_name = $this->get_purchase_display_data($user_id);

                            // $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            // remove cross over comm if have take before
                            $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                            if($check_is_got_cross_over_comm['data']['status']){
                                $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                                
                                $data_wallet_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                                $data_yue_ji_log_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                            }

                            // give break away bonus to referral that no fulfil the requirement of max package boxes
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            if($smart_partner_bonus != "0.00"){
                                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                                $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                                $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                            }

                            // give another break away bonus to their upline of the current referral
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, user_id', array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $insert_name = $this->get_purchase_display_data($referral_id);

                            $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $order_user_user_id, 'active' => 1));
                            $referral_upline_id = $member['referral_id'];

                            // get until the upline is max package
                            $upline_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, company_id, country_id, package_id', array('id' => $referral_upline_id, 'active' => 1));
                            $is_max_package = $this->check_is_package_max_package($upline_info['package_id'], $upline_info['company_id'], $upline_info['country_id'], "break_away");
                            if($is_max_package){
                                $referral_upline_id = $referral_upline_id;
                            }else{
                                $referral_upline_id = $this->get_fulfil_upline_post($referral_upline_id, "break_away");
                            }

                            // $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            $break_away_remark = "Break Away Pension (" . strtoupper($month) . ") | : Pension by " . $insert_name . " | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                            $wallet_id = $this->give_cash_wallet_comm($referral_upline_id, $referral_id, "break_away", $break_away_remark, $break_away_bonus);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            if($smart_partner_bonus != "0.00"){
                                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_upline_id, 'is_smart_partner' => 1));
                                $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                $is_available_smart_partner = $this->check_is_smart_partner_available($referral_upline_id);
                                $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_upline_id);
                            }
                        }
                    }
                }
            }else if($personal_group_total_conditions['data']['status']){
                $purchase_id_arr = $personal_group_total_conditions['data']['purchase_id'];
                $referral_id = $personal_group_total_conditions['data']['referral_id'];
                $total_quantity = $personal_group_total_conditions['data']['total_quantity'];
                $referral_total_stock = $this->get_balance_purchase_quantity_total($current_month, $referral_id, 1);
                $total_quantity += $referral_total_stock;

                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, package_id, is_voucher, voucher_id", array('id' => $referral_id, 'active' => 1));
                $total_stock = $total_quantity;
                // $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                // remove cross over comm if have take before
                $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                if($check_is_got_cross_over_comm['data']['status']){
                    $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                    
                    $data_wallet_update = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                    $data_yue_ji_log_update = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                }

                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $user_info['company_id'], 'active' => 1));
                $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                $break_away_bonus = $total_stock * $break_away_bonus_rate;

                $break_away_remark = "Break Away (" . strtoupper($month) . ") | Personal Group Sales | " . $total_stock . " boxes @ RM" . $break_away_bonus_rate . " per box";
                if($break_away_bonus != "0.00"){
                    $wallet_id = $this->give_cash_wallet_comm($referral_id, 0, "break_away", $break_away_remark, $break_away_bonus);
                    foreach($purchase_id_arr as $purchase_id){
                        $data_purchase_package_update = array(
                            'is_paid' => 1,
                            'wallet_id' => $wallet_id
                        );
                        $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                    }

                    // check is available for smart partner and update become smart partner
                    if($smart_partner_bonus != "0.00"){
                        $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                        $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                        $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                        $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                    }
                }
            }
        }
    }

    // check is upline is max package or not
    public function check_upline_is_max_package($insert_id = 0){
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $insert_id, 'active' => 1));
        $company_id = isset($member['id']) ? $member['company_id'] : 0;
        $country_id = isset($member['id']) ? $member['country_id'] : 0;

        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1), "id", "DESC", 1);
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, package_id', array('id' => $member['referral_id'], 'active' => 1));
        $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;
            
        if($referral_package_id == $package_id && $referral_package_id != 0 && $package_id != 0){
            return true;
        }else{
            return false;
        }
    }

    // check is upline is got referral 2 max package
    public function check_upline_is_referral_two_max_package($insert_id = 0, $month, $year, $condition, $is_smart_partner_check = false, $is_restock = false, $debug = false){
        $break_away_package_list = array();
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id', array('id' => $insert_id, 'active' => 1));
        $company_id = isset($member['id']) ? $member['company_id'] : 0;

        $package_id_arr = $this->get_conditions_package_id_array($company_id, $condition);
        $package_id = implode("','", $package_id_arr);

        if($is_smart_partner_check){
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE active = '1' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }else if($is_restock){
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE active = '1' AND referral_id = '" . $member['id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }else{
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE is_paid = '0' AND active = '1' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }
        
        $total_referral_max_package = isset($referral_info['id']) ? $referral_info['total_referral'] : 0;

        if($total_referral_max_package > 1){
            return true;
        }else{
            return false;
        }
    }

    // check is upline team total got fulfil the break away box requirement
    public function check_break_away_team_total_box($insert_id = 0, $month, $year, $is_smart_partner_check = false, $debug = false){
        $result['data'] = [];
        $purchase_id = [];
    
        $total_quantity = 0;
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, is_voucher, voucher_id, package_id, company_id, country_id', array('id' => $insert_id, 'active' => 1));
    
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $company_type = $this->get_company_type($member_info['company_id']);
            $referral_id = $member_info['referral_id'];
    
            if($company_type == "FIXED"){
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
                $referral_stock = $this->get_balance_purchase_quantity_total($month, $referral_info['id']);
                $referral_package_id = $referral_info['package_id'];
                $referral_company_id = $referral_info['company_id'];
                $package_id_arr = $this->get_conditions_package_id_array($referral_company_id, "break_away");
                
                if(in_array($referral_package_id, $package_id_arr)){
                    $total_quantity += $referral_stock;
                }

                $referral_total_stock = $this->get_balance_purchase_quantity_total($month, $referral_info['id'], 1);

                if($is_smart_partner_check){
                    $team_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('referral_id' => $referral_id, 'active' => 1, 'package_id' => 5, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'status' => "APPROVE"));
                }else{
                    $team_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'status' => "APPROVE"));
                }
                
                if(!empty($team_list)){
                    foreach($team_list as $row_team){
                        $purchase_id[] = $row_team['id'];
                        $member = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $row_team['user_id'], 'active' => 1));
                        $total_stock = $this->get_team_break_away_total_box($member['is_voucher'], $member['voucher_id'], $member['package_id'], $member['company_id'], $member['country_id']);
                        $max_package_quantity = $this->get_conditions_package_quantity_array($member['id'], "break_away");
                        
                        if($total_stock != $max_package_quantity){
                            $total_quantity += $total_stock;
                        }
                    }
                }

                // must sponsor at least 1 direct max package
                // $is_got_sponsor_at_least_one_max_package = false;
                // $package_id_arr = $this->get_conditions_package_id_array($referral_info['company_id'], "break_away");
                // $package_id = implode("','", $package_id_arr);
                // $member_list = $this->Api_Model->get_all_sql(TBL_USER, "id", "WHERE active = '1' AND referral_id = '$referral_id' AND package_id IN ('" . $package_id . "')");
                
                // if(count($member_list) > 0){
                //     $is_got_sponsor_at_least_one_max_package = true;
                // }
            }
        }

        if($total_quantity >= $referral_total_stock && $total_quantity != 0 && $referral_total_stock != 0){
            $status = true;
        }else{
            $status = false;
        }

        $row['status'] = $status;
        $row['purchase_id'] = $purchase_id;
        $row['referral_id'] = $referral_id;
        $row['total_quantity'] = $total_quantity;
    
        $result['data'] = $row;
    
        return $result;
    }

    // smart partner code start 

    public function get_smart_partner_qualified_member($insert_id = 0, $company_id){
        $smart_partner_id = array();
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, '*', array('id' => $insert_id, 'active' => 1, 'company_id' => $company_id));
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $top_member_id = $member_info['id'];
            // $total_group += 1;

            $tmp_output = array($top_member_id);
            $downline_list = $this->Api_Model->get_rows(TBL_USER, "id", array('referral_id' => $top_member_id, 'active' => 1, 'id !=' => $member_info['id'], 'company_id' => $company_id));
            while(count($downline_list) > 0){
                $query_smart_partner = implode("','", array_column($downline_list, "id"));
                $smart_partner = $this->Api_Model->get_all_sql(TBL_USER, "id", "WHERE id IN ('".$query_smart_partner."') AND is_smart_partner = '1' AND company_id = '$company_id' AND active = '1'");

                if(count($downline_list) > 0){
                    if(count($smart_partner) > 0){
                        // $total_group += count($smart_partner);
                        foreach($smart_partner as $row_smart_partner){
                            $member_id[] = $row_smart_partner['id'];
                            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('referral_id' => $row_smart_partner['id'], 'active' => 1));
                            $is_got_referral = isset($member_info['id']) ? 1 : 0;
                            if($is_got_referral == 1){
                                $smart_partner_id = array_merge($tmp_output, $member_id);
                            }
                        }
                    }
                }
                else{
                    break;
                }

                $query_str = implode("','", array_column($downline_list, "id"));
                $downline_list = $this->Api_Model->get_all_sql(TBL_USER, 'id', "WHERE referral_id IN ('".$query_str."') AND company_id = '$company_id' AND active = '1'");
            }
        }

        return $smart_partner_id;
    }

    public function insert_smart_partner_total_sales($month, $company_id){
        $first_member_company_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('company_id' => $company_id, 'active' => 1, 'user_type' => "AGENT"), "id", "ASC");
        $first_member_id = isset($first_member_company_info['id']) ? $first_member_company_info['id'] : 0;

        $smart_partner_arr = $this->get_smart_partner_qualified_member($first_member_id, $company_id);
        if(!empty($smart_partner_arr)){
            foreach($smart_partner_arr as $smart_partner_id){
                $company_id = $this->get_company_id($smart_partner_id);
                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, smart_partner_bonus", array('id' => $company_id, 'active' => 1));
                $smart_partner_bonus = isset($company_info['id']) ? $company_info['smart_partner_bonus'] : "0.00";

                $company_sales = $this->check_package_total_post("", $month);
                $total_quantity = $this->check_package_total_post($smart_partner_id, $month);
                $company_sales_after_bonus = $company_sales * $smart_partner_bonus;

                $data = array(
                    'company_id' => $company_id,
                    'user_id' => $smart_partner_id,
                    'month' => $month,
                    'year' => date("Y"),
                    'company_sales' => $company_sales,
                    'company_sales_after_bonus' => $company_sales_after_bonus,
                    'total_sales' => $total_quantity
                );
                if($company_sales != 0){
                    $this->Api_Model->insert_data(TBL_SMART_PARTNER, $data);
                }
            }
        }
    }

    public function proceed_smart_partner(){
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 1;

        $smart_partner_list = $this->Api_Model->get_rows(TBL_SMART_PARTNER, "id", array('month' => $month, 'active' => 1, 'company_id' => $company_id));
        if(count($smart_partner_list) > 0){
            $data['message'] = "You already generate for this month";
            $this->load->view("output/error_response", $data);
        }else{
            $this->insert_smart_partner_total_sales($month, $company_id);

            // get all qualified smart partner
            $smart_partner_qualified_list = $this->Api_Model->get_rows(TBL_SMART_PARTNER, "*", array('active' => 1, 'month' => $month, 'company_id' => $company_id), "", "", "id", "DESC");
            if(!empty($smart_partner_qualified_list)){
                // debugPrintArr($smart_partner_qualified_list); die;
                foreach($smart_partner_qualified_list as $spkey => $row_smart_partner){
                    $smart_partner_id = $row_smart_partner['user_id'];
                    
                    // smart partner personal sales
                    $total_sales = $row_smart_partner['total_sales'];

                    $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id" , array('id' => $smart_partner_id, 'active' => 1, 'company_id' => $company_id));
                    // check is got upline of current smart partner
                    $is_got_upline = ($member_info['referral_id'] != 0) ? 1 : 0;

                    // if($smart_partner_id == 7){
                        if($is_got_upline){
                            $break_away_conditions = $this->check_upline_is_max_package($smart_partner_id, true);
                            $two_max_package_conditions = $this->check_upline_is_referral_two_max_package($smart_partner_id, $month, date("Y"), "break_away", true);
                            $personal_group_total_conditions = $this->check_break_away_team_total_box($smart_partner_id, $month, date("Y"), true, true);

                            if($break_away_conditions || $two_max_package_conditions || $personal_group_total_conditions['data']['status']){
                                // echo "1 - " . $smart_partner_id . "<br>";
                                $upline_id = $member_info['referral_id'];
                                
                                // get total sales pass up by downline
                                $smart_partner_upline_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER, "id, sales_pass_up", array('user_id' => $upline_id, 'active' => 1, 'company_id' => $company_id));
                                $upline_sales = isset($smart_partner_upline_info['id']) ? $smart_partner_upline_info['sales_pass_up'] : 0;

                                // reget total sales pass up by downline
                                $smart_partner_pass_up_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER_PASS_UP, "id, SUM(sales_pass_up) as total_sales_pass_up", array('user_id' => $smart_partner_id, 'active' => 1, 'month' => $month, 'year' => date("Y")));
                                $sales_pass_up = isset($smart_partner_pass_up_info['id']) ? $smart_partner_pass_up_info['total_sales_pass_up'] : 0;

                                // if self not fulfil the max package condition but got referral 2 max package
                                if(!$break_away_conditions && $two_max_package_conditions){
                                    $top_upline_id = $this->get_fulfil_upline_post($upline_id, "smart_partner");

                                    $grand_total_sales_before_pass = $total_sales + $sales_pass_up;
                                    if($grand_total_sales_before_pass <= 300){
                                        $self_total_sales_pass_to_upline = ($total_sales + $sales_pass_up) / 2;
                                    }else{
                                        $self_total_sales_pass_to_upline = 150;
                                    }
                                    $self_total_sales_pass_to_upline1 = $self_total_sales_pass_to_upline / 2;
                                    $self_total_sales_pass_to_upline2 = $self_total_sales_pass_to_upline / 2;
        
                                    $data_smart_pass_up1 = array(
                                        'from_user_id' => $smart_partner_id,
                                        'user_id' => $upline_id,
                                        'month' => $month,
                                        'year' => date("Y"),
                                        'sales_pass_up' => $self_total_sales_pass_to_upline1
                                    );

                                    $data_smart_pass_up2 = array(
                                        'from_user_id' => $upline_id,
                                        'user_id' => $top_upline_id,
                                        'month' => $month,
                                        'year' => date("Y"),
                                        'sales_pass_up' => $self_total_sales_pass_to_upline2
                                    );
                                    if($self_total_sales_pass_to_upline1 != 0 || $self_total_sales_pass_to_upline2 != 0){
                                        $this->Api_Model->insert_data(TBL_SMART_PARTNER_PASS_UP, $data_smart_pass_up1);
                                        $this->Api_Model->insert_data(TBL_SMART_PARTNER_PASS_UP, $data_smart_pass_up2);
                                    }
                                }else{
                                    $grand_total_sales_before_pass = $total_sales + $sales_pass_up;
                                    if($grand_total_sales_before_pass <= 300){
                                        $self_total_sales_pass_to_upline = ($total_sales + $sales_pass_up) / 2;
                                    }else{
                                        $self_total_sales_pass_to_upline = 150;
                                    }
                                    $grand_sales_to_pass_up = $self_total_sales_pass_to_upline;
        
                                    $sales_pass_to_upline = $grand_sales_to_pass_up;
        
                                    $data_smart_pass_up = array(
                                        'from_user_id' => $smart_partner_id,
                                        'user_id' => $upline_id,
                                        'month' => $month,
                                        'year' => date("Y"),
                                        'sales_pass_up' => $sales_pass_to_upline
                                    );
                                    if($sales_pass_to_upline != 0){
                                        $this->Api_Model->insert_data(TBL_SMART_PARTNER_PASS_UP, $data_smart_pass_up);
                                    }
                                }
                            }
                        }
                    // }
                
                    $total_smart_partner_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER_PASS_UP, "id, SUM(sales_pass_up) as total_sales_pass_up", array('user_id' => $smart_partner_id, 'active' => 1, 'month' => $month, 'year' => date("Y")));
                    $grand_total_pass_up_sales = isset($total_smart_partner_info['id']) ? $total_smart_partner_info['total_sales_pass_up'] : 0;

                    // update total pass up sales to smart partner db
                    $data_smart_partner_pass_up = array(
                        'sales_pass_up' => $grand_total_pass_up_sales
                    );
                    $this->Api_Model->update_data(TBL_SMART_PARTNER, array('user_id' => $smart_partner_id, 'active' => 1), $data_smart_partner_pass_up);

                    // reget smart partner data
                    $smart_partner_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER, "id, total_sales", array('user_id' => $smart_partner_id, 'active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
                    $grand_total_personal_sales = isset($smart_partner_info['id']) ? $smart_partner_info['total_sales'] : 0;
                    $final_total_sales = $grand_total_pass_up_sales + $grand_total_personal_sales;

                    // update total pass up sales + personal sales to smart partner db
                    $data_smart_partner_grand_sales = array(
                        'grand_sales' => $final_total_sales,
                    );
                    $this->Api_Model->update_data(TBL_SMART_PARTNER, array('user_id' => $smart_partner_id, 'active' => 1), $data_smart_partner_grand_sales);
                }

                // reget smart partner data for updating group sales
                $group_sales_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER, "id, SUM(grand_sales) as total_group_sales", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
                $total_group_sales = isset($group_sales_info['id']) ? $group_sales_info['total_group_sales'] : 0;

                $data_smart_partner_group_sales = array(
                    'group_sales' => $total_group_sales
                );
                $this->Api_Model->update_multiple_data(TBL_SMART_PARTNER, array('month' => $month, 'active' => 1), $data_smart_partner_group_sales);

                // reget smart partner data for calculate bonus per box
                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER, "id, group_sales, company_sales_after_bonus", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
                $total_group_sales = isset($smart_partner_info['id']) ? $smart_partner_info['group_sales'] : 0;
                $company_sales_after_bonus = isset($smart_partner_info['id']) ? $smart_partner_info['company_sales_after_bonus'] : 0;
                $bonus_per_box = $company_sales_after_bonus / $total_group_sales;
                $bonus_per_box_two_decimal = bcdiv($bonus_per_box, 1, 2);

                $data_smart_partner_bonus_per_box = array(
                    'bonus_per_box' => $bonus_per_box_two_decimal
                );
                $this->Api_Model->update_multiple_data(TBL_SMART_PARTNER, array('month' => $month, 'active' => 1), $data_smart_partner_bonus_per_box);

                // get smart partner list to calculate personal bonus
                $smart_partner_list = $this->Api_Model->get_rows(TBL_SMART_PARTNER, "*", array('active' => 1, 'month' => $month, 'company_id' => $company_id));
                foreach($smart_partner_list as $skey => $sval){
                    $smart_partner_id = $sval['user_id'];
                    $bonus_month = $sval['month'];
                    $bonus_year = $sval['year'];
                    $downline_pass_up_list = $this->Api_Model->get_all_sql(TBL_SMART_PARTNER_PASS_UP, "from_user_id", "WHERE user_id = '$smart_partner_id' AND month = '$bonus_month' AND year = '$bonus_year' AND active = '1'");
                    if(!empty($downline_pass_up_list)){
                        $member_username = array();
                        foreach($downline_pass_up_list as $row_pass_up){
                            $downline_id = $row_pass_up['from_user_id'];

                            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $downline_id, 'active' => 1, 'company_id' => $company_id));
                            $member_username[] = $member_info['username'];
                        }
                    }
                    // debugPrintArr($member_username);
                    $personal_sales = $sval['grand_sales'];
                    $bonus_per_box = $sval['bonus_per_box'];
                    $bonus = $bonus_per_box * $personal_sales;

                    $data_smart_partner_bonus = array(
                        'bonus' => $bonus
                    );
                    $this->Api_Model->update_data(TBL_SMART_PARTNER, array('user_id' => $smart_partner_id, 'active' => 1), $data_smart_partner_bonus);
                }

                $this->load->view("output/success_response");
            }else{
                $data['message'] = "Empty Data !";
                $this->load->view("output/error_response", $data);
            }
        }
    }

    public function released_smart_partner(){
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 1;

        // get smart partner list to calculate personal bonus
        $smart_partner_list = $this->Api_Model->get_rows(TBL_SMART_PARTNER, "*", array('active' => 1, 'month' => $month, 'company_id' => $company_id));
        foreach($smart_partner_list as $skey => $sval){
            $smart_partner_id = $sval['user_id'];
            $bonus_month = $sval['month'];
            $bonus_year = $sval['year'];
            $downline_pass_up_list = $this->Api_Model->get_all_sql(TBL_SMART_PARTNER_PASS_UP, "from_user_id", "WHERE user_id = '$smart_partner_id' AND month = '$bonus_month' AND year = '$bonus_year' AND active = '1'");
            if(!empty($downline_pass_up_list)){
                $member_username = array();
                foreach($downline_pass_up_list as $row_pass_up){
                    $downline_id = $row_pass_up['from_user_id'];

                    $member_info = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $downline_id, 'active' => 1, 'company_id' => $company_id));
                    $member_username[] = $member_info['username'];
                }
            }
            // debugPrintArr($member_username);
            $personal_sales = $sval['grand_sales'];
            $bonus_per_box = $sval['bonus_per_box'];
            $bonus = $sval['bonus'];
            $month = date('M', mktime(0, 0, 0, $bonus_month, 10));

            if(!empty($downline_pass_up_list)){
                $total_smart_partner_info = $this->Api_Model->get_rows_info(TBL_SMART_PARTNER_PASS_UP, "id, SUM(sales_pass_up) as total_sales_pass_up", array('user_id' => $smart_partner_id, 'active' => 1, 'month' => $bonus_month, 'year' => date("Y")));
                $grand_total_pass_up_sales = isset($total_smart_partner_info['id']) ? $total_smart_partner_info['total_sales_pass_up'] : 0;
                $pass_up_member = implode(', ', $member_username);
                // $pass_up_member = str_replace("'", "", $pass_up_member);
                $smart_partner_remark = "Smart Partner Bonus (" . strtoupper($month) . ") | " . $personal_sales . "SV @ " . $bonus_per_box . " per SV | " . $pass_up_member . " passed " . $grand_total_pass_up_sales . "SV to you.";
            }else{
                $smart_partner_remark = "Smart Partner Bonus (" . strtoupper($month) . ") | " . $personal_sales . "SV @ " . $bonus_per_box . " per SV";
            }
            if($bonus != "0.00"){
                $wallet_id = $this->give_cash_wallet_comm($smart_partner_id, 0, "smart_partner", $smart_partner_remark, $bonus);
            }
        }

        $this->load->view("output/success_response");
    }

    public function check_is_generate_smart_partner(){
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 1;

        $smart_partner_list = $this->Api_Model->get_rows(TBL_SMART_PARTNER, "id", array('month' => $month, 'active' => 1, 'company_id' => $company_id));
        if(count($smart_partner_list) > 0){
            $is_released_to_wallet = $this->Api_Model->get_rows_info(TBL_WALLET, "id", array('company_id' => $company_id, 'MONTH(insert_time)' => $month, 'type' => "smart_partner"));
            if(isset($is_released_to_wallet['id']) && $is_released_to_wallet['id'] > 0){
                $is_generate = 2;
            }else{
                $is_generate = 1;
            }
        }else{
            $is_released_to_wallet = $this->Api_Model->get_rows_info(TBL_WALLET, "id", array('company_id' => $company_id, 'MONTH(insert_time)' => $month, 'type' => "smart_partner"));
            if(isset($is_released_to_wallet['id']) && $is_released_to_wallet['id'] > 0){
                $is_generate = 2;
            }else{
                $is_generate = 0;
            }
        }

        $data['response_data'] = $is_generate;
        $this->load->view("output/success_response", $data);
    }

    // smart partner code end

    // mdb code start

    public function get_organization_group($insert_id = 0, $company_id){
        $group_arr_list = array();

        $member_info = $this->Api_Model->get_rows_info(TBL_USER, '*', array('id' => $insert_id, 'active' => 1, 'company_id' => $company_id));
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $country_id = $member_info['country_id'];
            $company_id = $member_info['company_id'];

            $top_member_id = $member_info['id'];

            $tmp_output = array($top_member_id);
            $downline_list = $this->Api_Model->get_rows(TBL_USER, "id", array('referral_id' => $top_member_id, 'active' => 1, 'company_id' => $company_id));
            while(count($downline_list) > 0){
                if(count($downline_list) > 0){
                    foreach($downline_list as $row_downline){
                        $member_id[] = $row_downline['id'];
                        $group_arr_list = array_merge($tmp_output, $member_id);
                    }                    
                }
                else{
                    break;
                }

                $query_str = implode("','", array_column($downline_list, "id"));
                $downline_list = $this->Api_Model->get_all_sql(TBL_USER, 'id', "WHERE referral_id IN ('".$query_str."') AND company_id = '$company_id'");
            }
        }

        return $group_arr_list;
    }

    public function get_mdb_group_quantity($insert_id = 0, $month){
        $total_quantity = 0;
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, '*', array('id' => $insert_id, 'active' => 1));
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $country_id = $member_info['country_id'];
            $company_id = $member_info['company_id'];
            $total_stock = $this->get_purchase_quantity_total($month, $member_info['id'], $member_info['package_id'], $member_info['company_id']);
            $total_quantity += $total_stock;

            $top_member_id = $member_info['id'];

            $tmp_output = array($top_member_id);
            $downline_list = $this->Api_Model->get_rows(TBL_USER, "id", array('referral_id' => $top_member_id, 'active' => 1));
            while(count($downline_list) > 0){
                $query_qualified_package = implode("','", array_column($downline_list, "id"));
                $personal_group_list = $this->Api_Model->get_all_sql(TBL_USER, "id", "WHERE id IN ('".$query_qualified_package."')");

                if(count($downline_list) > 0){
                    foreach($downline_list as $row_downline){
                        $member = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $row_downline['id'], 'active' => 1));
                        $total_stock = $this->get_purchase_quantity_total($month, $member_info['id'], $member_info['package_id'], $member_info['company_id']);
                        $total_quantity += $total_stock;
                    }
                }
                else{
                    break;
                }

                $query_str = implode("','", array_column($downline_list, "id"));
                $downline_list = $this->Api_Model->get_all_sql(TBL_USER, 'id', "WHERE referral_id IN ('".$query_str."')");
            }
        }

        return $total_quantity;
    }

    public function calculate_monthly_bonus(){
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $mdb_qualified_list = $this->Api_Model->get_rows(TBL_MONTHLY_BONUS_REPORT, "*", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
        $first_member_company_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('company_id' => $company_id, 'active' => 1, 'user_type' => "AGENT"), "id", "ASC");
        $first_member_id = isset($first_member_company_info['id']) ? $first_member_company_info['id'] : 0;

        $mdb_qualified_member = $this->get_organization_group($first_member_id, $company_id);
        if(!empty($mdb_qualified_member)){
            if(count($mdb_qualified_list) > 0){
                $data['message'] = "You already release this month bonus !";
                $this->load->view("output/error_response", $data);
            }else{
                foreach($mdb_qualified_member as $mdb_member_id){
                    $company_id = $this->get_company_id($mdb_member_id);
                    $total_quantity = $this->get_mdb_group_quantity($mdb_member_id, $month);

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, min_mdb_qty", array('id' => $company_id, 'active' => 1));
                    $min_mdb_quantity = isset($company_info['id']) ? $company_info['min_mdb_qty'] : 0;

                    if($total_quantity >= $min_mdb_quantity){
                        $monthly_bonus_info = $this->Api_Model->get_info_sql(TBL_MONTHLY_BONUS, "id, bonus", "WHERE active = '1' AND company_id = '$company_id' AND (from_amount <= '$total_quantity' AND to_amount >= '$total_quantity')");
                        $bonus_per_box = isset($monthly_bonus_info['id']) ? $monthly_bonus_info['bonus'] : "0.00";
                        $bonus = $bonus_per_box * $total_quantity;

                        $data = array(
                            'month' => $month,
                            'year' => date("Y"),
                            'company_id' => $company_id,
                            'user_id' => $mdb_member_id,
                            'total_quantity' => $total_quantity,
                            'bonus_per_box' => $bonus_per_box,
                            'bonus' => $bonus
                        );
                    
                        $this->Api_Model->insert_data(TBL_MONTHLY_BONUS_REPORT, $data);

                        $mdbm_remark = "MDS (" . strtoupper($month) . ") | " . $total_quantity . " Qty @ " . $bonus_per_box . " per Qty";
                        $this->give_cash_wallet_comm($mdb_member_id, 0, "mdbm", $mdbm_remark, $bonus);
                    }
                }

                $mdb_qualified_list = $this->Api_Model->get_rows(TBL_MONTHLY_BONUS_REPORT, "*", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
                if(empty($mdb_qualified_list)){
                    $data['message'] = "Empty Data !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->load->view("output/success_response");
                }
            }
        }else{
            $data['message'] = "Empty Data !";
            $this->load->view("output/error_response", $data);
        }
    }

    // mdb code end

    // quarterly code start

    public function calculate_quarterly_bonus(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $convert_numeric_to_text = date('M', mktime(0, 0, 0, $month, 10));
        $to_month = date('n', strtotime('+3 months', strtotime($convert_numeric_to_text)));

        $mdb_quarterly_qualified_list = $this->Api_Model->get_all_sql(TBL_QUARTERLY_BONUS_REPORT, "id", "WHERE active = '1' AND company_id = '$company_id' AND MONTH(insert_time) BETWEEN " . $month . " AND " . $to_month);

        $first_member_company_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('company_id' => $company_id, 'active' => 1, 'user_type' => "AGENT"), "id", "ASC");
        $first_member_id = isset($first_member_company_info['id']) ? $first_member_company_info['id'] : 0;

        $qualified_member = $this->get_organization_group($first_member_id, $company_id);
    
        if(!empty($qualified_member)){
            if(empty($mdb_quarterly_qualified_list)){
                foreach($qualified_member as $member_id){
                    $company_id = $this->get_company_id($member_id);
                    
                    $monthly_bonus_info = $this->Api_Model->get_info_sql(TBL_MONTHLY_BONUS_REPORT, "id, SUM(total_quantity) as total_quarterly_quantity", "WHERE active = '1' AND company_id = '$company_id' AND user_id = '$member_id' AND month BETWEEN " . $month . " AND " . $to_month);
                    $total_quarterly_quantity = isset($monthly_bonus_info['id']) ? $monthly_bonus_info['total_quarterly_quantity'] : "0.00";

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, min_quarterly_qty", array('id' => $company_id, 'active' => 1));
                    $min_quarterly_quantity = isset($company_info['id']) ? $company_info['min_quarterly_qty'] : 0;
            
                    if($total_quarterly_quantity >= $min_quarterly_quantity){
                        $quarterly_bonus_info = $this->Api_Model->get_info_sql(TBL_QUARTERLY_BONUS, "id, bonus", "WHERE active = '1' AND company_id = '$company_id' AND amount <= '$total_quarterly_quantity' ORDER BY id DESC LIMIT 1");
                        $bonus = isset($quarterly_bonus_info['id']) ? $quarterly_bonus_info['bonus'] : "0.00";
            
                        $data = array(
                            'from_month' => $month,
                            'to_month' => $to_month,
                            'year' => date("Y"),
                            'company_id' => $company_id,
                            'user_id' => $member_id,
                            'total_quantity' => $total_quarterly_quantity,
                            'bonus' => $bonus
                        );
                    
                        $this->Api_Model->insert_data(TBL_QUARTERLY_BONUS_REPORT, $data);

                        $mdbq_remark = "Seasonal Development Bonus From Month of " . strtoupper($month) . " to " . strtoupper($to_month) . " | " . $total_quarterly_quantity . " Qty @ " . $bonus . " Received";
                        $this->give_cash_wallet_comm($member_id, 0, "mdbq", $mdbq_remark, $bonus);
                    }
                }

                $mdb_qualified_list = $this->Api_Model->get_rows(TBL_QUARTERLY_BONUS_REPORT, "*", array('active' => 1, 'from_month' => $month, 'year' => date("Y"), 'company_id' => $company_id));

                if(empty($mdb_qualified_list)){
                    $data['message'] = "Empty Data !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->load->view("output/success_response");
                }
            }else{
                $data['message'] = "You already release this month bonus !";
                $this->load->view("output/error_response", $data);
            }
        }else{
            $data['message'] = "Empty Data !";
            $this->load->view("output/error_response", $data);
        }
    }

    // quarterly code end

    // mms code start

    public function give_mms_bonus_post($insert_id = 0, $company_id = 0, $month){
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, mms_level", array('id' => $company_id, 'active' => 1));
        $total_mms_level = isset($company_info['id']) ? $company_info['mms_level'] : 0;

        $mms_level = $this->Api_Model->get_rows(TBL_MMS_BONUS, "level, bonus", array('company_id' => $company_id, 'active' => 1));
        foreach($mms_level as $mlkey => $mlval){
            $mms_level_arr[] = $mlval['level'];
            $mms_bonus_arr[] = $mlval['bonus'];
        }
        $comm = array_combine($mms_level_arr, $mms_bonus_arr);
        
        $lvl = 1;
        $max_lvl = $total_mms_level;
        $is_got_downline = true;
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, package_id', array('id' => $insert_id, 'active' => 1));
        // debugPrintArr($member); die;
        if(isset($member['id']) && $member['id'] > 0){
            $comm_amt = 0;
            while($is_got_downline){
                if($member['referral_id'] != 0){
                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, package_id', array('id' => $member['referral_id'], 'active' => 1));

                    if(isset($referral_info['id']) && $referral_info['id'] > 0){
                        $bonus = isset($comm[$lvl])?$comm[$lvl]:$comm[3];

                        $mms_report_info = $this->Api_Model->get_rows_info(TBL_MMS_REPORT, "id", array('from_user_id' => $member['id'], 'user_id' => $member['referral_id'], 'level' => $lvl, 'active' => 1));
                        // $is_record_exist = isset($mms_report_info['id']) ? 1 : 0;

                        $data_mms = array(
                            'month' => $month,
                            'year' => date("Y"),
                            'company_id' => $company_id,
                            'from_user_id' => $insert_id,
                            'user_id' => $member['referral_id'],
                            'level' => $lvl,
                            'bonus' => $bonus
                        );
                        $this->Api_Model->insert_data(TBL_MMS_REPORT, $data_mms);

                        $mms_remark = "MMS (" . strtoupper($month) . ") | " . $lvl . " Level @ " . $bonus . " Received";
                        $this->give_cash_wallet_comm($member['referral_id'], 0, "mms", $mms_remark, $bonus);

                        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, package_id', array('id' => $member['referral_id']));
                        // if($is_record_exist == 0){
                            $lvl++;
                        // }
                        if($lvl > $max_lvl){
                            $is_got_downline = false;
                        }
                    }else{
                        $is_got_downline = false;
                    }
                }else{
                    $is_got_downline = false;
                }
            }
        }else{
            $is_got_downline = false;
        }
    }

    public function calculate_mms_bonus(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("m");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, min_mms_order", array('id' => $company_id, 'active' => 1));
        $company_min_mms_order = isset($company_info['id']) ? $company_info['min_mms_order'] : 0;

        $mms_qualified_list = $this->Api_Model->get_rows(TBL_MMS_REPORT, "*", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
        if(count($mms_qualified_list) > 0){
            $data['message'] = "You already release this month bonus !";
            $this->load->view("output/error_response", $data);
        }else{
            $member_list = $this->Api_Model->get_rows(TBL_USER, "id", array('active' => 1, 'company_id' => $company_id), "", "", "id", "DESC");
            foreach($member_list as $row_member){
                $mms_order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, SUM(total_quantity) as total_mms_quantity", array('user_id' => $row_member['id'], 'active' => 1, 'type' => "mms", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                $total_mms_order = isset($mms_order_info['id']) ? $mms_order_info['total_mms_quantity'] : 0;
                if($company_min_mms_order != 0){
                    // if fulfil mms requirement order
                    if($total_mms_order >= $company_min_mms_order){
                        $this->give_mms_bonus_post($row_member['id'], $company_id, $month);
                    }
                }
            }

            $mms_qualified_list = $this->Api_Model->get_rows(TBL_MMS_REPORT, "*", array('active' => 1, 'month' => $month, 'year' => date("Y"), 'company_id' => $company_id));
            if(empty($mms_qualified_list)){
                $data['message'] = "Empty Data !";
                $this->load->view("output/error_response", $data);
            }else{
                $this->load->view("output/success_response");
            }
        }
    }

    // mms code end

    // drb code start

    public function calculate_drb_bonus(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $is_record_exist = 0;

        $package_id_arr = $this->get_conditions_package_id_array($company_id, "drb");
        $max_package_id = implode("','", $package_id_arr);
        $member_list = $this->Api_Model->get_all_sql(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", "WHERE active = '1' AND company_id = '$company_id' AND package_id IN ('" . $max_package_id . "')");
        if(!empty($member_list)){
            foreach($member_list as $row_member){
                $total_quantity = $this->get_team_break_away_total_box($row_member['is_voucher'], $row_member['voucher_id'], $row_member['package_id'], $row_member['company_id'], $row_member['country_id']);
                $total_price = $this->get_selected_package_price($row_member['is_voucher'], $row_member['voucher_id'], $row_member['package_id'], $row_member['company_id'], $row_member['country_id']);
                if($row_member['is_voucher'] == 1){
                    $grand_total = $total_price;
                }else{
                    $grand_total = $total_quantity * $total_price;
                }
                // echo $grand_total; die;
                $bonus = $grand_total * 0.001;

                $drb_info = $this->Api_Model->get_rows_info(TBL_DRB_REPORT, "id", array('day' => date("d"), 'month' => date("m"), 'year' => date("Y"), 'user_id' => $row_member['id'], 'active' => 1));
                $is_record_exist = isset($drb_info['id']) ? 1 : 0;

                $data_drb = array(
                    'day' => date("d"),
                    'month' => date("m"),
                    'year' => date("Y"),
                    'company_id' => $company_id,
                    'user_id' => $row_member['id'],
                    'total_quantity' => $total_quantity,
                    'price' => $total_price,
                    'description' => "Daily Rebate",
                    'bonus' => $bonus
                );
                if($is_record_exist == 0){
                    $this->Api_Model->insert_data(TBL_DRB_REPORT, $data_drb);

                    $drb_remark = "Daily Rebate (" . date("d") . "-" . date("m") . "-" . date("Y") . ") | " . $bonus . " Received";
                    $this->give_cash_wallet_comm($row_member['id'], 0, "drb", $drb_remark, $bonus, 1);
                }
            }
        }

        if($is_record_exist == 1){
            $data['message'] = "Daily Rebate Given Today !";
            $this->load->view("output/error_response", $data);
        }else{
            $this->load->view("output/success_response");
        }
    }

    // drb code end

    public function check_is_smart_partner_available($referral_id, $debug = false){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_smart_partner", array('is_smart_partner' => 1, 'active' => 1));
        $total_smart_partner = isset($user_info['id']) ? $user_info['total_smart_partner'] : 0;

        $user_company_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $referral_id, 'active' => 1));
        $company_id = isset($user_company_info['id']) ? $user_company_info['company_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $first_smart_partner = $company_info['first_smart_partner'];

        if($total_smart_partner == $first_smart_partner || $total_smart_partner > $first_smart_partner){
            return false;
        }else{
            return true;
        }
    }

    // get the max package id
    public function get_conditions_package_id_array($company_id, $type){
        $package_id = array();

        $break_away_package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "id", array('company_id' => $company_id, 'active' => 1, $type => 1));
        if(!empty($break_away_package_list)){
            foreach($break_away_package_list as $row_break_away_package){
                $package_id[] = $row_break_away_package['id'];
            }
        }

        return $package_id;
    }

    // get the max package quantity
    public function get_conditions_package_quantity_array($user_id, $type, $debug = false){
        $total_quantity = 0;

        $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('active' => 1, 'id' => $user_id));
        $is_voucher = $member_info['is_voucher'];
        $voucher_id = $member_info['voucher_id'];
        $package_id = $member_info['package_id'];
        $company_id = $member_info['company_id'];
        $country_id = $member_info['country_id'];

        $package_id_arr = array();

        $break_away_package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "id", array('company_id' => $company_id, 'active' => 1, $type => 1));
        if(!empty($break_away_package_list)){
            foreach($break_away_package_list as $row_break_away_package){
                $package_id_arr[] = $row_break_away_package['id'];
            }
        }

        if(in_array($package_id, $package_id_arr)){
            $valid = true;
        }else{
            $valid = false;
        }

        if($valid){
            if($is_voucher == 1){
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                $total_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
            }else{
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
                $total_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
            }
        }else{
            $total_quantity = 0;
        }

        return $total_quantity;
    }

    public function check_is_package_max_package($member_package_id, $company_id, $country_id, $type){
        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, $type => 1));
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        if($member_package_id == $package_id){
            return true;
        }else{
            return false;
        }
    }

    public function get_purchase_display_data($referral_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, email", array('id' => $referral_id, 'active' => 1));
        $data = isset($user_info['id']) ? $user_info['username'] : $user_info['email'];
        return $data;
    }

    public function get_referral_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
        $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;
        return $referral_id;
    }

    public function get_company_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
        return $company_id;
    }

    public function get_company_type($company_id){
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, type", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";
        return $company_type;
    }

    public function get_purchase_quantity_total($month, $user_id, $package_id, $company_id){
        $total_box = 0;

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(amount) as total_quantity", array('user_id' => $user_id, 'package_id' => $package_id, 'company_id' => $company_id, 'MONTH(insert_time)' => $month, 'active' => 1));
        $total_stock = isset($purchase_package_info['id']) ? $purchase_package_info['total_quantity'] : 0;
        $total_box = $total_stock;

        return $total_box;
    }

    public function get_balance_purchase_quantity_total($month, $user_id, $is_paid = 0){
        $total_box = 0;

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(amount) as total_quantity", array('user_id' => $user_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'is_paid' => $is_paid));
        $total_stock = isset($purchase_package_info['id']) ? $purchase_package_info['total_quantity'] : 0;
        $total_box = $total_stock;

        return $total_box;
    }

    public function get_team_break_away_total_box($is_voucher, $voucher_id, $package_id, $company_id, $country_id, $debug = false){
        $total_box = 0;
        if($is_voucher == 1){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
            $total_stock = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
            $total_box = $total_stock;
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
            $total_stock = isset($package_info['id']) ? $package_info['quantity'] : 0;
            $total_box = $total_stock;
        }

        return $total_box;
    }

    public function get_selected_package_price($is_voucher, $voucher_id, $package_id, $company_id, $country_id, $debug = false){
        $total_box = 0;
        if($is_voucher == 1){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
            $total_price = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
            $total_price = isset($package_info['id']) ? $package_info['unit_price'] : 0;
        }

        return $total_price;
    }

    // ====================== above code all is verify status of break away requirement  ====================== //

    // check until the upline fulfil the requirement
    public function get_fulfil_upline_post($insert_id = 0, $type, $debug = false){
        $is_fulfil = false;
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $insert_id, 'active' => 1));
        while($is_fulfil === false){
            $company_id = $member['company_id'];
            $country_id = $member['country_id'];
            $package_id = $member['package_id'];

            $package_id_arr = $this->get_conditions_package_id_array($company_id, $type);

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, package_id', array('id' => $member['referral_id'], 'active' => 1));
            $referral_id = isset($referral_info['id']) ? $referral_info['id'] : 0;
            $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;

            if(in_array($referral_package_id, $package_id_arr)){
                $user_id = $referral_id;
                break;
            }else if($referral_id == 0){
                $user_id = 0;
                break;
            }else{
                $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $member['referral_id']));
                $is_fulfil = false;
            }
        }

        return $user_id;
    }

    public function give_cb_point($user_id, $description, $amount, $package_id = 0, $order_id = 0){
        $company_id = $this->get_company_id($user_id);
        $total_balance = $this->check_cb_point_balance_post($user_id);
        $new_balance = $total_balance + $amount;

        $data_point_insert = array(
            'company_id' => $company_id,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'order_id' => $order_id,
            'description' => $description,
            'credit' => $amount,
            'balance' => $new_balance,
        );

        $this->Api_Model->insert_data(TBL_CB_POINT, $data_point_insert);
    }

    public function give_cash_wallet_comm($referral_id, $user_id, $type, $description, $amount, $is_drb = 0){
        $company_id = $this->get_company_id($referral_id);
        $total_balance = $this->check_wallet_balance_post($type, $referral_id, $is_drb);
        $new_balance = $total_balance + $amount;

        $data_wallet_insert = array(
            'type' => $type,
            'company_id' => $company_id,
            'from_user_id' => $user_id,
            'to_user_id' => $referral_id,
            'description' => $description,
            'credit' => $amount,
            'balance' => $new_balance,
        );

        $wallet_id = $this->Api_Model->insert_data(TBL_WALLET, $data_wallet_insert);
        return $wallet_id;
    }

    public function deduct_drb($user_id, $amount){
        $company_id = $this->get_company_id($user_id);
        $total_balance = $this->check_drb_balance_post($user_id);
        $new_balance = $total_balance - $amount;

        $data_wallet_insert = array(
            'type' => "drb",
            'company_id' => $company_id,
            'from_user_id' => 0,
            'to_user_id' => $user_id,
            'description' => "Product Redemption",
            'debit' => $amount,
            'balance' => $new_balance,
        );

        $this->Api_Model->insert_data(TBL_WALLET, $data_wallet_insert);
    }

    public function check_is_got_cross_over_comm($referral_id){
        $result['data'] = array();
        $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id", array('to_user_id' => $referral_id, 'type' => "cross_over", 'active' => 1));
        if(isset($wallet_info['id']) && $wallet_info['id'] > 0){
            $status = true;
            $wallet_id = $wallet_info['id'];
        }else{
            $status = false;
            $wallet_id = 0;
        }

        $row['status'] = $status;
        $row['wallet_id'] = $wallet_id;

        $result['data'] = $row;

        return $result;
    }

    public function check_package_total_post($user_id = 0, $month){
        if($user_id == 0){
            $package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, SUM(amount) as total_quantity', array('active' => 1, 'is_company' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => date("Y")));
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, SUM(amount) as total_quantity', array('active' => 1, 'user_id' => $user_id, 'is_company' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => date("Y")));
        }
        $total_quantity = isset($package_info['id']) ? $package_info['total_quantity'] : 0;
        return $total_quantity;
    }

    public function check_drb_balance_post($user_id){
        $drb_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
        $total_credit = isset($drb_balance['total_credit']) ? $drb_balance['total_credit'] : 0;
        $total_debit = isset($drb_balance['total_debit']) ? $drb_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_stock_balance_post($user_id){
        $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
        $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_cb_point_balance_post($user_id){
        $point_balance = $this->Api_Model->get_rows_info(TBL_CB_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($point_balance['total_credit']) ? $point_balance['total_credit'] : 0;
        $total_debit = isset($point_balance['total_debit']) ? $point_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_point_balance_post($user_id){
        $point_balance = $this->Api_Model->get_rows_info(TBL_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($point_balance['total_credit']) ? $point_balance['total_credit'] : 0;
        $total_debit = isset($point_balance['total_debit']) ? $point_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_rb_balance_post($rb_voucher_id, $user_id){
        $rb_voucher_balance = $this->Api_Model->get_rows_info(TBL_RB_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id, 'rb_voucher_id' => $rb_voucher_id));
        $total_credit = isset($rb_voucher_balance['total_credit']) ? $rb_voucher_balance['total_credit'] : 0;
        $total_debit = isset($rb_voucher_balance['total_debit']) ? $rb_voucher_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_wallet_balance_post($type, $user_id, $is_drb = 0){
        if($is_drb == 1){
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
        }else{
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
        }
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, $type, $debug = false){
        $stock_balance = $this->check_stock_balance_post($user_id);
        if($type == "debit"){
            $new_balance = $stock_balance - $total_quantity;
        }else{
            $new_balance = $stock_balance + $total_quantity;
        }

        $data_stock = array(
            'user_id' => $user_id,
            'company_id' => $company_id,
            'package_id' => $purchase_id,
            'description' => "Repeat Order/Upgrade",
            'balance' => $new_balance
        );

        if($type == "debit"){
            $data_stock['debit'] = $total_quantity;
        }else{
            $data_stock['credit'] = $total_quantity;
        }
        $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
    }

    public function update_member_stock_post($available_stock_balance, $total_quantity, $user_id, $type, $is_self = false){
        if(!$is_self){
            if($type == "debit"){
                $new_stock_balance = $available_stock_balance - $total_quantity;
            }else{
                $new_stock_balance = $available_stock_balance + $total_quantity;
            }
        }else{
            $new_stock_balance = $available_stock_balance;
        }

        $data_user_update = array(
            'total_stock' => $new_stock_balance
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
    }

    public function proceed_point_post($user_id, $company_id, $purchase_id, $total_point, $type, $debug = false){
        $point_balance = $this->check_point_balance_post($user_id);
        if($type == "debit"){
            $new_balance = $point_balance - $total_point;
        }else{
            $new_balance = $point_balance + $total_point;
        }

        $data_point = array(
            'user_id' => $user_id,
            'company_id' => $company_id,
            'package_id' => $purchase_id,
            'description' => "Repeat Order/Upgrade",
            'balance' => $new_balance
        );

        if($type == "debit"){
            $data_point['debit'] = $total_point;
        }else{
            $data_point['credit'] = $total_point;
        }
        $this->Api_Model->insert_data(TBL_POINT, $data_point);
    }

    public function update_member_point_post($available_point_balance, $total_point, $user_id, $type, $is_self = false){
        if(!$is_self){
            if($type == "debit"){
                $new_point_balance = $available_point_balance - $total_point;
            }else{
                $new_point_balance = $available_point_balance + $total_point;
            }
        }else{
            $new_point_balance = $available_point_balance;
        }

        $data_user_update = array(
            'total_point' => $new_point_balance
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
    }

    public function update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $user_id){
        if($is_available_smart_partner && $is_already_become_smart_partner === false){
            $data_active_smart_partner_update = array(
                'is_smart_partner' => 1
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_active_smart_partner_update);
        }
    }

    // backend data

    public function get_order(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $status = isset($this->request_data['status']) ? $this->request_data['status'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($user_type == "ADMIN" && $group_id == 1){
            $where_query = array('active' => 1);
            if($status != ""){
                $where_query['status'] = $status;
            }
        }else if($user_type == "ADMIN" && $group_id != 1){
            $where_query = array('active' => 1, 'company_id' => $company_id);
            if($status != ""){
                $where_query['status'] = $status;
            }
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_ORDER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('product_name' => $search);
            $where_group_or_like_query = array('rndcode' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_ORDER, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }else{
            foreach ($order as $row) {
                if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " payment_status ".$row['dir'] : $order_query.", payment_status ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " status ".$row['dir'] : $order_query.", status ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " order_status ".$row['dir'] : $order_query.", order_status ".$row['dir'];
                }else if ($row['column'] == 5) {
                    $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $order_list = $this->Api_Model->get_datatables_list(TBL_ORDER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query);
        $counting = 0;

        foreach ($order_list as $row) {
            $user_id = ($row['user_id'] == 0) ? $row['referral_id'] : $row['user_id'];
            $purchase_type = ($row['user_id'] == 0) ? "Referral: " : "Agent: ";
            $agent_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname", array('id' => $user_id, 'active' => 1));
            if(isset($agent_info['id']) && $agent_info['id'] > 0){
                $username = $agent_info['username'];
                $fullname = $agent_info['fullname'];
                $type = $purchase_type;
            }else{
                $username = "";
                $fullname = "";
                $type = "";
            }

            $product_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('order_id' => $row['id'], 'active' => 1, 'user_id' => $row['user_id']));
            if(!empty($product_list)){
                foreach($product_list as $row_product){
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name, image", array('id' => $row_product['product_id'], 'active' => 1));
                    if(isset($product_info['id']) && $product_info['id'] > 0){
                        $grand_total = $row_product['subtotal'] + $row['delivery_fee'];
                        $row['product_name'] = "Order ID: #000" . $row['id'] . "<br>" . "<img width='80' src='" . DISPLAY_PATH . "img/product/" . $product_info['image'] . "'><br><span>" . $product_info['name'] . " x " . $row_product['quantity'] . "<br><br>" . "Total Price: " . $row_product['subtotal'] . "</span><br>" . "Delivery Fee: " . $row['delivery_fee'] . "<br>" . "Subtotal: " . number_format($grand_total, 2) . "<br>";
                    }else{
                        $row['product_name'] = "";
                    }
                }
            }else{
                $row['product_name'] = "";
            }

            $row['agent_name'] = $type . $fullname . " (" . $username . ")";
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));

            $payment_status = $row['payment_status'];
            if($payment_status == "UNPAID"){
                $row['payment_status'] = '<span class="badge bg-light-danger mb-1 mr-2">' . $payment_status . '</span>';
            }else{
                $row['payment_status'] = '<span class="badge bg-light-success mb-1 mr-2">' . $payment_status . '</span>&nbsp;<a href="#" onclick="show_order_receipt(' . $row['id'] . '); return false;"><u>View</u></a>';
            }

            $btn = "";

            $order_status = $row['status'];
            if($order_status == "PENDING"){
                $row['status'] = '<span class="badge bg-light-warning mb-1 mr-2">' . $order_status . '</span>';
                $btn .= "<a href='#' class='btn-sm btn-success' style='border:none;' onclick='approve_order(" . $row['id'] . ")'>" . "Approve" . "</a> ";
            }else if($order_status == "APPROVE"){
                $row['status'] = '<span class="badge bg-light-success mb-1 mr-2">' . $order_status . '</span>';
            }else if($order_status == "REJECT"){
                $row['status'] = '<span class="badge bg-light-danger mb-1 mr-2">' . $order_status . '</span>';
            }else{
                $row['status'] = '<span class="badge bg-light-danger mb-1 mr-2">' . $order_status . '</span>';
            }

            $shipment_status = $row['order_status'];
            if($shipment_status == "PLACED"){
                $row['order_status'] = '<span class="badge bg-light-warning mb-1 mr-2">' . "NOT SHIPPED" . '</span>';
            }else{
                $row['order_status'] = '<span class="badge bg-light-success mb-1 mr-2">' . "SHIPPED" . '</span>';
            }

            if($user_type == "ADMIN"){
                $btn .= "<a href='#' class='btn-sm btn-danger' onclick='update_order_status(" . $row['id'] . ")' style='border:none;'>" . "Update" . "</a> ";
            }

            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_shipment(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $status = isset($this->request_data['status']) ? $this->request_data['status'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($user_type == "ADMIN" && $group_id == 1){
            $where_query = array('active' => 1);
            if($status != ""){
                $where_query['order_status'] = $status;
            }
        }else if($user_type == "ADMIN" && $group_id != 1){
            $where_query = array('active' => 1, 'company_id' => $company_id);
            if($status != ""){
                $where_query['order_status'] = $status;
            }
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_ORDER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('product_name' => $search);
            $where_group_or_like_query = array('rndcode' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_ORDER, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }else{
            foreach ($order as $row) {
                if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " order_status ".$row['dir'] : $order_query.", order_status ".$row['dir'];
                }else if ($row['column'] == 5) {
                    $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $order_list = $this->Api_Model->get_datatables_list(TBL_ORDER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query);
        $counting = 0;

        foreach ($order_list as $row) {
            $shipping_name = $row['s_name'];
            $shipping_contact = $row['s_contact'];
            $shipping_email = $row['s_email'];
            $shipping_address = $row['s_address'];
            $shipping_city = $row['s_city'];
            $shipping_postcode = $row['s_postcode'];
            $shipping_state = $row['s_state'];
            $shipping_country = $row['s_country'];
            $shipping_remark = $row['s_remark'];
            $delivery_company = ($row['delivery_company'] != "" && $row['delivery_company'] != NULL) ? $row['delivery_company'] : "-";
            $tracking_no = ($row['tracking_no'] != "" && $row['tracking_no'] != NULL) ? $row['tracking_no'] : "-";
            $tracking_url = ($row['tracking_url'] != "" && $row['tracking_url'] != NULL) ? $row['tracking_url'] : "-";

            $agent_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, referral_id", array('id' => $row['user_id'], 'active' => 1));
            if(isset($agent_info['id']) && $agent_info['id'] > 0){
                $username = $agent_info['username'];
                $fullname = $agent_info['fullname'];
                $referral_id = $agent_info['referral_id'];
            }else{
                $username = "";
                $fullname = "";
                $referral_id = 0;
            }

            $payment_status = $row['payment_status'];
            if($payment_status == "UNPAID"){
                $payment_badges = '<span class="badge bg-light-danger mb-1 mr-2">' . $payment_status . '</span>';
            }else{
                $payment_badges = '<span class="badge bg-light-success mb-1 mr-2">' . $payment_status . '</span>&nbsp;<a href="#" onclick="show_order_receipt(' . $row['id'] . '); return false;"><u>View</u></a>';
            }

            $product_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('order_id' => $row['id'], 'active' => 1, 'user_id' => $row['user_id']));
            if(!empty($product_list)){
                foreach($product_list as $row_product){
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name, image", array('id' => $row_product['product_id'], 'active' => 1));
                    if(isset($product_info['id']) && $product_info['id'] > 0){
                        $row['product_name'] = "Order ID: #000" . $row['id'] . "<br>" . "<img width='80' src='" . DISPLAY_PATH . "img/product/" . $product_info['image'] . "'><br><span>" . $product_info['name'] . " x " . $row_product['quantity'] . "</span><br><br>" . $payment_badges . "<br>";
                    }else{
                        $row['product_name'] = "";
                    }
                }
            }else{
                $row['product_name'] = "";
            }

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname", array('id' => $referral_id, 'active' => 1));
            if(isset($referral_info['id']) && $referral_info['id'] > 0){
                $referral_username = $referral_info['username'];
                $referral_fullname = $referral_info['fullname'];
            }else{
                $referral_username = "";
                $referral_fullname = "";
            }

            if($referral_username == "" || $referral_fullname == ""){
                $row['upline_name'] = "";
            }else{
                $row['upline_name'] = $referral_fullname . " (" . $referral_username . ")";
            }
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));

            $row['shipment_detail'] = "Name: " . $shipping_name . "<br>"
            . "Contact: " . $shipping_contact . "<br>"
            . "Email: " . $shipping_email . "<br>"
            . "Address: " . $shipping_address . "<br>"
            . "City: " . $shipping_city . "<br>"
            . "State: " . $shipping_state . "<br>"
            . "Postcode: " . $shipping_postcode . "<br>"
            . "To: " . $shipping_country . "<br>"
            . "Remark: " . $shipping_remark . "<br>";

            $row['tracking_detail'] = "Delivery Company: " . $delivery_company . "<br>"
            . "Tracking No: " . $tracking_no . "<br>"
            . "Tracking Url: " . $tracking_url . "<br>";

            $btn = "";

            $shipment_status = $row['order_status'];
            if($shipment_status == "PLACED"){
                $row['order_status'] = '<span class="badge bg-light-warning mb-1 mr-2">' . "NOT SHIPPED" . '</span>';
            }else{
                $row['order_status'] = '<span class="badge bg-light-success mb-1 mr-2">' . "SHIPPED" . '</span>';
            }

            if($user_type == "ADMIN"){
                $btn .= "<a href='#' class='btn-sm btn-danger' onclick='update_order_status(" . $row['id'] . ")' style='border:none;'>" . "Update" . "</a> ";
            }

            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function generate($order_id)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $this->page_data['order_info'] = $this->Api_Model->get_info_sql(TBL_ORDER, "*", "WHERE id = '$order_id'");
        $this->page_data['order_list'] = $this->Api_Model->get_all_sql(TBL_ORDER_DETAIL, "*", "WHERE order_id = '$order_id'");
        $html = $this->load->view('main/order_invoice',$this->page_data,true);
        $mpdf->WriteHTML($html);
        $mpdf->Output(); // opens in browser
    }

    public function get_shipment_status(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, delivery_company, tracking_no, tracking_url", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $data = array(
                'delivery_company' => $order_info['delivery_company'],
                'tracking_no' => $order_info['tracking_no'],
                'tracking_url' => $order_info['tracking_url']
            );

            $json['response_data'] = $data;
            $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Order !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function update_order_status(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;
        $delivery_company = isset($this->request_data['delivery_company']) ? $this->request_data['delivery_company'] : "";
        $tracking_no = isset($this->request_data['tracking_no']) ? $this->request_data['tracking_no'] : "";
        $tracking_url = isset($this->request_data['tracking_url']) ? $this->request_data['tracking_url'] : "";

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $data = array(
                'status' => "APPROVE",
                'delivery_company' => $delivery_company,
                'tracking_no' => $tracking_no,
                'tracking_url' => $tracking_url
            );
            if($delivery_company != "" && $tracking_no != "" && $tracking_url != ""){
                $data['order_status'] = "SHIPPED";
            }
            $this->Api_Model->update_data(TBL_ORDER, array('id' => $order_id, 'active' => 1), $data);

            $json['response_data'] = $data;
            $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Order !";
            $this->load->view("output/error_response", $data);
        }
    }
}
