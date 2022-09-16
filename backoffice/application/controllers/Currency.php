<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Currency extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function view(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('active' => 1, 'id' => $id));
        $this->check_is_fake_data($company_info, $this->page_data, "currency_list", "Company");
    }

    public function add(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('active' => 1, 'id' => $id));
        $this->check_is_fake_data($company_info, $this->page_data, "add_currency", "Currency/view/" . $this->uri->segment(3));
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('active' => 1, 'id' => $id));
        $this->page_data['edit'] = $edit_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_currency", "Currency/view/" . $this->uri->segment(3));
    }

    public function get_currency(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_CURRENCY, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_CURRENCY, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " code ".$row['dir'] : $order_query.", code ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " exchange_rate ".$row['dir'] : $order_query.", exchange_rate ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $currency_list = $this->Api_Model->get_datatables_list(TBL_CURRENCY, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($currency_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $row['exchange_rate'] = $row['exchange_rate'] . " " . $row['code'];
            $btn = '';
            $btn .= "<a href='" . site_url() . "Currency/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_currency(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_currency(){
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $code = isset($this->request_data['code']) ? $this->request_data['code'] : "";
        $exchange_rate = isset($this->request_data['exchange_rate']) ? $this->request_data['exchange_rate'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $data = array(
            'company_id' => $company_id,
            'name' => $name,
            'code' => $code,
            'exchange_rate' => $exchange_rate
        );
        if($exchange_rate < 1){
            $data['type'] = 1;
        }else{
            $data['type'] = 2;
        }
        $this->Api_Model->insert_data(TBL_CURRENCY, $data);

        $json['response_data'] = $data;
	    $this->load->view("output/success_response", $json);
    }

    public function update_currency(){
        $currency_id = isset($this->request_data['currency_id']) ? $this->request_data['currency_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $code = isset($this->request_data['code']) ? $this->request_data['code'] : "";
        $exchange_rate = isset($this->request_data['exchange_rate']) ? $this->request_data['exchange_rate'] : "";

        $data = array(
            'name' => $name,
            'code' => $code,
            'exchange_rate' => $exchange_rate
        );
        $this->Api_Model->update_data(TBL_CURRENCY, array('id' => $currency_id, 'active' => 1), $data);

        $currency_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('active' => 1, 'id' => $currency_id));
        $company_id = isset($currency_info['id']) ? $currency_info['company_id'] : 0;
        $data['company_id'] = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($company_id));

        $json['response_data'] = $data;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_currency(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_CURRENCY, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }
}
