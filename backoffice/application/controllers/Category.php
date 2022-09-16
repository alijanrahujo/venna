<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/category_list");
    }

    public function add(){
        $this->load(ADMIN_URL . "/add_category");
    }

    public function edit(){
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_CATEGORY, "*", array('active' => 1, 'id' => $this->uri->segment(3)));
        $this->load(ADMIN_URL . "/edit_category", $this->page_data);
    }

    public function get_category(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_CATEGORY, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_CATEGORY, $where_query, $where_group_like_query, $where_group_or_like_query);
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
        $category_list = $this->Api_Model->get_datatables_list(TBL_CATEGORY, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($category_list as $row) {
            $counting++;
            $row['count'] = $counting;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Category/edit/" . $row['id'] . "' class='btn-sm btn-info' style='border:none;'>" . "Edit" . "</a> <a href='#' onclick='delete_category(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Delete" . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_category(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";

        $data_category = array(
            'company_id' => $company_id,
            'name' => $name
        );
        $this->Api_Model->insert_data(TBL_CATEGORY, $data_category);

        $json['response_data'] = $data_category;
	    $this->load->view("output/success_response", $json);
    }

    public function update_category(){
        $category_id = isset($this->request_data['category_id']) ? $this->request_data['category_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";

        $data_category = array(
            'name' => $name
        );
        $this->Api_Model->update_data(TBL_CATEGORY, array('id' => $category_id, 'active' => 1), $data_category);

        $json['response_data'] = $data_category;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_category(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_CATEGORY, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }
}
