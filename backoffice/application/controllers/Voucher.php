<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/voucher_list");
    }

    public function package(){
        $company_id = $this->user_profile_info['company_id'];
        $this->page_data['user'] = $this->Api_Model->get_rows(TBL_USER, "id, username", array('active' => 1, 'is_done' => 1, 'user_type' => "AGENT", 'company_id' => $company_id), "", "", "username", "ASC");
        $this->load(ADMIN_URL . "/voucher_package_list", $this->page_data);
    }

    public function open(){
        $this->load(ADMIN_URL . "/add_voucher_package");
    }

    public function add(){
        $company_id = $this->user_profile_info['company_id'];
        $this->page_data['company'] = $this->Api_Model->get_rows(TBL_COMPANY, "*", array('active' => 1));
        $this->page_data['country'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('active' => 1, 'company_id' => $company_id));
        $this->load(ADMIN_URL . "/add_voucher", $this->page_data);
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('active' => 1, 'id' => $id));
        $this->page_data['edit'] = $edit_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_voucher", "Voucher");
    }

    public function get_package(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;

        $package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('country_id' => $country_id, 'company_id' => $company_id, 'active' => 1));

        $json['response_data'] = $package_list;
        $this->load->view("output/success_response", $json);
    }

    public function get_voucher(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "BIG_PRESENT";
        $admin_id = isset($this->request_data['admin_id']) ? $this->request_data['admin_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            if($type == "BIG_PRESENT"){
                $where_query = array('active' => 1, 'type' => $type);
            }else{
                $where_query = array('type' => $type);
            }
        }else{
            if($type == "BIG_PRESENT"){
                $where_query = array('active' => 1, 'company_id' => $company_id, 'type' => $type);
            }else{
                $where_query = array('company_id' => $company_id, 'type' => $type);
            }
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_BIG_PRESENT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('code' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_BIG_PRESENT, $where_query, $where_group_like_query, $where_group_or_like_query);
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
            if($type == "BIG_PRESENT"){
                foreach ($order as $row) {
                    if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " code ".$row['dir'] : $order_query.", code ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " balance_quantity ".$row['dir'] : $order_query.", balance_quantity ".$row['dir'];
                    }else if ($row['column'] == 5) {
                        $order_query = $order_query == "" ? " price ".$row['dir'] : $order_query.", price ".$row['dir'];
                    }else if ($row['column'] == 6) {
                        $order_query = $order_query == "" ? " total_stock ".$row['dir'] : $order_query.", total_stock ".$row['dir'];
                    }
                }
            }else{
                foreach ($order as $row) {
                    if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " code ".$row['dir'] : $order_query.", code ".$row['dir'];
                    }else if ($row['column'] == 5) {
                        $order_query = $order_query == "" ? " balance_quantity ".$row['dir'] : $order_query.", balance_quantity ".$row['dir'];
                    }else if ($row['column'] == 6) {
                        $order_query = $order_query == "" ? " price ".$row['dir'] : $order_query.", price ".$row['dir'];
                    }else if ($row['column'] == 7) {
                        $order_query = $order_query == "" ? " total_stock ".$row['dir'] : $order_query.", total_stock ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $big_present_list = $this->Api_Model->get_datatables_list(TBL_BIG_PRESENT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($big_present_list as $row) {
            // check the permission for create voucher
            $admin_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_create_voucher", array('id' => $admin_id));
            $is_create_voucher_permission = isset($admin_info['id']) ? $admin_info['is_create_voucher'] : 0;

            if($row['type'] == "BIG_PRESENT"){
                $big_present_log_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_LOG, "id, user_id", array('big_present_id' => $row['id'], 'active' => 1));
                $agent_id = isset($big_present_log_info['id']) ? $big_present_log_info['user_id'] : 0;
            }else{
                $voucher_log_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, user_id", array('id' => $row['id']));
                $agent_id = isset($voucher_log_info['id']) ? $voucher_log_info['user_id'] : 0;
                $voucher_id = isset($voucher_log_info['id']) ? $voucher_log_info['id'] : 0;

                $used_voucher_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "id, register_user_id", array('user_id' => $agent_id, 'active' => 1, 'voucher_id' => $voucher_id));
                $used_agent_id = isset($used_voucher_info['id']) ? $used_voucher_info['register_user_id'] : 0;

                if($used_agent_id == 0){
                    $row['used_by'] = "";
                }else{
                    $used_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname", array('id' => $used_agent_id, 'active' => 1));
                    $row['used_by'] = isset($used_info['id']) ? $used_info['fullname'] : "";
                }
            }

            $agent_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname", array('id' => $agent_id, 'active' => 1));
            $row['agent_name'] = isset($agent_info['id']) ? $agent_info['fullname'] : "";

            $package_id = $row['package_id'];
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $package = "";
            $package_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT_PACKAGE, "id, package_id, quantity", array('big_present_id' => $row['id'], 'active' => 1));
            if(!empty($package_list)){
                foreach($package_list as $plkey => $plval){
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $plval['package_id'], 'active' => 1));
                    $package_list[$plkey]['package_id'] = isset($package_info['id']) ? $package_info['name'] : 0;
                    unset($package_list[$plkey]['package_id']);
                    $package .= "<span>" . $package_info['name'] . "x" . $plval['quantity'] . "</span><br>";
                }
            }

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
            $row['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
            $row['package'] = $package;
            $btn = '';
            if($row['status'] == "PENDING" && $company_id == 0){
                $btn .= "<a href='" . site_url() . "Voucher/approve_voucher/" . $row['id'] . "' class='btn-sm btn-success' style='border:none;'>" . "Approve" . "</a> ";
            }else{
                if($row['status'] == "PENDING"){

                }else{
                    if($row['type'] == "BIG_PRESENT"){
                        if($row['balance_quantity'] != 0 && $is_create_voucher_permission == 1){
                            $btn .= "<a href='#' onclick='share_link(" . $row['id'] . ")' class='btn-sm btn-dark' style='border:none;'>" . "Share" . "</a> ";
                        }
                        $btn .= "<a href='" . site_url() . "Voucher/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> ";
                    }else if($row['type'] == "VOUCHER"){
                        if($row['active'] == 1){
                            $btn .= "<a href='" . site_url() . "Voucher/vstatus/" . $row['id'] . "/0" . "' class='btn-sm btn-info' style='border:none;'>" . "Deactive" . "</a> ";
                        }else{
                            $btn .= "<a href='" . site_url() . "Voucher/vstatus/" . $row['id'] . "/1" . "' class='btn-sm btn-success' style='border:none;'>" . "Active" . "</a> ";
                        }
                    }
                }
            }
            if($row['type'] != "BIG_PRESENT"){
                if($used_agent_id == 0){
                    $btn .= "<a href='#' onclick='show_voucher_update(" . $row['id'] . ")' class='btn-sm btn-success' style='border:none;'>" . "Update" . "</a> ";
                }
            }
            $btn .= "<a href='#' onclick='delete_voucher(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function update_voucher_log(){
        $register_user_id = isset($this->request_data['register_user_id']) ? $this->request_data['register_user_id'] : 0;
        $voucher_id = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : 0;

        $voucher_log_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "id", array('voucher_id' => $voucher_id, 'active' => 1));
        if(isset($voucher_log_info['id'])){
            $this->Api_Model->update_data(TBL_VOUCHER_LOG, array('voucher_id' => $voucher_id, 'active' => 1), array('register_user_id' => $register_user_id));

            $this->load->view("output/success_response");
        }else{
            $data['message'] = "Invalid Voucher !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function vstatus($voucher_id, $status){
        $data = array('active' => $status);

        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id), $data);

        redirect(site_url() . "Voucher/package", "refresh");
    }

    public function approve_voucher($id){
        $data = array('status' => "APPROVE");
        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $id), $data);
        redirect(site_url() . "Voucher", "refresh");
    }

    public function insert_voucher(){
        $set_package_id = isset($this->request_data['set_package_id']) ? $this->request_data['set_package_id'] : 0;
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $code = isset($this->request_data['code']) ? $this->request_data['code'] : "";
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";
        $total_stock = isset($this->request_data['total_stock']) ? $this->request_data['total_stock'] : 1;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $package_id_arr = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : [];
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : [];
        $is_company = isset($this->request_data['is_company']) ? $this->request_data['is_company'] : 0;
        $total_point = isset($this->request_data['total_point']) ? $this->request_data['total_point'] : 0;

        $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $code, 'active' => 1));

        if($company_id == 0){
            $data['message'] = "Please select company !";
            $this->load->view("output/error_response", $data);
        }else if($country_id == 0){
            $data['message'] = "Please select country !";
            $this->load->view("output/error_response", $data);
        }else if(isset($big_present_info['id']) && $big_present_info['id'] > 0){
            $data['message'] = "Code already exist !";
            $this->load->view("output/error_response", $data);
        }else{
            $data_big_present = array(
                'type' => 1,
                'country_id' => $country_id,
                'company_id' => $company_id,
                'package_id' => $set_package_id,
                'code' => $code,
                'quantity' => $quantity,
                'balance_quantity' => $quantity,
                'price' => $price,
                'total_stock' => $total_stock,
                'total_point' => $total_point,
                'is_company' => $is_company
            );

            if($group_id == 1){
                $data_big_present['status'] = "APPROVE";
            }

            $is_empty_quantity = false;
            $is_empty_price = false;
            if(!empty($package_id_arr)){
                foreach($package_id_arr as $package_id){
                    $quantity = isset($attr[$package_id]['quantity']) ? $attr[$package_id]['quantity'] : 0;
                    $price = isset($attr[$package_id]['price']) ? $attr[$package_id]['price'] : 0;

                    if($price == 0){
                        $is_empty_price = true;
                        $data_big_present_package_arr[] = array(
                            'package_id' => $package_id,
                            'quantity' => $quantity,
                            'price' => $price
                        );
                    }else if($quantity == 0){
                        $is_empty_quantity = true;
                        $data_big_present_package_arr[] = array(
                            'package_id' => $package_id,
                            'quantity' => $quantity,
                            'price' => $price
                        );
                    }else{
                        $data_big_present_package_arr[] = array(
                            'package_id' => $package_id,
                            'quantity' => $quantity,
                            'price' => $price
                        );
                    }
                }
            }else{
                $data_big_present_package_arr = array();
            }

            if(empty($data_big_present_package_arr)){
                $big_present_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);

                $json['response_data'] = $data_big_present;
                $this->load->view("output/success_response", $json);
            }else{
                if($is_empty_price){
                    $data['message'] = "Empty Package Price !";
                    $this->load->view("output/error_response", $data);
                }else if($is_empty_quantity){
                    $data['message'] = "Empty Package Quantity !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $big_present_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);
                    foreach($data_big_present_package_arr as $row_big_present_package){
                        if($row_big_present_package['price'] == 0){
                            $is_empty_price = true;
                        }else if($row_big_present_package['quantity'] == 0){
                            $is_empty_quantity = true;
                        }

                        if($is_empty_quantity || $is_empty_price){
                            $data_big_present_package = array();
                        }else{
                            $data_big_present_package = array(
                                'big_present_id' => $big_present_id,
                                'package_id' => $row_big_present_package['package_id'],
                                'quantity' => $row_big_present_package['quantity'],
                                'price' => $row_big_present_package['price']
                            );

                            $this->Api_Model->insert_data(TBL_BIG_PRESENT_PACKAGE, $data_big_present_package);
                        }
                    }

                    $json['response_data'] = $data_big_present;
                    $this->load->view("output/success_response", $json);
                }
            }
        }
    }

    public function update_voucher(){
        $voucher_id = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : 0;
        $code = isset($this->request_data['code']) ? $this->request_data['code'] : "";
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";
        $total_stock = isset($this->request_data['total_stock']) ? $this->request_data['total_stock'] : 1;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $package_id_arr = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : [];
        $is_company = isset($this->request_data['is_company']) ? $this->request_data['is_company'] : 1;
        $total_point = isset($this->request_data['total_point']) ? $this->request_data['total_point'] : 0;

        $code_exist_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $code, 'active' => 1, 'id !=' => $voucher_id));

        if(isset($code_exist_info['id']) && $code_exist_info['id'] > 0){
            $data['message'] = "Code already exist !";
            $this->load->view("output/error_response", $data);
        }else{
            $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id, 'active' => 1));
            $balance_quantity = isset($big_present_info['id']) ? $big_present_info['balance_quantity'] : 0;

            if($quantity > $balance_quantity){
                $data['message'] = "Balance quantity is smaller than quantity you fill in !";
                $this->load->view("output/error_response", $data);
            }else{
                $data_big_present = array(
                    'code' => $code,
                    'quantity' => $quantity,
                    'balance_quantity' => $quantity,
                    'price' => $price,
                    'total_stock' => $total_stock,
                    'total_point' => $total_point,
                    'is_company' => $is_company
                );

                $is_empty_price = false;
                $is_empty_quantity = false;
                if(!empty($package_id_arr)){
                    foreach($package_id_arr as $package_id){
                        $quantity = isset($attr[$package_id]['quantity']) ? $attr[$package_id]['quantity'] : 0;
                        $price = isset($attr[$package_id]['price']) ? $attr[$package_id]['price'] : 0;

                        if($price == 0){
                            $is_empty_price = true;
                            $data_big_present_package_arr[] = array(
                                'package_id' => $package_id,
                                'quantity' => $quantity,
                                'price' => $price
                            );
                        }else if($quantity == 0){
                            $is_empty_quantity = true;
                            $data_big_present_package_arr[] = array(
                                'package_id' => $package_id,
                                'quantity' => $quantity,
                                'price' => $price
                            );
                        }else{
                            $data_big_present_package_arr[] = array(
                                'package_id' => $package_id,
                                'quantity' => $quantity,
                                'price' => $price
                            );
                        }
                    }
                }else{
                    $data_big_present_package_arr = array();
                }

                if(empty($data_big_present_package_arr)){
                    // clear all to active 0
                    $data_clear_big_present_package = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_multiple_data(TBL_BIG_PRESENT_PACKAGE, array('big_present_id' => $voucher_id), $data_clear_big_present_package);

                    $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'active' => 1), $data_big_present);

                    $json['response_data'] = $data_big_present;
                    $this->load->view("output/success_response", $json);
                }else{
                    if($is_empty_price){
                        $data['message'] = "Empty Package Price !";
                        $this->load->view("output/error_response", $data);
                    }else if($is_empty_quantity){
                        $data['message'] = "Empty Package Quantity !";
                        $this->load->view("output/error_response", $data);
                    }else{
                        // clear all to active 0
                        $data_clear_big_present_package = array(
                            'active' => 0
                        );
                        $this->Api_Model->update_multiple_data(TBL_BIG_PRESENT_PACKAGE, array('big_present_id' => $voucher_id), $data_clear_big_present_package);

                        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'active' => 1), $data_big_present);
                        foreach($data_big_present_package_arr as $row_big_present_package){
                            if($row_big_present_package['price'] == 0){
                                $is_empty_price = true;
                            }else if($row_big_present_package['quantity'] == 0){
                                $is_empty_quantity = true;
                            }
    
                            if($is_empty_quantity || $is_empty_price){
                                $data_big_present_package = array();
                            }else{
                                $data_big_present_package = array(
                                    'package_id' => $row_big_present_package['package_id'],
                                    'quantity' => $row_big_present_package['quantity'],
                                    'price' => $row_big_present_package['price'],
                                    'active' => 1
                                );

                                $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_PACKAGE, "*", array('big_present_id' => $voucher_id, 'package_id' => $row_big_present_package['package_id']));
                                if(isset($big_present_package_info['id']) && $big_present_package_info['id'] > 0){
                                    $big_present_package_id = $big_present_package_info['id'];
                                    $this->Api_Model->update_data(TBL_BIG_PRESENT_PACKAGE, array('id' => $big_present_package_id, 'big_present_id' => $voucher_id, 'package_id' => $row_big_present_package['package_id']), $data_big_present_package);
                                }else{
                                    $data_big_present_package['big_present_id'] = $voucher_id;
                                    $this->Api_Model->insert_data(TBL_BIG_PRESENT_PACKAGE, $data_big_present_package);
                                }
                            }
                        }

                        $json['response_data'] = $data_big_present;
                        $this->load->view("output/success_response", $json);
                    }
                }
            }
        }
    }

    public function search_username(){
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('username' => $username, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_id = $user_info['id'];
            $country_id = $user_info['country_id'];

            $big_present_free_list = $this->Api_Model->get_rows(TBL_USER_BIG_PRESENT_FREE, "*", array('user_id' => $user_id, 'quantity !=' => 0));
            if(!empty($big_present_free_list)){
                foreach($big_present_free_list as $bpkey => $bpval){
                    $package_id = $bpval['package_id'];

                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $package_id, 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name']: "";
                    $big_present_free_list[$bpkey]['package_name'] = $package_name;
                    $big_present_free_list[$bpkey]['id'] = $package_id;
                    unset($big_present_free_list[$bpkey]['package_id']);
                }

                $country_list = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('id' => $country_id, 'active' => 1));

                $data = array(
                    'package' => $big_present_free_list,
                    'country' => $country_list
                );

                $json['response_data'] = $data;
                $this->load->view("output/success_response", $json);
            }else{
                $data['message'] = "Insufficient Voucher !";
                $this->load->view("output/error_response", $data);
            }
        }else{
            $data['message'] = "Invalid Account !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function insert_voucher_package(){
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $voucher_code = $this->generate_voucher_code(8) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(10);

        if($country_id == 0){
            $data['message'] = "Invalid Country !";
            $this->load->view("output/error_response", $data);
        }else if($package_id == 0){
            $data['message'] = "Invalid Package !";
            $this->load->view("output/error_response", $data);
        }else{
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('username' => $username, 'active' => 1));
            $user_id = isset($user_info['id']) ? $user_info['id'] : 0;
            $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

            $data_voucher_log = array(
                'user_id' => $user_id,
                'country_id' => $country_id,
                'package_id' => $package_id
            );

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_PACKAGE, "*", array('package_id' => $package_id, 'active' => 1));
            $package_price = isset($big_present_package_info['id']) ? $big_present_package_info['price'] : "0.00";

            $user_big_present_free_info = $this->Api_Model->get_rows_info(TBL_USER_BIG_PRESENT_FREE, "*", array('package_id' => $package_id, 'user_id' => $user_id, 'active' => 1));
            $current_quantity = isset($user_big_present_free_info['id']) ? $user_big_present_free_info['quantity'] : 0;
            $new_update_quantity = $current_quantity - 1;

            if($current_quantity == 0){
                $data['message'] = "Insufficient Voucher !";
                $this->load->view("output/error_response", $data);
            }else{
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
                $total_price = isset($package_info['id']) ? $package_info['grand_total'] : "0.00";
                $total_stock = isset($package_info['id']) ? $package_info['quantity'] : 0;
                $new_point = $total_price / 2;
                $new_stock = $total_stock / 2;

                $data_big_present = array(
                    'type' => 2,
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                    'package_id' => $package_id,
                    'code' => $voucher_code,
                    'quantity' => 1,
                    'balance_quantity' => 1,
                    'price' => $package_price,
                    'status' => "APPROVE"
                );

                
                if($company_type == "FIXED"){
                    $data_big_present['total_stock'] = $new_stock;
                    $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);
                }else{
                    $data_big_present['total_point'] = $new_point;
                    $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);
                }

                $data_voucher_log['voucher_id'] = $insert_id;
                $this->Api_Model->insert_data(TBL_VOUCHER_LOG, $data_voucher_log);

                $data_user_big_present = array(
                    'quantity' => $new_update_quantity
                );
                $this->Api_Model->update_data(TBL_USER_BIG_PRESENT_FREE, array('package_id' => $package_id, 'user_id' => $user_id, 'active' => 1, 'quantity !=' => 0), $data_user_big_present);

                $json['response_data'] = $data_voucher_log;
                $this->load->view("output/success_response", $json);
            }
        }
    }

    public function delete_voucher(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $id, 'active' => 1));
        $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";

        $data = array(
            'active' => 0
        );
        if($voucher_type == "VOUCHER"){
            $voucher_user_id = $voucher_info['user_id'];
            $voucher_package_id = $voucher_info['package_id'];

            $user_big_present_free_info = $this->Api_Model->get_rows_info(TBL_USER_BIG_PRESENT_FREE, "*", array('user_id' => $voucher_user_id, 'package_id' => $voucher_package_id, 'active' => 1));
            $current_quantity = isset($user_big_present_free_info['id']) ? $user_big_present_free_info['quantity'] : 0;
            $new_update_quantity = $current_quantity + 1;
            $data_update = array(
                'quantity' => $new_update_quantity
            );
            $this->Api_Model->update_data(TBL_USER_BIG_PRESENT_FREE, array('user_id' => $voucher_user_id, 'package_id' => $voucher_package_id, 'active' => 1), $data_update);
            $this->Api_Model->update_data(TBL_VOUCHER_LOG, array('user_id' => $voucher_user_id, 'package_id' => $voucher_package_id, 'active' => 1), $data);
        }
        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function check_referral_username(){
        $agent_name = isset($this->request_data['agent_name']) ? $this->request_data['agent_name'] : "";
        $voucher_id = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, company_id", array('username' => $agent_name, 'active' => 1, 'is_done' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, subdomain", array('id' => $user_info['company_id'], 'active' => 1));
            $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";

            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, code", array('id' => $voucher_id, 'active' => 1));
            $voucher_code = isset($voucher_info['id']) ? $voucher_info['code'] : "";
            $voucher_url = "https://" . $company_subdomain . ".ainra.co/" . "register.html?referral=" . $user_info['username'] . "&voucher=" . $voucher_code;

            $json['response_data'] = $voucher_url;
            $this->load->view("output/success_response", $json);
        }else{
            $data['message'] = "Invalid Username !";
            $this->load->view("output/error_response", $data);
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
}
