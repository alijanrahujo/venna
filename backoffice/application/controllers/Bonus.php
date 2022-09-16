<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bonus extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function drb(){
        $this->load(ADMIN_URL . "/drb_record_list");
    }

    public function club(){
        $this->load(ADMIN_URL . "/smart_partner_record_list");
    }
    
    public function tuoli(){
        $this->load(ADMIN_URL . "/tuoli_record_list");
    }

    public function yueji(){
        $this->load(ADMIN_URL . "/yueji_record_list");
    }

    public function mdsm(){
        $this->load(ADMIN_URL . "/monthly_bonus_record_list");
    }

    public function mdsq(){
        $this->load(ADMIN_URL . "/quarterly_bonus_record_list");
    }

    public function rb(){
        $this->load(ADMIN_URL . "/rb_record_list");
    }

    public function mms(){
        $this->load(ADMIN_URL . "/mms_record_list");
    }

    public function cb(){
        $this->load(ADMIN_URL . "/cb_record_list");
    }

    public function get_drb_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_DRB_REPORT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('drb.description' => $search);
            $where_group_or_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = drb.user_id";
            if($company_id == 0){
                $where_query = array('drb.active' => 1);
            }else{
                $where_query = array('drb.active' => 1, 'drb.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_DRB_REPORT . " drb", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " drb.description ".$row['dir'] : $order_query.", drb.description ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " drb.bonus ".$row['dir'] : $order_query.", drb.bonus ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " drb.insert_time ".$row['dir'] : $order_query.", drb.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " description ".$row['dir'] : $order_query.", description ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " bonus ".$row['dir'] : $order_query.", bonus ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $drb_record_list = $this->Api_Model->get_datatables_list(TBL_DRB_REPORT . " drb", "drb.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $drb_record_list = $this->Api_Model->get_datatables_list(TBL_DRB_REPORT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        

        foreach ($drb_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            $row['bonus'] = $row['bonus'] . "DRB";
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_smart_partner_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_SMART_PARTNER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('sm.insert_time' => $search);
            $where_group_or_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = sm.user_id";
            if($company_id == 0){
                $where_query = array('sm.active' => 1);
            }else{
                $where_query = array('sm.active' => 1, 'sm.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_SMART_PARTNER . " sm", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " sm.company_sales_after_bonus ".$row['dir'] : $order_query.", sm.company_sales_after_bonus ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " sm.total_sales ".$row['dir'] : $order_query.", sm.total_sales ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " sm.sales_pass_up ".$row['dir'] : $order_query.", sm.sales_pass_up ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " sm.grand_sales ".$row['dir'] : $order_query.", sm.grand_sales ".$row['dir'];
                    }else if ($row['column'] == 5) {
                        $order_query = $order_query == "" ? " sm.group_sales ".$row['dir'] : $order_query.", sm.group_sales ".$row['dir'];
                    }else if ($row['column'] == 6) {
                        $order_query = $order_query == "" ? " sm.bonus_per_box ".$row['dir'] : $order_query.", sm.bonus_per_box ".$row['dir'];
                    }else if ($row['column'] == 7) {
                        $order_query = $order_query == "" ? " sm.bonus ".$row['dir'] : $order_query.", sm.bonus ".$row['dir'];
                    }else if ($row['column'] == 8) {
                        $order_query = $order_query == "" ? " sm.insert_time ".$row['dir'] : $order_query.", sm.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " company_sales_after_bonus ".$row['dir'] : $order_query.", company_sales_after_bonus ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " total_sales ".$row['dir'] : $order_query.", total_sales ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " sales_pass_up ".$row['dir'] : $order_query.", sales_pass_up ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " grand_sales ".$row['dir'] : $order_query.", grand_sales ".$row['dir'];
                    }else if ($row['column'] == 5) {
                        $order_query = $order_query == "" ? " group_sales ".$row['dir'] : $order_query.", group_sales ".$row['dir'];
                    }else if ($row['column'] == 6) {
                        $order_query = $order_query == "" ? " bonus_per_box ".$row['dir'] : $order_query.", bonus_per_box ".$row['dir'];
                    }else if ($row['column'] == 7) {
                        $order_query = $order_query == "" ? " bonus ".$row['dir'] : $order_query.", bonus ".$row['dir'];
                    }else if ($row['column'] == 7) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $sm_record_list = $this->Api_Model->get_datatables_list(TBL_SMART_PARTNER . " sm", "sm.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $sm_record_list = $this->Api_Model->get_datatables_list(TBL_SMART_PARTNER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($sm_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_tuoli_record(){
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

        if($company_id == 0){
            $where_query = array('active' => 1, 'type' => $type);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id, 'type' => $type);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_WALLET, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('w.description' => $search);
            $where_group_or_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = w.to_user_id";
            if($company_id == 0){
                $where_query = array('w.active' => 1, 'w.type' => $type);
            }else{
                $where_query = array('w.active' => 1, 'w.company_id' => $company_id, 'w.type' => $type);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_WALLET . " w", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " w.description ".$row['dir'] : $order_query.", w.description ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " w.debit ".$row['dir'] : $order_query.", w.debit ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " w.credit ".$row['dir'] : $order_query.", w.credit ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " w.insert_time ".$row['dir'] : $order_query.", w.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " to_user_id ".$row['dir'] : $order_query.", to_user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " description ".$row['dir'] : $order_query.", description ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " debit ".$row['dir'] : $order_query.", debit ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " credit ".$row['dir'] : $order_query.", credit ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $wallet_record_list = $this->Api_Model->get_datatables_list(TBL_WALLET . " w", "w.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $wallet_record_list = $this->Api_Model->get_datatables_list(TBL_WALLET, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($wallet_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['to_user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_monthly_bonus_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_MONTHLY_BONUS_REPORT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = mbr.user_id";
            if($company_id == 0){
                $where_query = array('mbr.active' => 1);
            }else{
                $where_query = array('mbr.active' => 1, 'mbr.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_MONTHLY_BONUS_REPORT . " mbr", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " mbr.total_quantity ".$row['dir'] : $order_query.", mbr.total_quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " mbr.bonus_per_box ".$row['dir'] : $order_query.", mbr.bonus_per_box ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " mbr.bonus ".$row['dir'] : $order_query.", mbr.bonus ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " mbr.insert_time ".$row['dir'] : $order_query.", mbr.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " total_quantity ".$row['dir'] : $order_query.", total_quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " bonus_per_box ".$row['dir'] : $order_query.", bonus_per_box ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " bonus ".$row['dir'] : $order_query.", bonus ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $monthly_bonus_record_list = $this->Api_Model->get_datatables_list(TBL_MONTHLY_BONUS_REPORT . " mbr", "mbr.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $monthly_bonus_record_list = $this->Api_Model->get_datatables_list(TBL_MONTHLY_BONUS_REPORT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($monthly_bonus_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_quarterly_bonus_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_QUARTERLY_BONUS_REPORT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = mbq.user_id";
            if($company_id == 0){
                $where_query = array('mbq.active' => 1);
            }else{
                $where_query = array('mbq.active' => 1, 'mbq.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_QUARTERLY_BONUS_REPORT . " mbq", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " mbq.total_quantity ".$row['dir'] : $order_query.", mbq.total_quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " mbq.bonus ".$row['dir'] : $order_query.", mbq.bonus ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " mbq.insert_time ".$row['dir'] : $order_query.", mbq.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " total_quantity ".$row['dir'] : $order_query.", total_quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " bonus ".$row['dir'] : $order_query.", bonus ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $quarterly_bonus_record_list = $this->Api_Model->get_datatables_list(TBL_QUARTERLY_BONUS_REPORT . " mbq", "mbq.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $quarterly_bonus_record_list = $this->Api_Model->get_datatables_list(TBL_QUARTERLY_BONUS_REPORT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($quarterly_bonus_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_rb_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_RB_VOUCHER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = rv.user_id";
            if($company_id == 0){
                $where_query = array('rv.active' => 1);
            }else{
                $where_query = array('rv.active' => 1, 'rv.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_RB_VOUCHER . " rv", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " rv.quantity ".$row['dir'] : $order_query.", rv.quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " rv.value_price ".$row['dir'] : $order_query.", rv.value_price ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " rv.insert_time ".$row['dir'] : $order_query.", rv.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 0) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " quantity ".$row['dir'] : $order_query.", quantity ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " value_price ".$row['dir'] : $order_query.", value_price ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $rb_record_list = $this->Api_Model->get_datatables_list(TBL_RB_VOUCHER . " rv", "rv.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $rb_record_list = $this->Api_Model->get_datatables_list(TBL_RB_VOUCHER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($rb_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['fullname'] : "";
            $row['fullname'] = $member_fullname;
            if($member_fullname != ""){
                $result['data'][] = $row;
            }
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_mms_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_MMS_REPORT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = mms.user_id";
            if($company_id == 0){
                $where_query = array('mms.active' => 1);
            }else{
                $where_query = array('mms.active' => 1, 'mms.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_MMS_REPORT . " mms", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " mms.level ".$row['dir'] : $order_query.", mms.level ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " mms.bonus ".$row['dir'] : $order_query.", mms.bonus ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " mms.insert_time ".$row['dir'] : $order_query.", mms.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " level ".$row['dir'] : $order_query.", level ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " bonus ".$row['dir'] : $order_query.", bonus ".$row['dir'];
                    }else if ($row['column'] == 4) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $rb_record_list = $this->Api_Model->get_datatables_list(TBL_MMS_REPORT . " mms", "mms.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $rb_record_list = $this->Api_Model->get_datatables_list(TBL_MMS_REPORT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($rb_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $from_member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['from_user_id'], 'active' => 1));
            $from_member_fullname = isset($from_member_info['id']) ? $from_member_info['fullname'] : "";

            $to_member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $to_member_fullname = isset($to_member_info['id']) ? $to_member_info['fullname'] : "";
            $row['from_member'] = $from_member_fullname;
            $row['to_member'] = $to_member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function get_cb_record(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        if($company_id == 0){
            $where_query = array('active' => 1);
        }else{
            $where_query = array('active' => 1, 'company_id' => $company_id);
        }
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_CB_POINT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('u.fullname' => $search);
            $left_join_query = "u.id = cb.user_id";
            if($company_id == 0){
                $where_query = array('cb.active' => 1);
            }else{
                $where_query = array('cb.active' => 1, 'cb.company_id' => $company_id);
            }
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_CB_POINT . " cb", $where_query, $where_group_like_query, $where_group_or_like_query, array(), TBL_USER . " u", $left_join_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " insert_time DESC";
        }else{
            foreach ($order as $row) {
                if($search != ""){
                    if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " u.fullname ".$row['dir'] : $order_query.", u.fullname ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " cb.credit ".$row['dir'] : $order_query.", cb.credit ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " cb.insert_time ".$row['dir'] : $order_query.", cb.insert_time ".$row['dir'];
                    }
                }else{
                    if ($row['column'] == 1) {
                        $order_query = $order_query == "" ? " user_id ".$row['dir'] : $order_query.", user_id ".$row['dir'];
                    }else if ($row['column'] == 2) {
                        $order_query = $order_query == "" ? " credit ".$row['dir'] : $order_query.", credit ".$row['dir'];
                    }else if ($row['column'] == 3) {
                        $order_query = $order_query == "" ? " insert_time ".$row['dir'] : $order_query.", insert_time ".$row['dir'];
                    }
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        if ($search != "") {
            $cb_record_list = $this->Api_Model->get_datatables_list(TBL_CB_POINT . " cb", "cb.*, u.fullname, u.id", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count, array(), TBL_USER . " u", $left_join_query);
        }else{
            $cb_record_list = $this->Api_Model->get_datatables_list(TBL_CB_POINT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        }
        
        foreach ($cb_record_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $row['user_id'], 'active' => 1));
            $member_fullname = isset($member_info['id']) ? $member_info['username'] : "";

            $row['member_name'] = $member_fullname;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }
}
