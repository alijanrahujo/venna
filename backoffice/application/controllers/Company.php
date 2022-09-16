<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends Base_Controller {
    protected $_sms_api_key = "6fe02ca8bfa206c7455bd265092ae543";
    protected $_sms_api_email = "scanpay4u@gmail.com";

	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/company_list");
    }

    public function add(){
        $this->load(ADMIN_URL . "/add_company");
    }

    public function abouta(){
        $this->load(ADMIN_URL . "/add_company_section");
    }

    public function othersa(){
        $this->load(ADMIN_URL . "/add_others_section");
    }

    public function about(){
        $this->load(ADMIN_URL . "/company_section_list");
    }

    public function others(){
        $this->load(ADMIN_URL . "/others_section_list");
    }

    public function stock(){
        $this->load(ADMIN_URL . "/edit_stock");
    }

    public function announcement(){
        $this->load(ADMIN_URL . "/announcement_list");
    }

    public function announcementa(){
        $this->load(ADMIN_URL . "/add_announcement");
    }
	public function auto_stock()
    {
        $this->load(ADMIN_URL . "/edit_stock_auto");
    }
	
	  public function manage_promotions()
    {
        $this->load(ADMIN_URL . "/manage_promotions");
    }

    public function topup(){
        $this->page_data['company_list'] = $this->Api_Model->get_rows(TBL_COMPANY, "*", array('active' => 1));
        $this->load(ADMIN_URL . "/edit_topup", $this->page_data);
    }

    public function sms(){
        $this->page_data['package_list'] = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('active' => 1, 'company_id' => $this->user_profile_info['company_id']), "", "", "quantity", "DESC");
        $this->load(ADMIN_URL . "/add_otp_message", $this->page_data);
    }

    public function bonus(){
        $company_id = $this->user_profile_info['company_id'];
        $this->page_data['mms'] = $this->Api_Model->get_rows(TBL_MMS_BONUS, "*", array('company_id' => $company_id, 'active' => 1), "", "", "id", "ASC");
        $this->page_data['company'] = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $this->load(ADMIN_URL . "/edit_bonus_setting", $this->page_data);
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('active' => 1, 'id' => $id));
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('active' => 1, 'company_id' => $id, 'user_type' => "ADMIN"));
        $this->page_data['edit'] = $edit_info;
        $this->page_data['user'] = $user_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_company", "Company");
    }

    public function aboute(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $section_info = $this->Api_Model->get_rows_info(TBL_COMPANY_SECTION, "*", array('active' => 1, 'id' => $id));
        $content_info = $this->Api_Model->get_rows_info(TBL_COMPANY_CONTENT, "*", array('active' => 1, 'content_id' => $id));
        $this->page_data['section'] = $section_info;
        $this->page_data['content'] = $content_info;
        $this->check_is_fake_data($section_info, $this->page_data, "edit_about_us", "Company/about");
    }

    public function announcemente(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $announcement_info = $this->Api_Model->get_rows_info(TBL_ANNOUNCEMENT, "*", array('active' => 1, 'id' => $id));
        $this->page_data['edit'] = $announcement_info;
        $this->check_is_fake_data($announcement_info, $this->page_data, "edit_announcement", "Company/announcement");
    }

    public function tnce(){
        $company_id = $this->user_profile_info['company_id'];
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, terms_and_conditions", array('active' => 1, 'id' => $company_id));
        $this->page_data['edit'] = $company_info;
        $this->load(ADMIN_URL . "/edit_tnc", $this->page_data);
    }

    public function get_company(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_COMPANY, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_COMPANY, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $company_list = $this->Api_Model->get_datatables_list(TBL_COMPANY, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($company_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Currency/view/" . $id . "' class='btn-sm btn-primary' style='border:none;'>" . $this->Lang_Model->replaceLang("currency") . "</a> <a href='" . site_url() . "Package/view/" . $id . "' class='btn-sm btn-success' style='border:none;'>" . $this->Lang_Model->replaceLang("package") . "</a> <a href='" . site_url() . "Company/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_company(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_company(){
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "";
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";

        if($password != $cfm_password){
            $data['message'] = "Both password is not same !";
            $this->load->view("output/error_response", $data);
        }else{
            $username_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('username' => $username, 'active' => 1));
            if(isset($username_info['id']) && $username_info['id'] > 0){
                $data['message'] = "Username already exist !";
                $this->load->view("output/error_response", $data);
            }else{
                $data = array(
                    'type' => $type,
                    'name' => $name,
                    'price' => $price
                );
                $company_id = $this->Api_Model->insert_data(TBL_COMPANY, $data);

                $password = password_hash($password, PASSWORD_BCRYPT);
                $data_register = array(
                    'user_type' => "ADMIN",
                    'company_id' => $company_id,
                    'username' => strtolower($username) . "1",
                    'password' => $password,
                    'fullname' => $name,
                    'group_id' => 3,
                    'bank_name' => $bank_name,
                    'account_name' => $account_name,
                    'account_no' => $account_no
                );
                $user_id = $this->Api_Model->insert_data(TBL_USER, $data_register);

                $json['response_data'] = $data;
                $this->load->view("output/success_response", $json);
            }
        }
    }

    public function update_company(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $reg_no = isset($this->request_data['reg_no']) ? $this->request_data['reg_no'] : "";
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";
        $min_withdraw = isset($this->request_data['min_withdraw']) ? $this->request_data['min_withdraw'] : "";
        $break_away_bonus = isset($this->request_data['break_away_bonus']) ? $this->request_data['break_away_bonus'] : "";
        $cross_over_bonus = isset($this->request_data['cross_over_bonus']) ? $this->request_data['cross_over_bonus'] : "";
        $any_cross_over_bonus = isset($this->request_data['any_cross_over_bonus']) ? $this->request_data['any_cross_over_bonus'] : "";
        $drb_bonus = isset($this->request_data['drb_bonus']) ? $this->request_data['drb_bonus'] : "";
        $drb_limit = isset($this->request_data['drb_limit']) ? $this->request_data['drb_limit'] : "";
        $first_smart_partner = isset($this->request_data['first_smart_partner']) ? $this->request_data['first_smart_partner'] : "";
        $smart_partner_bonus = isset($this->request_data['smart_partner_bonus']) ? $this->request_data['smart_partner_bonus'] : "";
        $min_mdb_qty = isset($this->request_data['min_mdb_qty']) ? $this->request_data['min_mdb_qty'] : "";
        $min_quarterly_qty = isset($this->request_data['min_quarterly_qty']) ? $this->request_data['min_quarterly_qty'] : "";
        $rb_voucher_qty = isset($this->request_data['rb_voucher_qty']) ? $this->request_data['rb_voucher_qty'] : "";
        $rb_voucher_value = isset($this->request_data['rb_voucher_value']) ? $this->request_data['rb_voucher_value'] : "";
        $mms_level = isset($this->request_data['mms_level']) ? $this->request_data['mms_level'] : 0;
        $withdrawal_charge_type = isset($this->request_data['withdrawal_charge_type']) ? $this->request_data['withdrawal_charge_type'] : 1;
        $withdrawal_charge_amount = isset($this->request_data['withdrawal_charge_amount']) ? $this->request_data['withdrawal_charge_amount'] : 0;
        $is_infinity_level = isset($this->request_data['is_infinity_level']) ? $this->request_data['is_infinity_level'] : 0;
        $cb_rate = isset($this->request_data['cb_rate']) ? $this->request_data['cb_rate'] : 0;

        $data = array(
            'type' => $type,
            'name' => $name,
            'reg_no' => $reg_no,
            'email' => $email,
            'phone_no' => $phone_no,
            'address' => $address,
            'price' => $price,
            'min_withdraw' => $min_withdraw,
            'break_away_bonus' => $break_away_bonus,
            'cross_over_bonus' => $cross_over_bonus,
            'any_cross_over_bonus' => $any_cross_over_bonus,
            'drb_bonus' => $drb_bonus,
            'drb_limit' => $drb_limit,
            'first_smart_partner' => $first_smart_partner,
            'smart_partner_bonus' => $smart_partner_bonus,
            'min_mdb_qty' => $min_mdb_qty,
            'min_quarterly_qty' => $min_quarterly_qty,
            'rb_voucher_qty' => $rb_voucher_qty,
            'rb_voucher_value' => $rb_voucher_value,
            'withdrawal_charge_type' => $withdrawal_charge_type,
            'withdrawal_charge_amount' => $withdrawal_charge_amount,
            'is_infinity_level' => $is_infinity_level,
            'cb_rate' => $cb_rate
        );
        if($mms_level != 0){
            $data['mms_level'] = $mms_level;
        }
        $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data);

        if($mms_level != 0){
            for ($x = 1; $x <= 3; $x++) {
                $data_mms = array(
                    'company_id' => $company_id,
                    'level' => $x
                );
                $this->Api_Model->insert_data(TBL_MMS_BONUS, $data_mms);
            }
        }

        $data_user = array(
            'bank_name' => $bank_name,
            'account_name' => $account_name,
            'account_no' => $account_no
        );
        $this->Api_Model->update_data(TBL_USER, array('company_id' => $company_id, 'active' => 1, 'user_type' => "ADMIN"), $data_user);

        $json['response_data'] = $data;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_company(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_COMPANY, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function get_company_section(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'type' => $type, 'company_id' => $company_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_COMPANY_SECTION, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_COMPANY_SECTION, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $company_section_list = $this->Api_Model->get_datatables_list(TBL_COMPANY_SECTION, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($company_section_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Company/aboute/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_company_section(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_announcement(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'company_id' => $company_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_ANNOUNCEMENT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_ANNOUNCEMENT, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                    $order_query = $order_query == "" ? " title ".$row['dir'] : $order_query.", title ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $announcement_list = $this->Api_Model->get_datatables_list(TBL_ANNOUNCEMENT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($announcement_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Company/announcemente/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_announcement(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_company_section(){
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : 0;
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";

        $data = array(
            'type' => $type,
            'company_id' => $company_id,
            'name' => $name
        );
        $company_section_id = $this->Api_Model->insert_data(TBL_COMPANY_SECTION, $data);

        $data_content = array(
            'content_id' => $company_section_id
        );
        $this->Api_Model->insert_data(TBL_COMPANY_CONTENT, $data_content);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function delete_company_section(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_COMPANY_SECTION, array('id' => $id), $data);
        $this->Api_Model->update_data(TBL_COMPANY_CONTENT, array('content_id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function delete_announcement(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_ANNOUNCEMENT, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function update_about_us(){
        $section_id = isset($this->request_data['section_id']) ? $this->request_data['section_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $about_us = isset($this->request_data['about_us']) ? $this->request_data['about_us'] : "";

        $data_section = array(
            'name' => $name
        );
        $this->Api_Model->update_data(TBL_COMPANY_SECTION, array('id' => $section_id, 'active' => 1), $data_section);

        $data_content = array(
            'content' => $about_us
        );
        $this->Api_Model->update_data(TBL_COMPANY_CONTENT, array('content_id' => $section_id, 'active' => 1), $data_content);

	    $this->load->view("output/success_response");
    }

    public function update_tnc(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $terms_and_conditions = isset($this->request_data['terms_and_conditions']) ? $this->request_data['terms_and_conditions'] : "";

        $data_company = array(
            'terms_and_conditions' => $terms_and_conditions
        );
        $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data_company);

	    $this->load->view("output/success_response");
    }

    public function update_mms(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $mms_id = isset($this->request_data['mms_id']) ? $this->request_data['mms_id'] : [];

        foreach($mms_id as $row_mms_id){
            $bonus = $attr[$row_mms_id]['bonus'];

            $data = array(
                'bonus' => $bonus
            );
            $this->Api_Model->update_data(TBL_MMS_BONUS, array('company_id' => $company_id, 'id' => $row_mms_id, 'active' => 1), $data);
        }

        $this->load->view("output/success_response");
    }

    public function update_bonus_month(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $bonus_month = isset($this->request_data['bonus_month']) ? $this->request_data['bonus_month'] : "";
        $is_active = isset($this->request_data['is_active']) ? $this->request_data['is_active'] : 0;

        $data_company = array(
            'bonus_month' => $bonus_month,
            'is_released' => $is_active
        );
        $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data_company);

        if($is_active == 1){
            $data_wallet_update = array(
                'is_released' => 1
            );
            $this->Api_Model->update_multiple_data(TBL_WALLET, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_wallet_update);

            $data_mms_update = array(
                'is_released' => 1
            );
            $this->Api_Model->update_multiple_data(TBL_MMS_REPORT, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_mms_update);

            $data_mdb_update = array(
                'is_released' => 1
            );
            $this->Api_Model->update_multiple_data(TBL_MONTHLY_BONUS_REPORT, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_mdb_update);
        }
        // else{
        //     $data_wallet_update = array(
        //         'is_released' => 0
        //     );
        //     $this->Api_Model->update_multiple_data(TBL_WALLET, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_wallet_update);

        //     $data_mms_update = array(
        //         'is_released' => 0
        //     );
        //     $this->Api_Model->update_multiple_data(TBL_MMS_REPORT, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_mms_update);

        //     $data_mdb_update = array(
        //         'is_released' => 0
        //     );
        //     $this->Api_Model->update_multiple_data(TBL_MONTHLY_BONUS_REPORT, array('MONTH(insert_time)' => $bonus_month, 'YEAR(insert_time)' => date("Y"), 'active' => 1, 'company_id' => $company_id), $data_mdb_update);
        // }

	    $this->load->view("output/success_response");
    }

    public function edit_stock(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $agent_username = isset($this->request_data['agent_username']) ? $this->request_data['agent_username'] : "";
        $stock_quantity = isset($this->request_data['stock_quantity']) ? $this->request_data['stock_quantity'] : 0;
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";

        $username_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $agent_username, 'active' => 1));
        $fullname_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('fullname' => $agent_username, 'active' => 1));
        if(isset($username_info['id']) && $username_info['id'] > 0 || isset($fullname_info['id']) && $fullname_info['id'] > 0){
            if(isset($username_info['id']) && $username_info['id'] > 0){
                $user_id = $username_info['id'];
            }else{
                $user_id = $fullname_info['id'];
            }
            $available_stock_balance = $this->check_stock_balance_post($user_id);

            if($available_stock_balance < $stock_quantity && $type == 0){
                $data['message'] = "Insufficient Stock to Deduct !";
                $this->load->view("output/error_response", $data);
            }else{
                if($type == "0"){
                    $new_stock_balance = $available_stock_balance - $stock_quantity;
                }else{
                    $new_stock_balance = $available_stock_balance + $stock_quantity;
                }

                $data_stock = array(
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                    'description' => $description,
                    'balance' => $new_stock_balance
                );
                if($type == "0"){
                    $data_stock['debit'] = $stock_quantity;
                }else{
                    $data_stock['credit'] = $stock_quantity;
                }
                $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_stock' => $new_stock_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);

                $this->load->view("output/success_response");
            }
        }else{
            $data['message'] = "Invalid Agent Username !";
            $this->load->view("output/error_response", $data);
        }
    }

	  public function edit_stock_auto()
    {
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $ausername = isset($this->request_data['ausername']) ? $this->request_data['ausername'] : "";
        $description = "";
        $stock_quantity = 2;

        if ($company_id == 7) {
            if ($type == "1") {
                $username_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $ausername, 'active' => 1));
                $fullname_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('fullname' => $ausername, 'active' => 1));

                if (isset($username_info['id']) && $username_info['id'] > 0 || isset($fullname_info['id']) && $fullname_info['id'] > 0) {
                    if (isset($username_info['id']) && $username_info['id'] > 0 && $username_info['id'] != "") {
                        $user_id = $username_info['id'];
                    } elseif (isset($fullname_info['id']) && $fullname_info['id'] > 0 && $fullname_info['id'] != "") {
                        $user_id = $fullname_info['id'];
                    }
                }
            } else {
                $phone_no_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('phone_no' => $ausername, 'active' => 1));
                $user_id = $phone_no_info['id'];
            }

            $check_duplicate = $this->Api_Model->get_rows_info('vny_user_stock_auto', "user_id", array('user_id' => $user_id));

            $phone_no = $this->Api_Model->get_rows_info(TBL_USER, "phone_no", array('id' => $user_id, 'active' => 1));
            $address = $this->Api_Model->get_rows_info(TBL_USER, "address_line1", array('id' => $user_id, 'active' => 1));
            $email = $this->Api_Model->get_rows_info(TBL_USER, "email", array('id' => $user_id, 'active' => 1));
            $un = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $user_id, 'active' => 1));

            $available_stock_balance = $this->check_stock_balance_post($user_id);

            if ($available_stock_balance <= 1) {
                $new_stock_balance = $available_stock_balance - 1;
            } elseif ($available_stock_balance == 0) {
                $new_stock_balance = 0;
            } else {
                $new_stock_balance = $available_stock_balance - $stock_quantity;
            }
              
            $data_stock2 = array(
                    'user_id' => $user_id,
                    'phone_no' => $phone_no['phone_no'],
                    'address' => $address['address_line1'],
                    'email' => $email['email'],
                    'username' => $un['username']
                );

            if ($un['username'] != "") {
                $this->Api_Model->insert_data("vny_user_stock_auto", $data_stock2);
            }
     
            $this->load->view("output/success_response", );
         
        }
        else {
            $data['message'] = "User/Agent not in Company";
            $this->load->view("output/error_response", $data);
        }
    }
	
	
    public function edit_topup(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $amount = isset($this->request_data['amount']) ? $this->request_data['amount'] : 0;
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";

        if($company_id == 0){
            $data['message'] = "Please Select Brand !";
            $this->load->view("output/error_response", $data);
        }else{
            $available_topup_balance = $this->check_topup_balance_post($company_id);

            if($available_topup_balance < $amount && $type == 0){
                $data['message'] = "Insufficient Amount to Deduct !";
                $this->load->view("output/error_response", $data);
            }else{
                if($amount == 0){
                    $data['message'] = "Please fill in your amount !";
                    $this->load->view("output/error_response", $data);
                }else{
                    if($type == "0"){
                        $new_topup_balance = $available_topup_balance - $amount;
                    }else{
                        $new_topup_balance = $available_topup_balance + $amount;
                    }

                    $data_topup = array(
                        'company_id' => $company_id,
                        'description' => $description,
                        'balance' => $new_topup_balance
                    );
                    if($type == "0"){
                        $data_topup['debit'] = $amount;
                    }else{
                        $data_topup['credit'] = $amount;
                    }
                    $this->Api_Model->insert_data(TBL_COMPANY_TOPUP, $data_topup);

                    // update total quantity to agent acc
                    $data_company_update = array(
                        'total_topup' => $new_topup_balance
                    );
                    $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data_company_update);

                    $this->load->view("output/success_response");
                }
            }
        }
    }

    public function insert_announcement(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $title = isset($this->request_data['title']) ? $this->request_data['title'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";

        $data = array(
            'company_id' => $company_id,
            'title' => $title,
            'content' => $content
        );
        $this->Api_Model->insert_data(TBL_ANNOUNCEMENT, $data);

        $this->load->view("output/success_response");
    }
    
    public function update_announcement(){
        $announcement_id = isset($this->request_data['announcement_id']) ? $this->request_data['announcement_id'] : 0;
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $title = isset($this->request_data['title']) ? $this->request_data['title'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";

        $data = array(
            'title' => $title,
            'content' => $content
        );
        $this->Api_Model->update_data(TBL_ANNOUNCEMENT, array('id' => $announcement_id, 'company_id' => $company_id, 'active' => 1), $data);

        $this->load->view("output/success_response");
    }

    public function send_otp(){
        $member_id_arr = isset($this->request_data['member_id']) ? $this->request_data['member_id'] : 0;

        if(!empty($member_id_arr)){
            $member_id = implode("','", $member_id_arr);
            $member_list = $this->Api_Model->get_all_sql(TBL_USER, "id, phone_no", "WHERE id IN ('".$member_id."') AND active = '1'", true);
            // foreach($member_list as $row_member){
            //     $phone_no = $row_member['phone_no'];
            //     $this->send_otp_post("", $phone_no);
            // }
            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Empty Selection !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function send_otp_post($phone_code = "6", $phone_no = "")
    {
        // initialize data
        $otp_email = $this->_sms_api_email;
        $otp_api_key = $this->_sms_api_key;
        $otp_receipent = $phone_code . $phone_no;
        $otp_unencode_message =  "[Sangrila] 大家下午好，Sangri-La 系统将不定期的做出升级。我们现有的活动都可以在主页的 'Activity' 按钮中找得到，请您登入代理前台系统查看。 谢谢。 Good afternoon everyone, Sangri-La system will be upgraded from time to time. Our existing activities can be found in 'Activity' button at home page, please log in to the agent portal to view. Thank you.";
        $otp_message = urlencode($otp_unencode_message);

        // curl otp sms
        $url = "https://www.smshubs.net/api/sendsms.php?email=$otp_email&key=$otp_api_key&recipient=$otp_receipent&message=$otp_message";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        $output = json_decode($json,true);
        
        if($output['statusCode'] == "1606"){
            // success
            $status = 1;

            $data_otp_log = array(
                'otp_reference' => $output['sms']['items']['referenceID']
            );
        }else{
            // failed
            $status = 2;
        }

        $current_time = date('Y-m-d H:i:s');
        $valid_time = date('Y-m-d H:i:s', strtotime('+90 seconds',strtotime($current_time)));

        $data_otp_log['phone_no'] = $phone_no;
        $data_otp_log['sms_message'] = $otp_unencode_message;
        $data_otp_log['status'] = $status;
        $data_otp_log['status_code'] = $output['statusCode'];
        $data_otp_log['status_msg'] = $output['statusMsg'];
        $this->Api_Model->insert_data(TBL_OTP_LOGS, $data_otp_log);
    }

    public function optimize_desc_image(){
        if (!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/about_us';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/about_us')) {
                @mkdir(IMAGE_PATH . './img/about_us', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('image'))
            {
                $img = $this->upload->data();
                $this->resizingImage($img['file_name']);
                $image = $img['file_name']; 

                $json['response_data'] = DISPLAY_PATH . "img/about_us/" . $image;
		        $this->load->view("output/success_response", $json);
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }
    }

    public function optimize_announcement_image(){
        if (!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/announcement';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '5120'; //in KB
            $config['encrypt_name'] = TRUE;
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/announcement')) {
                @mkdir(IMAGE_PATH . './img/announcement', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('image'))
            {
                $img = $this->upload->data();
                $this->resizingImage($img['file_name']);
                $image = $img['file_name']; 

                $json['response_data'] = DISPLAY_PATH . "img/announcement/" . $image;
		        $this->load->view("output/success_response", $json);
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }
    }

    public function resizingImage($file_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/about_us/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 400,
                'new_image'     => IMAGE_PATH . 'img/about_us/' . $file_name
            )
        );

        $this->load->library('image_lib', $config[0]);
        foreach ($config as $item) {
            $this->image_lib->initialize($item);
            if (!$this->image_lib->resize()) {
                return false;
            }
            $this->image_lib->clear();
        }
    }

    public function check_stock_balance_post($user_id){
        $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
        $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_topup_balance_post($company_id){
        $topup_balance = $this->Api_Model->get_rows_info(TBL_COMPANY_TOPUP, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'company_id' => $company_id));
        $total_credit = isset($topup_balance['total_credit']) ? $topup_balance['total_credit'] : 0;
        $total_debit = isset($topup_balance['total_debit']) ? $topup_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }
	
	public function get_user_list(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
       // $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : 0;

        // $user_info = $this->Api_Model->get_rows_info(TBL_USER_RESTRICTION, "id, username, fullname", array('id' => $user_id, 'active' => 1));
        
         $result = array();
         $result['draw'] = $draw;

         $order_query="id ASC";
         $where_query = array('id' > 0);
         $where_group_like_query = "";
         $where_group_or_like_query = "";
       

         $result['recordsTotal'] = $this->Api_Model->count_datatables_list("vny_user_stock_auto", $where_query, $where_group_like_query, $where_group_or_like_query);

        
         $result['recordsFiltered'] = $result['recordsTotal'];        	
        

         $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
         $result['current_page'] = floor($start/$count) + 1;
         $result['records_per_page'] = $count;
        
        // $user_restriction_query = "";

         $order_query="id ASC";

  

        $where_query = array('id' > 0);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $output_data = array();
        $result['data'] = [];
        $user_list = $this->Api_Model->get_datatables_list("vny_user_stock_auto", "username, phone_no, address, email",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

      //  echo $user_restriction_list['username'];

        foreach ($user_list as $row) {

            //$ausername = $row['user_id'];
           // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
            $username = $row['username'];
            $phone_no = $row['phone_no'];
            $address = $row['address'];
            $email = $row['email'];

            $result['data'][] = $row;
            
        }
        
      //echo $result['data'];
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
        
    }
	
	public function get_ass(){
$company_id = 7; //isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        
        //  $aphone_no = "";isset($this->request_data['aphone_no']) ? $this->request_data['aphone_no'] : "";
          $description = "Auto Stock Management";
          $stock_quantity = 2;
          $start = 0;
          $count = 10;
          $where_query = array('id' > 0);
          $where_group_like_query = "";
          $where_group_or_like_query = "";
          $order_query = "";
  
          $user_list[] = array();
          $user_list = $this->Api_Model->get_datatables_list("vny_user_stock_auto", "user_id",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
          
          
          foreach ($user_list as $row) {
  
              //$ausername = $row['user_id'];
              // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
              $user_id = $row['user_id'];
			  
             $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
			  
             $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
             $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
             $total_balance = $total_credit - $total_debit;
              $available_stock_balance = $total_balance;
  
              if ($available_stock_balance <= 1) {
                  $new_stock_balance = 0;
              } else {
                  $new_stock_balance = $available_stock_balance - $stock_quantity;
              }
  
              $data_stock = array(
                      'user_id' => $user_id,
                      'company_id' => $company_id,
                      'description' => "Auto Stock Management",
                      'balance' => $new_stock_balance
                  );
        
              $data_stock['debit'] = $stock_quantity;
              $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
  
              // update total quantity to agent acc
              $data_user_update = array(
                      'total_stock' => $new_stock_balance
                  );
              $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);             
          }
		
				 
          $this->load->view("output/success_response");
          }
      public function add_promotions()
    {
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : 0;
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 1;
 
        $pid = isset($this->request_data['pid']) ? $this->request_data['pid'] : 0;

        $check_active = $this->Api_Model->get_rows_info("vny_promotions", "*", array('package_id' => $pid, 'active' => 1));
        
        if ($check_active['package_id']>0) {

            $data['message'] = "Selected package is currently on active promotion!";
            $this->load->view("output/error_response", $data);            
        }
        else{
            $data = array(
            
                'package_id' => $pid,
                'company_id' => $company_id,
                'current_price' => $this->request_data['cp'],
                'promotion_price' => $this->request_data['pp'],
                'start_date' => date("Y-m-d"),
                'end_date' => $this->request_data['pe']
            );
                $this->Api_Model->insert_data("vny_promotions", $data);
    
                $data2 = array(
                'grand_total' => $this->request_data['pp']
            );
                $this->Api_Model->update_data(TBL_PACKAGE, array('id' => $this->request_data['pid']), $data2);
    
                $json['response_data'] = $data;
                $this->load->view("output/success_response", $json);
        }
    }
       
	public function get_promotions_list(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
       // $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : 0;

        // $user_info = $this->Api_Model->get_rows_info(TBL_USER_RESTRICTION, "id, username, fullname", array('id' => $user_id, 'active' => 1));
        
         $result = array();
         $result['draw'] = $draw;

         $order_query="id ASC";
         $where_query = array('active' > 0);
         $where_group_like_query = "";
         $where_group_or_like_query = "";
       

         $result['recordsTotal'] = $this->Api_Model->count_datatables_list("vny_promotions", $where_query, $where_group_like_query, $where_group_or_like_query);

        
         $result['recordsFiltered'] = $result['recordsTotal'];        	
        

         $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
         $result['current_page'] = floor($start/$count) + 1;
         $result['records_per_page'] = $count;
        
        // $user_restriction_query = "";

        
         $order_query="id ASC";

  

         $where_query = array('active' > 0);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $output_data = array();
        $result['data'] = [];

        //$sql = "select a.username, a.phone_no, a.address, a.email from vny_user_stock_auto a inner join vny_user b on a.user_id = b.id where b.company_id = " + $company_id;
        //$user_list = $this->db->query($sql);
        //return $query->result_array();

        $promotion_list = $this->Api_Model->get_datatables_list("vny_promotions", "*",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        
        $counting = 0;

      //  echo $user_restriction_list['username'];
 
        foreach ($promotion_list as $row) {

                
            //$ausername = $row['user_id'];
            $pid = $this->Api_Model->get_rows_info("vny_package", "english_name", array('id' => $row['package_id'], 'active' => 1));

            $pn = $pid['english_name'];
          //  $package_name = 
            $row['package_name'] = $pn;
            $promotion_price = $row['promotion_price'];
            $start_date = $row['start_date'];
            $end_date = $row['end_date'];
            //$row['test']="";

            $result['data'][] = $row;
            
        }
        
      //echo $result['data'];
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
        
    }
    
}
