<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function package(){
        $this->load(ADMIN_URL . "/package_report");
    }
    
    public function shipment(){
        $this->load(ADMIN_URL . "/shipment_report");
    }

    public function member(){
        $this->load(ADMIN_URL . "/member_report");
    }

    public function wablaster(){
        $this->load(ADMIN_URL . "/wablaster_report");
    }

    public function product(){
        $this->load(ADMIN_URL . "/product_report");
    }

    public function mmember(){
        $this->load(ADMIN_URL . "/max_package_member_report");
    }

    public function training(){
        $this->load(ADMIN_URL . "/training_report");
    }

    public function summary(){
        $this->load(ADMIN_URL . "/summary_report");
    }
	
	 public function stock(){
        $this->load(ADMIN_URL . "/agent_stock_report");
    }
	
	public function monthly_sales_report(){
        $this->load(ADMIN_URL . "/monthly_sales_report");   
    }

    public function get_package_report(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_PACKAGE, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('description' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_PACKAGE, $where_query, $where_group_like_query, $where_group_or_like_query);
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
        $package_report_list = $this->Api_Model->get_datatables_list(TBL_PACKAGE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);

        foreach ($package_report_list as $row) {
            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, name, code", array('id' => $row['country_id'], 'active' => 1));
            $country_name = isset($country_info['id']) ? $country_info['name'] : "";

            $total_package_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_package", array('package_id' => $row['id'], 'active' => 1));
            $total_package = isset($total_package_info['id']) ? $total_package_info['total_package'] : 0;
            
            $monthly_package_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_package", array('package_id' => $row['id'], 'active' => 1, 'MONTH(insert_time)' => date("m")));
            $monthly_package_total = isset($monthly_package_info['id']) ? $monthly_package_info['total_package'] : 0;

            $row['country_name'] = $country_name;
            $row['total_package'] = $total_package;
            $row['monthly_package_total'] = $monthly_package_total;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_shipment_report(){
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
            $where_group_like_query = array('id' => $search);
            // $where_group_or_like_query = array('rndcode' => $search);
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
        }

        $output_data = array();
        $result['data'] = [];
        $order_list = $this->Api_Model->get_datatables_list(TBL_ORDER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($order_list as $row) {
            $order_type = $row['type'];
            $order_referral_id = $row['referral_id'];
            $order_user_id = $row['user_id'];
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

            if($order_referral_id == 0){
                $agent_id = $order_user_id;
            }else{
                if($order_type == "normal"){
                    $agent_id = $order_user_id;
                }else{
                    $agent_id = $order_referral_id;
                }
            }

            $agent_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, phone_no", array('id' => $agent_id, 'active' => 1));
            if(isset($agent_info['id']) && $agent_info['id'] > 0){
                $agent_username = $agent_info['username'];
                $agent_fullname = $agent_info['fullname'];
                $agent_contact = $agent_info['phone_no'];
            }else{
                $agent_username = "";
                $agent_fullname = "";
                $agent_contact = "";
            }

            $row['order_id'] = "000" . $row['id'];
            $row['order_type'] = $row['type'];
            $row['agent_name'] = $agent_fullname . " (" . $agent_username . ")";
            $row['agent_contact'] = $agent_contact;
            $row['shipment_name'] = $shipping_name;
            $row['shipping_contact'] = $shipping_contact;
            $row['shipping_address'] = $shipping_address;
            $row['shipping_city'] = $shipping_city;
            $row['shipping_state'] = $shipping_state;
            $row['shipping_postcode'] = $shipping_postcode;
            $row['shipping_country'] = $shipping_country;
            $row['shipping_remark'] = $shipping_remark;
            
            $product_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('order_id' => $row['id'], 'active' => 1, 'user_id' => $row['user_id']));
            if(!empty($product_list)){
                foreach($product_list as $row_product){
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name, image", array('id' => $row_product['product_id'], 'active' => 1));
                    if(isset($product_info['id']) && $product_info['id'] > 0){
                        $row['product_details'] = "<span>" . $product_info['name'] . " x " . $row_product['quantity'] . "</span>";
                    }else{
                        $row['product_details'] = "";
                    }
                }
            }else{
                $row['product_details'] = "";
            }

            $row['payment_status'] = $row['payment_status'];
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $row['delivery_company'] = $delivery_company;
            $row['tracking_no'] = $tracking_no;
            $row['tracking_url'] = $tracking_url;

            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_member_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : "";
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : "";
        $is_search = isset($this->request_data['is_search']) ? $this->request_data['is_search'] : 0;
        $from_date = isset($this->request_data['from_date']) ? $this->request_data['from_date'] : "";
        $to_date = isset($this->request_data['to_date']) ? $this->request_data['to_date'] : "";

        $result = array();
        $result['draw'] = $draw;

        if($is_search == 1){
            if($user_type == "ADMIN" && $group_id == 1){
                $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'is_done' => 1, 'DATE(insert_time) >=' => $from_date, 'DATE(insert_time) <=' => $to_date);
            }else{
                $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'company_id' => $company_id, 'is_done' => 1, 'DATE(insert_time) >=' => $from_date, 'DATE(insert_time) <=' => $to_date);
            }
        }else{
            if($user_type == "ADMIN" && $group_id == 1){
                $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'is_done' => 1);
            }else{
                $where_query = array('active' => 1, 'id !=' => 1, 'user_type' => "AGENT", 'company_id' => $company_id, 'is_done' => 1);
            }
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
        }

        $output_data = array();
        $result['data'] = [];
        $agent_list = $this->Api_Model->get_datatables_list(TBL_USER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($agent_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));

            $agent_username = isset($row['id']) ? $row['username'] : "";
            $agent_fullname = isset($row['id']) ? $row['fullname'] : "";
            if($agent_username == "" || $agent_fullname == ""){
                $row['agent_name'] = "";
            }else{
                $row['agent_name'] = $agent_username . " (" . $agent_fullname . ")";
            }

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('active' => 1, 'id' => $row['package_id']));
            $row['agent_package'] = isset($package_info['id']) ? $package_info['name'] : "";

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $row['referral_id']));
            $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
            if($row['referral_id'] == 0){
                $row['upline_name'] = "";
            }else{
                $row['upline_name'] = $referral_username . " (" . $referral_fullname . ")";
            }

            $row['rb_voucher'] = "";
            $rb_voucher_list = $this->Api_Model->get_rows(TBL_RB_VOUCHER, "*", array('user_id' => $row['id'], 'active' => 1));
            if(!empty($rb_voucher_list)){
                foreach($rb_voucher_list as $row_rb_voucher){
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('active' => 1, 'id' => $row_rb_voucher['package_id']));
                    if(isset($package_info['id']) && $package_info['id'] > 0){
                        $row['rb_voucher'] .= "<span>" . $package_info['name'] . " x " . $row_rb_voucher['quantity'] . "</span><br>";
                    }else{
                        $row['rb_voucher'] = "";
                    }
                }
            }

            $drb_balance = $this->check_drb_balance_post($row['id']);
            $row['drb_balance'] = $drb_balance;
            $wallet_balance = $this->check_wallet_balance_post($row['id']);
            $row['wallet_balance'] = $wallet_balance;
            $stock_balance = $this->check_stock_balance_post($row['id']);
            $row['stock_balance'] = $stock_balance;
            $counting++;
            $row['wa_no'] = $counting;
            $row['wa_check'] = "TRUE";
            $row['wa_target'] = "6" . $row['phone_no'];
            $row['wa_source'] = "";
            $row['wa_status'] = "";
            $row['last_name'] = "";
            $row['password'] = "";
            $row['zip'] = "";
            $row['country'] = "";

            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));

            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_summary_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("m");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'company_id' => $company_id);
        
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
        }

        $output_data = array();
        $result['data'] = [];
        $purchase_package_list = $this->Api_Model->get_datatables_group_by(TBL_PURCHASE_PACKAGE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, "user_id");
        $total_purchase_package = 0;

        foreach ($purchase_package_list as $row) {
            $total_purchase_package += $row['amount'];
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username", array('id' => $row['user_id'], 'active' => 1));
            $member_name = isset($user_info['id']) ? $user_info['username'] : "";
            $row['member_name'] = $member_name;

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $row['package_id'], 'active' => 1));
            $package_name = isset($package_info['id']) ? $package_info['name'] : "";
            $row['package_name'] = $row['amount'] . " " . $package_name;

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname", array('id' => $row['referral_id'], 'active' => 1));
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
            $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
            $row['referral_name'] = $referral_username . " (" . $referral_fullname . ")";

            $retail_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, COUNT(*) as total_retail_sales", array('referral_id' => $row['user_id'], 'active' => 1, 'order_status' => "APPROVE", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
            $total_retail_sales = isset($retail_info['id']) ? $retail_info['total_retail_sales'] : 0;
            $row['total_retail_sales'] = $total_retail_sales;

            $retail_amount_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, SUM(total_price) as total_retail_sales_amount", array('referral_id' => $row['user_id'], 'active' => 1, 'order_status' => "APPROVE", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
            $total_retail_sales_amount = isset($retail_amount_info['id']) ? $retail_amount_info['total_retail_sales_amount'] : 0;
            $row['total_retail_sales_amount'] = $total_retail_sales_amount;

            $restock_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, COUNT(*) as total_restock", array('referral_id' => $row['user_id'], 'status' => "APPROVE", 'is_restock' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
            $total_restock = isset($restock_package_info['id']) ? $restock_package_info['total_restock'] : 0;
            $row['total_restock'] = $total_restock;

            $result['data'][] = $row;
        }

        $result['total_purchase_package'] = $total_purchase_package;
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_product_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $is_search = isset($this->request_data['is_search']) ? $this->request_data['is_search'] : 0;
        // $from_date = isset($this->request_data['from_date']) ? $this->request_data['from_date'] : "";
        // $to_date = isset($this->request_data['to_date']) ? $this->request_data['to_date'] : "";
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("m");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $user_type = isset($this->request_data['user_type']) ? $this->request_data['user_type'] : "";
        $group_id = isset($this->request_data['group_id']) ? $this->request_data['group_id'] : "";

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "name";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_PRODUCT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_PRODUCT, $where_query, $where_group_like_query, $where_group_or_like_query);
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
        $product_list = $this->Api_Model->get_datatables_list(TBL_PRODUCT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $total_sales = 0;
        $total_gram = 0;

        foreach ($product_list as $row) {
            if($is_search == 1){
                $order_detail_info = $this->Api_Model->get_rows_info(TBL_ORDER_DETAIL, "id, SUM(quantity) as total_quantity", array('product_id' => $row['id'], 'month(insert_time)' => $month, 'year(insert_time) ' => $year, 'active' => 1, 'is_cancel' => 0, 'is_delivered' => 1, 'is_approve' => 1));
            }else{
                $order_detail_info = $this->Api_Model->get_rows_info(TBL_ORDER_DETAIL, "id, SUM(quantity) as total_quantity", array('product_id' => $row['id'], 'active' => 1, 'is_cancel' => 0, 'is_delivered' => 1, 'is_approve' => 1));
            }
            $total_quantity = isset($order_detail_info['id']) ? $order_detail_info['total_quantity'] : 0;
            
            $row['total_quantity'] = $total_quantity;
            $total_sales += $total_quantity;

            $product_gram = isset($row['id']) ? $row['gram'] : "0.00";
            $total_gram += $product_gram * $total_quantity;

            $result['data'][] = $row;
        }
        $result['total_quantity'] = $total_sales;
        $result['total_kg'] = $total_gram / 1000;
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }	
	
    public function get_high_package_member_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
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

        foreach ($agent_list as $alkey => $row) {
            $company_id = $row['company_id'];

            $max_package_list = $this->get_conditions_package_id_array($company_id, "break_away");

            if(in_array($row['package_id'], $max_package_list) && $row['is_voucher'] == 1){
                $agent_username = isset($row['id']) ? $row['username'] : "";
                $agent_fullname = isset($row['id']) ? $row['fullname'] : "";
                if($agent_username == "" || $agent_fullname == ""){
                    $row['agent_name'] = "";
                }else{
                    $row['agent_name'] = $agent_username . " (" . $agent_fullname . ")";
                }
    
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $row['referral_id']));
                $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
                $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
                if($row['referral_id'] == 0){
                    $row['upline_name'] = "";
                }else{
                    $row['upline_name'] = $referral_username . " (" . $referral_fullname . ")";
                }
    
                $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
                $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, amount", array('user_id' => $row['id'], 'package_id' => $row['package_id'], 'active' => 1), "id", "ASC", 1);
                $register_total_stock = isset($purchase_package_info['id']) ? $purchase_package_info['amount'] : 0;
                $row['in_stock'] = $register_total_stock;
                $row['total_stock'] = $this->check_stock_balance_post($row['id']);

                $result['data'][] = $row;
            }
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
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

    public function check_drb_balance_post($user_id){
        $drb_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
        $total_credit = isset($drb_balance['total_credit']) ? $drb_balance['total_credit'] : 0;
        $total_debit = isset($drb_balance['total_debit']) ? $drb_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_wallet_balance_post($user_id){
        $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
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
	
	public function get_agent_stock_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("m");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'company_id' => $company_id);
        
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_STOCK, $where_query, $where_group_like_query, $where_group_or_like_query);
        
        if ($search != "") {
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_STOCK, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;

        $order_query="id ASC";        

        $output_data = array();
        $result['data'] = [];
        $stock_info = $this->Api_Model->get_datatables_list(TBL_STOCK, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $total_purchase_package = 0;

        foreach ($stock_info as $row) {
            //$total_purchase_package += $row['amount'];
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username", array('id' => $row['user_id'], 'active' => 1));
            $member_name = isset($user_info['id']) ? $user_info['username'] : "";
           $row['member_name'] = $member_name;

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $row['package_id'], 'active' => 1));
            $package_name = isset($package_info['id']) ? $package_info['name'] : "";
            $row['package_name'] = $package_name;
            
            $credit = $row['credit'];
            $debit = $row['debit'];
            $balance = $row['balance'];
            $date_time = $row['insert_time'];

            $result['data'][] = $row;
        }

        $result['total_purchase_package'] = 0;
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }
	
	public function get_monthly_sales_report(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("m");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'order_status' => "SHIPPED", 'company_id' => $company_id, 'YEAR(insert_time)' => 2022);
        
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_ORDER, $where_query, $where_group_like_query, $where_group_or_like_query);
        
        if ($search != "") {
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
        }
       
        $output_data = array();
        $result['data'] = [];
        $order_master = $this->Api_Model->get_group_by_rows(TBL_ORDER, "SUM(total_quantity) as total_quantity, monthname(insert_time) as month", $where_query, "month(insert_time)");
    //    $total_quantity = $purchase_package_list['tot_qty'];
    //     $month = $purchase_package_list['mo'];
        $total_purchase_package = 0;
        //echo $total_quantity, $month;
          foreach ($order_master as $row) {

             $row['month'];

             $total_quantity = $row['total_quantity'];

            $result['data'][] = $row;
       }
      
        $result['total_purchase_package'] = $total_purchase_package;
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }
	
	
}
