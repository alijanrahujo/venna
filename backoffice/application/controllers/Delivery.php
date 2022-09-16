<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delivery extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/delivery_list");
    }

    public function add(){
        $company_id = $this->user_profile_info['company_id'];
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $this->page_data['country'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $company_id, 'active' => 1));
        $this->load(ADMIN_URL . "/add_delivery_fee", $this->page_data);
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "*", array('active' => 1, 'id' => $id));
        $company_id = isset($edit_info['id']) ? $edit_info['company_id'] : 0;
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $this->page_data['country'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['edit'] = $edit_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_delivery_fee", "Delivery");
    }

    public function get_delivery(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_DELIVERY_FEE, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('description' => $search);
            $where_group_or_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_DELIVERY_FEE, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                    $order_query = $order_query == "" ? " country_id ".$row['dir'] : $order_query.", country_id ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " type ".$row['dir'] : $order_query.", type ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " region ".$row['dir'] : $order_query.", region ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }else if ($row['column'] == 5) {
                    $order_query = $order_query == "" ? " start ".$row['dir'] : $order_query.", start ".$row['dir'];
                }else if ($row['column'] == 6) {
                    $order_query = $order_query == "" ? " end ".$row['dir'] : $order_query.", end ".$row['dir'];
                }else if ($row['column'] == 7) {
                    $order_query = $order_query == "" ? " price ".$row['dir'] : $order_query.", price ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $delivery_list = $this->Api_Model->get_datatables_list(TBL_DELIVERY_FEE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($delivery_list as $row) {
            $counting++;
            $row['count'] = $counting;
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, name", array('id' => $row['country_id'], 'active' => 1));
            $country_name = isset($country_info['id']) ? $country_info['name'] : "";
            $row['country_id'] = $country_name;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Delivery/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_delivery(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_delivery(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : 0;
        $region = isset($this->request_data['region']) ? $this->request_data['region'] : 0;
        $delivery_company = isset($this->request_data['delivery_company']) ? $this->request_data['delivery_company'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : "";
        $end = isset($this->request_data['end']) ? $this->request_data['end'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";

        $data_delivery = array(
            'company_id' => $company_id,
            'country_id' => $country_id,
            'type' => $type,
            'region' => $region,
            'name' => $delivery_company,
            'start' => $start,
            'end' => $end,
            'price' => $price
        );
        $this->Api_Model->insert_data(TBL_DELIVERY_FEE, $data_delivery);

        $json['response_data'] = $data_delivery;
	    $this->load->view("output/success_response", $json);
    }

    public function update_delivery(){
        $delivery_id = isset($this->request_data['delivery_id']) ? $this->request_data['delivery_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : 0;
        $region = isset($this->request_data['region']) ? $this->request_data['region'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : "";
        $end = isset($this->request_data['end']) ? $this->request_data['end'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "";

        $data_delivery = array(
            'country_id' => $country_id,
            'type' => $type,
            'region' => $region,
            'name' => $name,
            'start' => $start,
            'end' => $end,
            'price' => $price
        );
        $this->Api_Model->update_data(TBL_DELIVERY_FEE, array('id' => $delivery_id, 'active' => 1), $data_delivery);

        $json['response_data'] = $data_delivery;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_delivery(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_DELIVERY_FEE, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }
}
