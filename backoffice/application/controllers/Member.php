<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'vendor/autoload.php';

class Member extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/member_list");
    }

    public function promotion(){
        $this->load(ADMIN_URL . "/promotion_member_list");
    }
    
    public function gold(){
        $this->load(ADMIN_URL . "/gold_promotion_member_list");
    }

    public function cwallet(){
        $this->load(ADMIN_URL . "/add_cash_wallet");
    }

    public function cb(){
        $this->load(ADMIN_URL . "/add_cb_point");
    }

    public function drb(){
        $this->load(ADMIN_URL . "/add_drb");
    }

    public function promotione(){
        $id = $this->user_profile_info['company_id'];
        $this->page_data['user_id'] = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $this->page_data['voucher'] = $this->Api_Model->get_all_sql(TBL_PACKAGE_VOUCHER, "*", "WHERE company_id = '$id' AND active = '1' GROUP BY product_id");
        $this->load(ADMIN_URL . "/edit_promotion_voucher", $this->page_data);
    }

    public function golde(){
        $id = $this->user_profile_info['company_id'];
        $this->page_data['user_id'] = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $this->page_data['package'] = $this->Api_Model->get_all_sql(TBL_PACKAGE, "*", "WHERE company_id = '$id' AND active = '1'");
        $this->load(ADMIN_URL . "/edit_member_gold_voucher", $this->page_data);
    }

    public function insert_cash_wallet(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $amount = isset($this->request_data['amount']) ? $this->request_data['amount'] : "0.00";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $username, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_id = isset($user_info['id']) ? $user_info['id'] : 0;

            $total_balance = $this->check_current_balance_post($user_id);
            $new_balance = $total_balance + $amount;
            
            $data = array(
                'type' => "break_away",
                'company_id' => $company_id,
                'to_user_id' => $user_id,
                'description' => "Transfer From Old System",
                'credit' => $amount,
                'balance' => $new_balance,
                'is_released' => 1
            );
            $this->Api_Model->insert_data(TBL_WALLET, $data);
    
            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Invalid Username !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function insert_cb_point(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $amount = isset($this->request_data['amount']) ? $this->request_data['amount'] : "0.00";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $username, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_id = isset($user_info['id']) ? $user_info['id'] : 0;

            $total_balance = $this->check_cb_point_balance_post($user_id);
            $new_balance = $total_balance + $amount;
            
            $data = array(
                'company_id' => $company_id,
                'user_id' => $user_id,
                'description' => "Transfer From Old System",
                'credit' => $amount,
                'balance' => $new_balance
            );
            $this->Api_Model->insert_data(TBL_CB_POINT, $data);
    
            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Invalid Username !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function insert_drb(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $amount = isset($this->request_data['amount']) ? $this->request_data['amount'] : "0.00";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $username, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_id = isset($user_info['id']) ? $user_info['id'] : 0;
            if($amount != "0.00" && $amount != ""){
                $data_drb = array(
                    'day' => date("d"),
                    'month' => date("m"),
                    'year' => date("Y"),
                    'company_id' => $company_id,
                    'user_id' => $user_id,
                    'description' => "Transfer From Old System",
                    'bonus' => $amount
                );
                $this->Api_Model->insert_data(TBL_DRB_REPORT, $data_drb);

                $total_drb_balance = $this->check_wallet_balance_post($user_id, 1, 1);
                $new_drb_balance = $total_drb_balance + $amount;
                $data_drb_wallet = array(
                    'type' => "drb",
                    'company_id' => $company_id,
                    'from_user_id' => 0,
                    'to_user_id' => $user_id,
                    'description' => "Transfer From Old System",
                    'credit' => $amount,
                    'balance' => $new_drb_balance,
                    'is_released' => 1
                );
                if($amount != "0.00"){
                    $this->Api_Model->insert_data(TBL_WALLET, $data_drb_wallet);
                }

                $this->load->view("output/success_response");
            }else{
                $data['message'] = "Please enter drb amount !";
                $this->load->view("output/error_response", $data);
            }
        }else{
            $data['message'] = "Invalid Username !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function check_cb_point_balance_post($user_id){
        $point_balance = $this->Api_Model->get_rows_info(TBL_CB_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($point_balance['total_credit']) ? $point_balance['total_credit'] : 0;
        $total_debit = isset($point_balance['total_debit']) ? $point_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_current_balance_post($user_id){
        $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function insert_gold_voucher(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $package_id_arr = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : [];

        $is_empty_quantity = false;
        $is_empty_total_stock = false;
        $is_empty_price = false;
        if(!empty($package_id_arr)){
            foreach($package_id_arr as $package_id){
                $quantity = isset($attr[$package_id]['quantity']) ? $attr[$package_id]['quantity'] : 0;
                $total_stock = isset($attr[$package_id]['total_stock']) ? $attr[$package_id]['total_stock'] : 0;
                $package_price = isset($attr[$package_id]['price']) ? $attr[$package_id]['price'] : 0;

                if($total_stock == 0){
                    $is_empty_total_stock = true;
                    $data_voucher_arr[] = array(
                        'package_id' => $package_id,
                        'quantity' => $quantity,
                        'total_stock' => $total_stock,
                        'package_price' => $package_price
                    );
                }else if($quantity == 0){
                    $is_empty_quantity = true;
                    $data_voucher_arr[] = array(
                        'package_id' => $package_id,
                        'quantity' => $quantity,
                        'total_stock' => $total_stock,
                        'package_price' => $package_price
                    );
                }else if($package_price == 0){
                    $is_empty_price = true;
                    $data_voucher_arr[] = array(
                        'package_id' => $package_id,
                        'quantity' => $quantity,
                        'total_stock' => $total_stock,
                        'package_price' => $package_price
                    );
                }else{
                    $data_voucher_arr[] = array(
                        'package_id' => $package_id,
                        'quantity' => $quantity,
                        'total_stock' => $total_stock,
                        'package_price' => $package_price
                    );
                }
            }
        }else{
            $data_voucher_arr = array();
        }

        if(empty($data_voucher_arr)){
            $data['message'] = "Empty Voucher !";
            $this->load->view("output/error_response", $data);
        }else{
            if($is_empty_total_stock){
                $data['message'] = "Empty Voucher Total Stock !";
                $this->load->view("output/error_response", $data);
            }else if($is_empty_quantity){
                $data['message'] = "Empty Voucher Quantity !";
                $this->load->view("output/error_response", $data);
            }else if($is_empty_price){
                $data['message'] = "Empty Voucher Price !";
                $this->load->view("output/error_response", $data);
            }else{
                foreach($data_voucher_arr as $row_voucher){
                    $voucher_code = $this->generate_voucher_code(8) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(10);

                    if($row_voucher['total_stock'] == 0){
                        $is_empty_total_stock = true;
                    }else if($row_voucher['quantity'] == 0){
                        $is_empty_quantity = true;
                    }else if($row_voucher['package_price'] == 0){
                        $is_empty_price = true;
                    }

                    if($is_empty_quantity || $is_empty_total_stock || $is_empty_price){
                        $data_voucher = array();
                    }else{
                        $user_big_present_info = $this->Api_Model->get_rows_info(TBL_USER_BIG_PRESENT_FREE, "id, quantity", array('package_id' => $row_voucher['package_id'], 'user_id' => $user_id, 'active' => 1));
                        $exist_voucher_id = isset($user_big_present_info['id']) ? $user_big_present_info['id'] : 0;
                        $is_voucher_data_exist = isset($user_big_present_info['id']) ? 1 : 0;
                        $current_balance_quantity = isset($user_big_present_info['id']) ? $user_big_present_info['quantity'] : 0;
                        $new_balance_quantity = $current_balance_quantity + $row_voucher['quantity'];
                        $data_voucher = array(
                            'user_id' => $user_id,
                            'package_id' => $row_voucher['package_id'],
                            'quantity' => $row_voucher['quantity'],
                            'total_stock' => $row_voucher['total_stock']
                        );

                        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, country_id, company_id", array('id' => $user_id, 'active' => 1));
                        $country_id = isset($user_info['id']) ? $user_info['country_id'] : 0;
                        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

                        if($row_voucher['total_stock'] == 60 && $company_id == 2){
                        }else{
                            if($is_voucher_data_exist == 1){
                                $this->Api_Model->update_data(TBL_USER_BIG_PRESENT_FREE, array('id' => $exist_voucher_id), array('quantity' => $new_balance_quantity));
                            }else{
                                $this->Api_Model->insert_data(TBL_USER_BIG_PRESENT_FREE, $data_voucher);
                            }
                        }

                        if($row_voucher['total_stock'] == 60 && $company_id == 2){
                            $data_voucher_log = array(
                                'user_id' => $user_id,
                                'country_id' => $country_id,
                                'package_id' => $row_voucher['package_id']
                            );

                            $data_big_present = array(
                                'type' => 2,
                                'user_id' => $user_id,
                                'country_id' => $country_id,
                                'company_id' => $company_id,
                                'package_id' => $row_voucher['package_id'],
                                'code' => $voucher_code,
                                'quantity' => 1,
                                'balance_quantity' => 1,
                                'price' => $row_voucher['package_price'],
                                'total_stock' => $row_voucher['total_stock'],
                                'status' => "APPROVE"
                            );
                            $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);

                            $data_voucher_log['voucher_id'] = $insert_id;
                            $this->Api_Model->insert_data(TBL_VOUCHER_LOG, $data_voucher_log);
                        }
                    }
                }
                $encrypt_user_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($user_id));
                $json['response_data'] = $encrypt_user_id;
                $this->load->view("output/success_response", $json);
            }
        }
    }

    public function generate_voucher_code($len){
        $str = 'abcdef0123456789';
        $voucher_code = "";
        for($i=0;$i<$len;$i++){
            $voucher_code.=substr($str, rand(0, strlen($str)), 1);   
        }
        return $voucher_code;
    }

    public function insert_promotion_voucher(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $product_id_arr = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : [];

        $is_empty_quantity = false;
        $is_empty_price = false;
        if(!empty($product_id_arr)){
            foreach($product_id_arr as $product_id){
                $quantity = isset($attr[$product_id]['quantity']) ? $attr[$product_id]['quantity'] : 0;
                $price = isset($attr[$product_id]['price']) ? $attr[$product_id]['price'] : 0;

                if($price == 0){
                    $is_empty_price = true;
                    $data_voucher_package_arr[] = array(
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'price' => $price
                    );
                }else if($quantity == 0){
                    $is_empty_quantity = true;
                    $data_voucher_package_arr[] = array(
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'price' => $price
                    );
                }else{
                    $data_voucher_package_arr[] = array(
                        'product_id' => $product_id,
                        'quantity' => $quantity,
                        'price' => $price
                    );
                }
            }
        }else{
            $data_voucher_package_arr = array();
        }

        if(empty($data_voucher_package_arr)){
            $data['message'] = "Empty Selected Voucher !";
            $this->load->view("output/error_response", $data);
        }else{
            if($is_empty_price){
                $data['message'] = "Empty Package Price !";
                $this->load->view("output/error_response", $data);
            }else if($is_empty_quantity){
                $data['message'] = "Empty Package Quantity !";
                $this->load->view("output/error_response", $data);
            }else{
                foreach($data_voucher_package_arr as $row_voucher_package){
                    if($row_voucher_package['price'] == 0){
                        $is_empty_price = true;
                    }else if($row_voucher_package['quantity'] == 0){
                        $is_empty_quantity = true;
                    }

                    if($is_empty_quantity || $is_empty_price){
                        $data_voucher_package = array();
                    }else{
                        $data_voucher_package = array(
                            'user_id' => $user_id,
                            'product_id' => $row_voucher_package['product_id'],
                            'quantity' => $row_voucher_package['quantity'],
                            'price' => $row_voucher_package['price'],
                            'is_system_adjust' => 1
                        );

                        $this->Api_Model->insert_data(TBL_USER_VOUCHER, $data_voucher_package);
                    }
                }
                $this->load->view("output/success_response");
            }
        }
    }

    public function update_promotion_voucher(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $user_voucher_id_arr = isset($this->request_data['user_voucher_id']) ? $this->request_data['user_voucher_id'] : [];

        if(!empty($user_voucher_id_arr)){
            foreach($user_voucher_id_arr as $user_voucher_id){
                $voucher_quantity = isset($attr[$user_voucher_id]['quantity']) ? $attr[$user_voucher_id]['quantity'] : 0;
                $voucher_status = isset($attr[$user_voucher_id]['active']) ? $attr[$user_voucher_id]['active'] : 0;
                $voucher_user_id = isset($attr[$user_voucher_id]['user_id']) ? $attr[$user_voucher_id]['user_id'] : 0;

                $data = array(
                    'quantity' => $voucher_quantity,
                    'active' => $voucher_status
                );
                $this->Api_Model->update_data(TBL_USER_VOUCHER, array('id' => $user_voucher_id, 'user_id' => $user_id), $data);
            }
        }

        $this->load->view("output/success_response");
    }

    public function update_gold_voucher(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $user_voucher_id_arr = isset($this->request_data['user_voucher_id']) ? $this->request_data['user_voucher_id'] : [];

        if(!empty($user_voucher_id_arr)){
            foreach($user_voucher_id_arr as $user_voucher_id){
                $voucher_quantity = isset($attr[$user_voucher_id]['quantity']) ? $attr[$user_voucher_id]['quantity'] : 0;
                $voucher_status = isset($attr[$user_voucher_id]['active']) ? $attr[$user_voucher_id]['active'] : 0;
                $voucher_user_id = isset($attr[$user_voucher_id]['user_id']) ? $attr[$user_voucher_id]['user_id'] : 0;

                $data = array(
                    'quantity' => $voucher_quantity,
                    'active' => $voucher_status
                );
                $this->Api_Model->update_data(TBL_USER_BIG_PRESENT_FREE, array('id' => $user_voucher_id, 'user_id' => $user_id), $data);
            }
        }

        $this->load->view("output/success_response");
    }

    public function upline(){
        $this->load(ADMIN_URL . "/edit_member_upline");
    }

    public function add(){
        $this->page_data['company'] = $this->Api_Model->get_rows(TBL_COMPANY, "*", array('active' => 1));
        $this->load(ADMIN_URL . "/add_member", $this->page_data);
    }

    public function check_upline_username(){
        $upline_id = isset($this->request_data['upline_id']) ? $this->request_data['upline_id'] : 0;
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $upline_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($user_info['id']));
        }else{
            $id = 0;
        }

        $json['response_data'] = $id;
        $this->load->view("output/success_response", $json);
    }

    public function view(){
        $company_id = $this->user_profile_info['company_id'];
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $total_organization = $this->get_organization($id);
        $stock_balance = $this->check_stock_balance_post($id);
        $total_wallet = $this->check_wallet_balance_post($id);
        $point_balance = $this->check_point_balance_post($id);
        $edit_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $id, 'active' => 1));
        $this->page_data['member'] = $this->Api_Model->get_rows(TBL_USER, "*", array('user_type' => "AGENT" ,'active' => 1, 'id !=' => $id, 'is_step4' => 1, 'company_id' => $company_id));
        $this->page_data['edit'] = $edit_info;
        $this->page_data['total_organization'] = $total_organization;
        $this->page_data['stock_balance'] = $stock_balance;
        $this->page_data['total_wallet'] = $total_wallet;
        $this->page_data['point_balance'] = $point_balance;
        $this->check_is_fake_data($edit_info, $this->page_data, "view_agent", "Member");
    }

    public function get_tree_view(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $id, 'active' => 1, 'status' => "APPROVE"));
        if($user_info['user_type'] == "ADMIN"){
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, user_type, package_id, voucher_id, insert_time", array('active' => 1, 'status' => "APPROVE", 'user_type' => "AGENT", 'is_done' => 1));
        }else{
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, user_type, package_id, voucher_id, insert_time", array('id' => $id, 'active' => 1, 'status' => "APPROVE", 'is_done' => 1));
        }

        if($member_info['voucher_id'] != 0){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $member_info['voucher_id']));
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $voucher_info['package_id'], 'active' => 1));
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $member_info['package_id'], 'active' => 1));
        }

        $package_name = isset($package_info['id']) ? $package_info['name'] : "";

        $total_organization = $this->get_organization($id);

		$output = array(
			"id" => $member_info['id'],
			"text" => $member_info['username'] . " | " . $member_info['fullname'] . " | " . $package_name . " | " . "Date Joined : " . date("Y/m/d", strtotime($member_info['insert_time'])) . " | " . "Organization : " . $total_organization
		);
		$downline = $this->get_tree_downline($member_info['id']);
		if(count($downline) > 0){
			$output['children'] = $downline;
		}
		
		$json['response_data']['chart_data'] = $output;
		$this->load->view("output/success_response", $json);
	}

    public function get_tree_downline($user_id , $level = 1){
		$data = array();
		$result = $this->Api_Model->get_all_sql(TBL_USER, "id, username, fullname, user_type, package_id, voucher_id, insert_time", "WHERE referral_id = '$user_id' AND status = 'APPROVE' AND user_type = 'AGENT' AND package_id != 0 AND is_done = '1'");
		
		if(count($result) > 0){
			foreach($result as $d_ky => $d_li){
                if($d_li['voucher_id'] != 0){
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $d_li['voucher_id']));
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $voucher_info['package_id'], 'active' => 1));
                }else{
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $d_li['package_id'], 'active' => 1));
                }
                $package_name = isset($package_info['id']) ? $package_info['name'] : "";
				$downline = $this->get_tree_downline($d_li['id'], $level);
                $total_organization = $this->get_organization($d_li['id']);
				if(count($downline) > 0){
					$data[] = array(
						"id" => $d_li['id'],
						"text" => $d_li['username'] . " | " . $d_li['fullname'] . " | " . $package_name . " | " . "Date Joined : " . date("Y/m/d", strtotime($d_li['insert_time'])) . " | " . "Organization : " . $total_organization,
						'children' => $downline
					);
				}
				else{
					$data[] = array(
						"id" => $d_li['id'],
						"text" => $d_li['username'] . " | " . $d_li['fullname'] . " | " . $package_name . " | " . "Date Joined : " . date("Y/m/d", strtotime($d_li['insert_time'])) . " | " . "Organization : " . "0",
					);
				}
			}
		}
        

		return $data;
	}

    public function get_organization($user_id){
        $total_group = 0;
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, 'id', array('referral_id' => $user_id, 'active' => 1, 'status' => "APPROVE", 'package_id !=' => 0, 'is_done' => 1));
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $downline_list = $this->Api_Model->get_rows(TBL_USER, "id", array('referral_id' => $user_id, 'active' => 1, 'package_id !=' => 0, 'is_done' => 1));
            while(count($downline_list) > 0){
                if(count($downline_list) > 0){
                    $total_group += count($downline_list);
                }else{
                    break;
                }

                $query_str = implode("','", array_column($downline_list, "id"));
                $downline_list = $this->Api_Model->get_all_sql(TBL_USER, 'id', "WHERE package_id != 0 AND is_done = '1' AND referral_id IN ('".$query_str."')");
            }
        }else{
            $total_group = 0;
        }

        return $total_group;
    }

    public function check_stock_balance_post($user_id){
        $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
        $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
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

    public function check_wallet_balance_post($user_id, $is_released = false, $is_drb = 0){
        if($is_released){
            if($is_drb == 1){
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type' => "drb"));
            }else{
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type !=' => "drb"));
            }
        }else{
            if($is_drb == 1){
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
            }else{
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
            }
        }
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function get_cash_wallet_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id, 'to_user_id' => $user_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_WALLET, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('description' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_WALLET . "*", $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }

        $output_data = array();
        $result['data'] = [];
        $wallet_record_list = $this->Api_Model->get_datatables_list(TBL_WALLET, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        
        foreach ($wallet_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            if($row['type'] == "break_away"){
                $row['wallet_type'] = "Break Away";
            }else if($row['type'] == "cross_over"){
                $row['wallet_type'] = "Overriding";
            }else if($row['type'] == "smart_partner"){
                $row['wallet_type'] = "Smart Partner";
            }else if($row['type'] == "drb"){
                $row['wallet_type'] = "DRB";
            }else if($row['type'] == "mdbm"){
                $row['wallet_type'] = "Monthly Developement Bonus";
            }else if($row['type'] == "mdbq"){
                $row['wallet_type'] = "Quarterly Developement Bonus";
            }else if($row['type'] == "mms"){
                $row['wallet_type'] = "MMS";
            }else if($row['type'] == "normal"){
                $row['wallet_type'] = "Retail";
            }

            if($row['debit'] == 0.00){
                $row['transaction'] = "+" . $row['credit'];
            }else{
                $row['transaction'] = "-" . $row['debit'];
            }
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_member(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : "";
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : "";

        $result = array();
        $result['draw'] = $draw;

        if($user_type == "ADMIN" && $group_id == 1){
            $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT");
        }else{
            $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('phone_no' => $search);
            $where_group_or_like_query = array(
                'fullname' => $search,
                'email' => $search,
                'username' => $search
            );
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                    $order_query = $order_query == "" ? " username ".$row['dir'] : $order_query.", username ".$row['dir'];
                }else if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " fullname ".$row['dir'] : $order_query.", fullname ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " rank ".$row['dir'] : $order_query.", rank ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " referral_id ".$row['dir'] : $order_query.", referral_id ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " ic ".$row['dir'] : $order_query.", ic ".$row['dir'];
                }else if ($row['column'] == 5) {
                    $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $agent_list = $this->Api_Model->get_datatables_list(TBL_USER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($agent_list as $row) {
            $counting++;
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $row['referral_id']));
            $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('active' => 1, 'id' => $row['package_id']));
            $row['package'] = isset($package_info['id']) ? $package_info['name'] : "";

            if($row['referral_id'] == 0){
                $row['referral_id'] = "";
            }else{
                $row['referral_id'] = $referral_username . " (" . $referral_fullname . ")";
            }
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $btn = '';
            if($user_type == "ADMIN"){
                $btn .= "<a href='" . site_url() . "Member/view/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("view") . "</a> ";
                if($counting != 1){
                    $btn .= "<a href='#' onclick='delete_member(" . $row['id'] . "); return false;' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a> ";
                }
            }
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_promotion_member(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : "";
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : "";
        $is_gold_voucher = isset($this->request_data['is_gold_voucher']) ? $this->request_data['is_gold_voucher'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($user_type == "ADMIN" && $group_id == 1){
            $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT");
        }else{
            $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('phone_no' => $search);
            $where_group_or_like_query = array(
                'fullname' => $search,
                'email' => $search
            );
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                    $order_query = $order_query == "" ? " username ".$row['dir'] : $order_query.", username ".$row['dir'];
                }else if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " rank ".$row['dir'] : $order_query.", rank ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " referral_id ".$row['dir'] : $order_query.", referral_id ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " ic ".$row['dir'] : $order_query.", ic ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $agent_list = $this->Api_Model->get_datatables_list(TBL_USER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($agent_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $row['referral_id']));
            $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('active' => 1, 'id' => $row['package_id']));
            $row['package'] = isset($package_info['id']) ? $package_info['name'] : "";

            if($row['referral_id'] == 0){
                $row['referral_id'] = "";
            }else{
                $row['referral_id'] = $referral_username . " (" . $referral_fullname . ")";
            }
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $btn = '';
            if($user_type == "ADMIN"){
                if($is_gold_voucher == 1){
                    $btn .= "<a href='" . site_url() . "Member/golde/" . $id . "' class='btn-sm btn-success' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a>";
                }else{
                    $btn .= "<a href='" . site_url() . "Member/promotione/" . $id . "' class='btn-sm btn-success' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a>";
                }
            }
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_brand_member(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : "";

        $result = array();
        $result['draw'] = $draw;
        $where_query = array('active' => 1, 'user_type' => "AGENT", 'company_id' => $company_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('phone_no' => $search);
            $where_group_or_like_query = array(
                'fullname' => $search,
                'email' => $search
            );
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_USER, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }

        $output_data = array();
        $result['data'] = [];
        $agent_list = $this->Api_Model->get_datatables_list(TBL_USER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($agent_list as $row) {
            $list = "";
            $agent_list = $this->Api_Model->get_rows(TBL_USER, "id, fullname, referral_id", array('active' => 1, 'user_type' => "AGENT", 'company_id' => $company_id, 'is_step4' => 1), "", "", "fullname", "ASC");
            $list .= '<select name="attr[' . $row['id'] . '][referral_id]" class="select2 form-control upline-select2">';
            $list .= '<option value="0">' . "Select Upline" . '</option>';
            foreach($agent_list as $row_agent){
                if($row_agent['id'] == $row['referral_id']){
                    $list .= '<option value="' . $row_agent['id'] . '" selected>' . $row_agent['fullname'] . '</option>';
                }else{
                    $list .= '<option value="' . $row_agent['id'] . '">' . $row_agent['fullname'] . '</option>';
                }
            }
            $list .= '</select>';
            $list .= '<input type="hidden" name="user_id[]" value="' . $row['id'] . '">';

            $fullname = $row['fullname'];
            if($fullname == ""){
                $row['username'] = $row['username'];
            }else{
                $row['username'] = $row['fullname'] . "(" . $row['username'] . ")";
            }

            $row['referral_id'] = $list;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function update_brand_all_upline(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $user_id_arr = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : [];
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        
        if(!empty($user_id_arr)){
            foreach($user_id_arr as $row_user_id){
                $referral_id = $attr[$row_user_id]['referral_id'];

                $data_update = array(
                    'referral_id' => $referral_id
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $row_user_id, 'active' => 1, 'company_id' => $company_id), $data_update);
            }
        }

        $this->load->view("output/success_response");
    }

    public function insert_member(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $ori_password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "";
        $referral_code = $this->generate_referral_code(9);
        $password = password_hash($ori_password, PASSWORD_BCRYPT);

        if($company_id == 0){
            $data['message'] = "Company Not Found !";
			$this->load->view("output/error_response", $data);
        }else{
            $data_register = array(
                'platform' => "DEFAULT",
                'user_type' => "AGENT",
                'company_id' => $company_id,
                'referral_code' => $referral_code,
                'username' => $username,
                'password' => $password,
                'email' => $email
            );
            $this->Api_Model->insert_data(TBL_USER, $data_register);

            $json['response_data'] = $data_register;
            $this->load->view("output/success_response", $json);
        }
    }

    // public function update_ic_no(){
    //     $user_list = $this->Api_Model->get_all_sql(TBL_USER, "*", "WHERE company_id = 1 AND active = 1 AND user_type = 'AGENT' AND dob IS NULL ORDER BY id ASC");

    //     foreach($user_list as $row_user_list){
    //         $dob = $this->get_birthday($row_user_list['ic']);
    //         $age = $this->calculate_age($dob);
    //         $data_update1 = array(
    //             'dob' => $dob,
    //             'age' => $age
    //         );
    //         $this->Api_Model->update_data(TBL_USER, array('id' => $row_user_list['id'], 'active' => 1), $data_update1);
    //     }
    // }

    public function update_member(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : "";
        $is_company = isset($this->request_data['is_company']) ? $this->request_data['is_company'] : 0;
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $ic = isset($this->request_data['ic']) ? $this->request_data['ic'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "";
        $address_line1 = isset($this->request_data['address_line1']) ? $this->request_data['address_line1'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";
        $insert_time = isset($this->request_data['insert_time']) ? $this->request_data['insert_time'] : "";
        $step = isset($this->request_data['step']) ? $this->request_data['step'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        if($step == 1){
            $is_exist_username = $this->Api_Model->get_rows_info(TBL_USER, "*", array('username' => $username, 'active' => 1, 'id!=' => $user_id, 'company_id' => $company_id));
            if(isset($is_exist_username['id']) && $is_exist_username['id'] > 0){
                $data['message'] = "Username already exist !";
                $this->load->view("output/error_response", $data);
            }else if($this->check_is_valid_ic($ic) === false && $ic != ""){
                $data['message'] = "Invalid Ic Format !";
                $this->load->view("output/error_response", $data);
            }else{
                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
                $current_user_upline = isset($user_info['id']) ? $user_info['referral_id'] : 0;

                $dob = $this->get_birthday($ic);
                $age = $this->calculate_age($dob);
                $data_update1 = array(
                    'referral_id' => $referral_id,
                    'email' => $email,
                    'username' => $username,
                    'dob' => $dob,
                    'age' => $age,
                    'phone_no' => $phone_no,
                    'ic' => $ic,
                    'is_company' => $is_company,
                    'insert_time' => $insert_time
                );
                if($password != ""){
                    if($password != $cfm_password){
                        $data['message'] = "Both password is not same !";
                        $this->load->view("output/error_response", $data);
                    }else{
                        $new_password = password_hash($password, PASSWORD_BCRYPT);
                        $data_update1['password'] = $new_password;
                    }
                }
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update1);

                // if($referral_id != $current_user_upline){
                //     $data_update_upline = array(
                //         'referral_id' => $referral_id
                //     );
                //     $this->Api_Model->update_multiple_data(TBL_USER, array('referral_id' => $user_id, 'active' => 1), $data_update_upline);
                // }

                $json['response_data'] = $data_update1;
                $this->load->view("output/success_response", $json);
            }
        }else if($step == 2){
            $data_update2 = array(
                'address_line1' => $address_line1,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update2);

            $json['response_data'] = $data_update2;
            $this->load->view("output/success_response", $json);
        }else if($step == 3){
            $data_update3 = array(
                'bank_name' => $bank_name,
                'account_name' => $account_name,
                'account_no' => $account_no
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update3);

            $json['response_data'] = $data_update3;
            $this->load->view("output/success_response", $json);
        }else{
            $this->load->view("output/success_response");
        }
    }

    public function delete_member(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, voucher_id", array('id' => $id, 'active' => 1));
        $voucher_id = isset($user_info['id']) ? $user_info['voucher_id'] : 0;

        if($voucher_id != 0){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, balance_quantity, type", array('id' => $voucher_id));
            $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
            $current_balance_quantity = isset($voucher_info['id']) ? $voucher_info['balance_quantity'] : 0;
            $new_quantity = $current_balance_quantity + 1;

            $data_update_voucher = array(
                'balance_quantity' => $new_quantity
            );
            $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id), $data_update_voucher);

            if($voucher_type == "BIG_PRESENT"){
                $data_update_big_present_log = array(
                    'active' => 0
                );
                $this->Api_Model->update_data(TBL_BIG_PRESENT_LOG, array('big_present_id' => $voucher_id, 'user_id' => $id), $data_update_big_present_log);
            }else if($voucher_type == "VOUCHER"){
                $data_update_voucher_log = array(
                    'active' => 0
                );
                $this->Api_Model->update_data(TBL_VOUCHER_LOG, array('voucher_id' => $voucher_id, 'user_id' => $id), $data_update_voucher_log);
            }
        }

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $id), $data);
        $this->Api_Model->update_multiple_data(TBL_PURCHASE_PACKAGE, array('user_id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function generate_report(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 618;
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : 11;
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : 2021;

        $agent_list = $this->Api_Model->get_all_sql(TBL_PURCHASE_PACKAGE, "id, user_id", "WHERE referral_id = '$user_id' AND active = '1' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' GROUP BY user_id");

        if(!empty($agent_list)){
            $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
            $this->page_data['user_id'] = $user_id;
            $this->page_data['month'] = $month;
            $this->page_data['year'] = $year;
            $this->page_data['agent_list'] = $agent_list;
            $html = $this->load->view('main/agent_sales_report',$this->page_data,true);
            $mpdf->WriteHTML($html);
            // $mpdf->Output(); // opens in browser
            $mpdf->Output("../img/report/summary" . $month . "_" . $year . "_" . $user_id . '.pdf','F');

            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Empty Data !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function generate_referral_code($length){
        $prefix = generateOneLetter();
        $user_id = $prefix . substr(str_shuffle('0123456789'),1,$length);
        return $user_id;
    }

    public function calculate_age($dob){
        $diff = (date('Y') - date('Y',strtotime($dob)));
        return $diff;
    }

    public function check_is_valid_ic($ic){
        $regex = '/^[0-9]{6}[0-9]{2}[0-9]{4}$/';

        if (preg_match($regex, $ic)) {
            return true;
        } else {
            return false;
        }
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

    public function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}
