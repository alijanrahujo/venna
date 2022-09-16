<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forms extends Base_Controller {
    public $_api_code = '1111';
	public function __construct() {
        parent::__construct();
    }

    public function add(){
        $this->load->view("include/header_normal");
        $this->load->view(ADMIN_URL . "/add_data");
        $this->load->view("include/footer_normal");
    }

    public function get_package(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;

        if($country_id == 0){
            $data['message'] = "Country is empty !";
			$this->load->view("output/error_response", $data);
        }else{
            $package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
            
            $json['response_data'] = $package_list;
            $this->load->view("output/success_response", $json);
        }
    }

    public function insert_data(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $fullname = isset($this->request_data['fullname']) ? $this->request_data['fullname'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $ic_no = isset($this->request_data['ic_no']) ? $this->request_data['ic_no'] : "";
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $ori_password = isset($this->request_data['password']) ? $this->request_data['password'] : "123456";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "123456";
        $ori_pincode = isset($this->request_data['pincode']) ? $this->request_data['pincode'] : "123456";
        $cfm_pincode = isset($this->request_data['cfm_pincode']) ? $this->request_data['cfm_pincode'] : "123456";
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";
        $total_stock = isset($this->request_data['total_stock']) ? $this->request_data['total_stock'] : "0.00";
        $cb_point = isset($this->request_data['cb_point']) ? $this->request_data['cb_point'] : "0.00";
        $drb = isset($this->request_data['drb']) ? $this->request_data['drb'] : "0.00";
        $balance_commision = isset($this->request_data['balance_commision']) ? $this->request_data['balance_commision'] : "0.00";
        $rb_package_id_arr = isset($this->request_data['rb_package_id']) ? $this->request_data['rb_package_id'] : [];
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $voucher_package_id_arr = isset($this->request_data['voucher_package_id']) ? $this->request_data['voucher_package_id'] : [];

        $is_voucher = isset($this->request_data['is_voucher']) ? $this->request_data['is_voucher'] : 0;
        $voucher_id = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : 0;

        $referral_code = $this->generate_referral_code(9);
        $password = password_hash($ori_password, PASSWORD_BCRYPT);
        $pincode = password_hash($ori_pincode, PASSWORD_BCRYPT);

        if($company_id == 0){
            $data['message'] = "Brand Not Found !";
			$this->load->view("output/error_response", $data);
        }else if($country_id == 0){
            $data['message'] = "Country Not Found !";
			$this->load->view("output/error_response", $data);
        }else if($package_id == 0){
            $data['message'] = "Package Not Found !";
			$this->load->view("output/error_response", $data);
        }else{
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            if(isset($company_info['id']) && $company_info['id'] > 0){
                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('email' => $email, 'active' => 1));
                
                if(isset($user_info['id']) && $user_info['id'] > 0){
                    $data['message'] = "Email already exist !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $dob = $this->get_birthday($ic_no);
                    $age = $this->calculate_age($dob);

                    $data_insert = array(
                        'company_id' => $company_id,
                        'user_type' => "AGENT",
                        'package_id' => $package_id,
                        'referral_id' => $referral_id,
                        'referral_code' => $referral_code,
                        'username' => $username,
                        'password' => $password,
                        'pincode' => $pincode,
                        'fullname' => $fullname,
                        'phone_no' => $phone_no,
                        'ic' => $ic_no,
                        'address_line1' => $address,
                        'city' => $city,
                        'state' => $state,
                        'country_id' => $country_id,
                        'postcode' => $postcode,
                        'email' => $email,
                        'bank_name' => $bank_name,
                        'account_name' => $account_name,
                        'account_no' => $account_no,
                        'total_stock' => $total_stock,
                        'is_voucher' => $is_voucher,
                        'voucher_id' => $voucher_id,
                        'is_step1' => 1,
                        'is_step3' => 1,
                        'is_step4' => 1
                    );
                    if($ic_no != ""){
                        $data_insert['dob'] = $dob;
                        $data_insert['age'] = $age;
                    }
                    $data_insert['is_old'] = 1;
                    $user_id = $this->Api_Model->insert_data(TBL_USER, $data_insert);

                    if($voucher_id != 0){
                        $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id, 'active' => 1));
                        $current_voucher_quantity = $voucher_info['balance_quantity'];

                        if($current_voucher_quantity < 1){
                            $is_able_to_continue = false;
                        }else{
                            $is_able_to_continue = true;
                        }
                    }else{
                        $is_able_to_continue = true;
                    }

                    if($is_able_to_continue){
                        if($voucher_id != 0){
                            $new_quantity = $current_voucher_quantity - 1;
                            $data_voucher = array(
                                'balance_quantity' => $new_quantity
                            );
                            $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'active' => 1), $data_voucher);
                        }

                        if(!empty($voucher_package_id_arr) && $voucher_package_id_arr[0] != 0){
                            foreach($voucher_package_id_arr as $vkey => $voucher_package_id){
                                $voucher_quantity = isset($attr[$vkey]['voucher_package_quantity']) ? $attr[$vkey]['voucher_package_quantity'] : 0;

                                $data_extra_voucher = array(
                                    'user_id' => $user_id,
                                    'package_id' => $voucher_package_id,
                                    'quantity' => $voucher_quantity
                                );
                                $this->Api_Model->insert_data(TBL_USER_BIG_PRESENT_FREE, $data_extra_voucher);
                            }
                        }

                        $data_address = array(
                            'user_id' => $user_id,
                            'name' => $address,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'postcode' => $postcode
                        );
                        if($address != "" || $city != "" || $state != "" || $postcode != ""){
                            $this->Api_Model->insert_data(TBL_USER_ADDRESS, $data_address);
                        }

                        // insert package record to agent account
                        if($voucher_id != 0){
                            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id, 'active' => 1));
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, is_company", array('id' => $package_id, 'active' => 1));
                            $total_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                            $is_paid_to_company = $package_info['is_company'];
                        }else{
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
                            $total_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
                            $is_paid_to_company = $package_info['is_company'];
                        }
                        $data_package = array(
                            'user_id' => $user_id,
                            'referral_id' => $referral_id,
                            'company_id' => $company_id,
                            'package_id' => $package_id,
                            'amount' => $total_quantity,
                            'is_company' => $is_paid_to_company,
                            'is_paid' => 1,
                            'status' => "APPROVE"
                        );
                        if($voucher_id != 0){
                            $data_package['is_voucher'] = 1;
                        }
                        $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_package);
                        
                        if($total_stock != "0.00" && $total_stock != ""){
                            $data_stock = array(
                                'user_id' => $user_id,
                                'company_id' => $company_id,
                                'package_id' => $package_id,
                                'description' => "Transfer From Old System",
                                'credit' => $total_stock,
                                'balance' => $total_stock
                            );
                            $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
                        }

                        if($cb_point != "0.00" && $cb_point != ""){
                            $data_cb_point = array(
                                'company_id' => $company_id,
                                'user_id' => $user_id,
                                'package_id' => 0,
                                'order_id' => 0,
                                'description' => "Transfer From Old System",
                                'credit' => $cb_point,
                                'balance' => $cb_point,
                            );
                            $this->Api_Model->insert_data(TBL_CB_POINT, $data_cb_point);
                        }

                        if($drb != "0.00" && $drb != ""){
                            $data_drb = array(
                                'day' => date("d"),
                                'month' => date("m"),
                                'year' => date("Y"),
                                'company_id' => $company_id,
                                'user_id' => $user_id,
                                'description' => "Transfer From Old System",
                                'bonus' => $drb
                            );
                            $this->Api_Model->insert_data(TBL_DRB_REPORT, $data_drb);

                            $total_drb_balance = $this->check_wallet_balance_post("drb", $user_id, 1);
                            $new_drb_balance = $total_drb_balance + $drb;
                            $data_drb_wallet = array(
                                'type' => "drb",
                                'company_id' => $company_id,
                                'from_user_id' => 0,
                                'to_user_id' => $user_id,
                                'description' => "Transfer From Old System",
                                'credit' => $drb,
                                'balance' => $new_drb_balance,
                                'is_released' => 1
                            );
                            if($drb != "0.00"){
                                $this->Api_Model->insert_data(TBL_WALLET, $data_drb_wallet);
                            }
                        }

                        if($balance_commision != "0.00" && $balance_commision != ""){
                            $wallet_balance = $this->check_wallet_balance_post("break_away", $user_id);
                            $new_wallet_balance = $wallet_balance + $balance_commision;
                            $data_comm_wallet = array(
                                'type' => "break_away",
                                'company_id' => $company_id,
                                'from_user_id' => 0,
                                'to_user_id' => $user_id,
                                'description' => "Transfer From Old System",
                                'credit' => $balance_commision,
                                'balance' => $new_wallet_balance,
                                'is_released' => 1
                            );

                            $this->Api_Model->insert_data(TBL_WALLET, $data_comm_wallet);
                        }

                        if(!empty($rb_package_id_arr) && $rb_package_id_arr[0] != 0){
                            foreach($rb_package_id_arr as $rkey => $rb_package_id){
                                $rb_quantity = isset($attr[$rkey]['rb_quantity']) ? $attr[$rkey]['rb_quantity'] : 0;
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $rb_package_id, 'active' => 1));
                                $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                                $package_name = isset($package_info['id']) ? $package_info['name'] : "";

                                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                                $rb_voucher_percentage = $company_info['rb_voucher_qty'];
                                $rb_voucher_value = $company_info['rb_voucher_value'];

                                if($rb_voucher_percentage != 0){
                                    $is_active_rb = true;
                                }else{
                                    $is_active_rb = false;
                                }

                                $rb_voucher_convert_qty = $rb_voucher_percentage / 100;
                                $rb_voucher_convert_value = $rb_voucher_value / 100;
                                $rb_value_price = $package_price * $rb_voucher_convert_value;
                                $rb_actual_price = $package_price - $rb_value_price;

                                if($is_active_rb){
                                    $data_rb_voucher = array(
                                        'company_id' => $company_id,
                                        'user_id' => $user_id,
                                        'package_id' => $rb_package_id,
                                        'cost_price' => $package_price,
                                        'quantity' => $rb_quantity,
                                        'value_price' => $rb_value_price,
                                        'actual_price' => $rb_actual_price
                                    );
                                    $rb_voucher_id = $this->Api_Model->insert_data(TBL_RB_VOUCHER, $data_rb_voucher);

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
                        }
            
                        $json['response_data'] = $data_insert;
                        $this->load->view("output/success_response", $json);
                    }else{
                        $data['message'] = "Insufficient Voucher Quantity !";
                        $this->load->view("output/error_response", $data);
                    }
                }
            }else{
                $data['message'] = "Brand Not Found !";
			    $this->load->view("output/error_response", $data);
            }
        }
    }

    public function calculate_age($dob){
        $diff = (date('Y') - date('Y',strtotime($dob)));
        return $diff;
    }

    function get_birthday($ic) {
        if(empty($ic)) return null;
        $current_year = date("y");
        $bir_year = substr($ic, 0, 2);
        if($bir_year <= 99 && $bir_year >= $current_year){
            $front_year = "19";
        }else{
            $front_year = "20";
        }
        $bir = substr($ic, 0, 6);
        $year = (int) substr($bir, 0, 2);
        $month = (int) substr($bir, 2, 2);
        if($month < 10){
            $month = "0" . $month;
        }else{
            $month = $month;
        }
        $day = (int) substr($bir, 4, 4);
        if($day < 10){
            $day = "0" . $day;
        }else{
            $day = $day;
        }
        return $front_year . $year . "-" . $month . "-" . $day;
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

    public function check_rb_balance_post($rb_voucher_id, $user_id){
        $rb_voucher_balance = $this->Api_Model->get_rows_info(TBL_RB_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id, 'rb_voucher_id' => $rb_voucher_id));
        $total_credit = isset($rb_voucher_balance['total_credit']) ? $rb_voucher_balance['total_credit'] : 0;
        $total_debit = isset($rb_voucher_balance['total_debit']) ? $rb_voucher_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function generate_referral_code($length){
        $prefix = generateOneLetter();
        $user_id = $prefix . substr(str_shuffle('0123456789'),1,$length);
        return $user_id;
    }
}
