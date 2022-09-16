<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Slider extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/slider_list");
    }

    public function add(){
        $this->load(ADMIN_URL . "/add_slider");
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_SLIDER, "*", array('active' => 1, 'id' => $id));
        $this->page_data['edit'] = $edit_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_slider", "Slider");
    }

    public function get_slider(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_SLIDER, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_SLIDER, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                    $order_query = $order_query == "" ? " type ".$row['dir'] : $order_query.", type ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $slider_list = $this->Api_Model->get_datatables_list(TBL_SLIDER, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($slider_list as $row) {
            $image = DISPLAY_PATH . "img/slider/" . $row['image'];
            $row['image'] = "<img src='" . $image . "' style='width: 200px;'>";
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;
            $btn = '';
            if($row['id'] == 1){
                $btn .= "<a href='" . site_url() . "Slider/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a>";
            }else{
                $btn .= "<a href='" . site_url() . "Slider/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_slider(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            }
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_slider(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";

        if (!empty($_FILES['file']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/slider';
            $config['allowed_types'] = '*';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/slider')) {
                @mkdir(IMAGE_PATH . './img/slider', 0777, TRUE);
            }
			$this->upload->initialize($config);
			         
            if ($this->upload->do_upload('file'))
            {
                $img = $this->upload->data();
                $this->resizingImage($img['file_name']);
                $image = $img['file_name'];
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }else{
            $image = "";
        }

        $data_slider = array(
            'type' => $type,
            'company_id' => $company_id,
            'image' => $image
        );
        $user_id = $this->Api_Model->insert_data(TBL_SLIDER, $data_slider);

        $json['response_data'] = $data_slider;
        $this->load->view("output/success_response", $json);
    }

    public function update_slider(){
        $slider_id = isset($this->request_data['slider_id']) ? $this->request_data['slider_id'] : 0;
        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($slider_id));

        if (!empty($_FILES['file']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/slider';
            $config['allowed_types'] = '*';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/slider')) {
                @mkdir(IMAGE_PATH . './img/slider', 0777, TRUE);
            }
			$this->upload->initialize($config);
			         
            if ($this->upload->do_upload('file'))
            {
                $img = $this->upload->data();
                $this->resizingImage($img['file_name']);
                $image = $img['file_name'];

                $data_slider = array(
                    'image' => $image
                );
                $user_id = $this->Api_Model->update_data(TBL_SLIDER, array('id' => $slider_id, 'active' => 1), $data_slider);
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }else{
            $image = "";
        }

        $json['response_data'] = $encrypt_id;
        $this->load->view("output/success_response", $json);
    }

    public function delete_slider(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_SLIDER, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function resizingImage($file_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/slider/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 800,
                'new_image'     => IMAGE_PATH . 'img/slider/' . $file_name
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
}
