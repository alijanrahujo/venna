<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/product_list");
    }

    public function add(){
        $company_id = $this->user_profile_info['company_id'];
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $this->page_data['currency'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['package'] = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['category'] = $this->Api_Model->get_rows(TBL_CATEGORY, "*", array('company_id' => $company_id, 'active' => 1));
        if($company_info['type'] == "FIXED"){
            $this->load(ADMIN_URL . "/add_product", $this->page_data);
        }else{
            $this->load(ADMIN_URL . "/add_dynamic_product", $this->page_data);
        }
    }

    public function promotion(){
        $this->load(ADMIN_URL . "/add_promotion");
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('active' => 1, 'id' => $id));
        $company_id = isset($edit_info['id']) ? $edit_info['company_id'] : 0;
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $this->page_data['currency'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['package'] = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['category'] = $this->Api_Model->get_rows(TBL_CATEGORY, "*", array('company_id' => $company_id, 'active' => 1));
        $this->page_data['edit'] = $edit_info;
        if($company_info['type'] == "FIXED"){
            $this->check_is_fake_data($edit_info, $this->page_data, "edit_product", "Product");
        }else{
            $this->check_is_fake_data($edit_info, $this->page_data, "edit_dynamic_product", "Product"); 
        }
    }

    public function get_product(){
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

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_PRODUCT, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('description' => $search);
            $where_group_or_like_query = array('name' => $search);
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
        }else{
            foreach ($order as $row) {
                if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " image ".$row['dir'] : $order_query.", image ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " price ".$row['dir'] : $order_query.", price ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $product_list = $this->Api_Model->get_datatables_list(TBL_PRODUCT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($product_list as $row) {
            $counting++;
            $row['count'] = $counting;
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $row['image'] = "<img src='" . DISPLAY_PATH . "img/product/" . $row['image'] . "' width='180'>";
            if($row['is_fixed'] == 0){
                $global_price = $row['price'];
                $currency_list = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $row['company_id'], 'active' => 1));
                $price = "";
                foreach($currency_list as $row_currency){
                    $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('currency_id' => $row_currency['id'], 'company_id' => $row['company_id'], 'product_id' => $row['id'], 'active' => 1));
                    $price .= "<span>" . $row_currency['name'] . "零售价: " . $global_price_info['price'] . "</span><br>";
                }
                $package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('company_id' => $row['company_id'], 'active' => 1));
                foreach($package_list as $row_package){
                    $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $row['company_id'], 'product_id' => $row['id'], 'package_id' => $row_package['id'], 'active' => 1));
                    $price .= "<span>" . $row_package['name'] . ": " . $product_price_info['price'] . "</span><br>";
                }
                $row['price'] = $price;
            }
            $btn = '';
            if($row['is_active'] == 0){
                $btn .= "<a href='#' onclick='enable_product(" . $row['id'] . ")' class='btn-sm btn-success' style='border:none;'>" . "Enable" . "</a> ";
            }else{
                $btn .= "<a href='#' onclick='disable_product(" . $row['id'] . ")' class='btn-sm btn-dark' style='border:none;'>" . "Disabled" . "</a> ";
            }
            $btn .= "<a href='" . site_url() . "Product/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . "Edit" . "</a> <a href='#' onclick='delete_product(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Delete" . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function manage_product(){
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $is_active = isset($this->request_data['is_active']) ? $this->request_data['is_active'] : 0;

        $data = array(
            'is_active' => $is_active
        );
        $this->Api_Model->update_data(TBL_PRODUCT, array('id' => $product_id, 'active' => 1), $data);

        $this->load->view("output/success_response");
    }

    public function insert_product(){
        $insert_by = isset($this->request_data['insert_by']) ? $this->request_data['insert_by'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $category_id = isset($this->request_data['category_id']) ? $this->request_data['category_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "0.00";
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";
        $random_no = round(microtime(true) * 1000);
        $rndcode = date("Ymdhis") . $random_no;
        $is_normal = isset($this->request_data['is_normal']) ? $this->request_data['is_normal'] : 0;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        if($price == "0.00" || empty($price)){
            $global_price = $company_info['price'];
        }else{
            $global_price = $price;
        }

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/product';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/product')) {
                @mkdir(IMAGE_PATH . './img/product', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('Image'))
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

        $data_product = array(
            'company_id' => $company_id,
            'rndcode' => $rndcode,
            'category_id' => $category_id,
            'name' => $name,
            'price' => $global_price,
            'content' => $content,
            'description' => $description,
            'image' => $image,
            'is_fixed' => 1,
            'unit' => $unit,
            'is_normal' => $is_normal,
            'is_promotion' => $is_promotion,
            'insert_by' => $insert_by
        );
        $this->Api_Model->insert_data(TBL_PRODUCT, $data_product);

        $json['response_data'] = $data_product;
	    $this->load->view("output/success_response", $json);
    }

    public function insert_dynamic_product(){
        $insert_by = isset($this->request_data['insert_by']) ? $this->request_data['insert_by'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $category_id = isset($this->request_data['category_id']) ? $this->request_data['category_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";
        $pv_price = isset($this->request_data['pv_price']) ? $this->request_data['pv_price'] : "0.00";
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";
        $package_arr = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : [];
        $currency_arr = isset($this->request_data['currency_id']) ? $this->request_data['currency_id'] : [];
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $random_no = round(microtime(true) * 1000);
        $rndcode = date("Ymdhis") . $random_no;
        $is_normal = isset($this->request_data['is_normal']) ? $this->request_data['is_normal'] : 0;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        $global_price = isset($this->request_data['price']) ? $this->request_data['price'] : "";

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/product';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/product')) {
                @mkdir(IMAGE_PATH . './img/product', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('Image'))
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

        if(!empty($package_arr)){
            foreach($package_arr as $package_id){
                $package_product_price = isset($attr[$package_id]['price']) ? $attr[$package_id]['price'] : "";

                $data_product_price[] = array(
                    'company_id' => $company_id,
                    'package_id' => $package_id,
                    'price' => $package_product_price,
                    'pv_price' => $pv_price
                );
            }
        }

        $data_product = array(
            'company_id' => $company_id,
            'rndcode' => $rndcode,
            'category_id' => $category_id,
            'name' => $name,
            'content' => $content,
            'description' => $description,
            'image' => $image,
            'unit' => $unit,
            'is_normal' => $is_normal,
            'is_promotion' => $is_promotion,
            'insert_by' => $insert_by
        );
        $product_id = $this->Api_Model->insert_data(TBL_PRODUCT, $data_product);

        if(!empty($currency_arr)){
            foreach($currency_arr as $currency_id){
                $global_selling_price = isset($attr[$currency_id]['global_price']) ? $attr[$currency_id]['global_price'] : "";
                $currency_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $currency_id, 'active' => 1));
                $global_price = $global_selling_price * $currency_info['exchange_rate'];
                $number_format_price = number_format($global_price, 2);
                $global_price = str_replace(",", "", $number_format_price);

                $data_global_price = array(
                    'company_id' => $company_id,
                    'currency_id' => $currency_id,
                    'product_id' => $product_id,
                    'price' => $global_price,
                    'pv_price' => $pv_price
                );
                $this->Api_Model->insert_data(TBL_GLOBAL_PRICE, $data_global_price);
            }
        }

        if(!empty($data_product_price)){
            foreach($data_product_price as $row_product_price){
                $data_price = array(
                    'company_id' => $row_product_price['company_id'],
                    'package_id' => $row_product_price['package_id'],
                    'product_id' => $product_id,
                    'price' => $row_product_price['price'],
                    'pv_price' => $row_product_price['pv_price']
                );
                $this->Api_Model->insert_data(TBL_PRODUCT_PRICE, $data_price);
            }
        }

        $json['response_data'] = $data_product;
	    $this->load->view("output/success_response", $json);
    }

    public function update_product(){
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $category_id = isset($this->request_data['category_id']) ? $this->request_data['category_id'] : 1;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $price = isset($this->request_data['price']) ? $this->request_data['price'] : "0.00";
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";
        $is_normal = isset($this->request_data['is_normal']) ? $this->request_data['is_normal'] : 0;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;
        $update_by = isset($this->request_data['update_by']) ? $this->request_data['update_by'] : "";

        $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $product_id, 'active' => 1));
        $company_id = isset($product_info['id']) ? $product_info['company_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        if($price == "0.00"){
            $global_price = $company_info['price'];
        }else{
            $global_price = $price;
        }

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/product';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/product')) {
                @mkdir(IMAGE_PATH . './img/product', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('Image'))
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

        $data_product = array(
            'category_id' => $category_id,
            'name' => $name,
            'price' => $global_price,
            'content' => $content,
            'description' => $description,
            'unit' => $unit,
            'is_normal' => $is_normal,
            'is_promotion' => $is_promotion,
            'update_time' => $this->update_time,
            'update_by' => $update_by
        );

        if($image != ""){
            $data_product['image'] = $image;
        }
        $this->Api_Model->update_data(TBL_PRODUCT, array('id' => $product_id, 'active' => 1), $data_product);

        $json['response_data'] = $data_product;
	    $this->load->view("output/success_response", $json);
    }

    public function update_dynamic_product(){
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $category_id = isset($this->request_data['category_id']) ? $this->request_data['category_id'] : 1;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";
        $description = isset($this->request_data['description']) ? $this->request_data['description'] : "";
        $update_by = isset($this->request_data['update_by']) ? $this->request_data['update_by'] : "";
        $product_price_arr = isset($this->request_data['product_price_id']) ? $this->request_data['product_price_id'] : [];
        $global_price_arr = isset($this->request_data['global_price_id']) ? $this->request_data['global_price_id'] : [];
        $pv_price = isset($this->request_data['pv_price']) ? $this->request_data['pv_price'] : "0.00";
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $is_normal = isset($this->request_data['is_normal']) ? $this->request_data['is_normal'] : 0;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;

        $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $product_id, 'active' => 1));
        $company_id = isset($product_info['id']) ? $product_info['company_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        $global_price = isset($this->request_data['price']) ? $this->request_data['price'] : "";

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/product';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/product')) {
                @mkdir(IMAGE_PATH . './img/product', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('Image'))
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

        if(!empty($product_price_arr)){
            foreach($product_price_arr as $product_price_id){
                $package_product_price = isset($attr[$product_price_id]['price']) ? $attr[$product_price_id]['price'] : "";

                $data_product_price[] = array(
                    'id' => $product_price_id,
                    'price' => $package_product_price
                );
            }
        }

        $data_product = array(
            'category_id' => $category_id,
            'name' => $name,
            'price' => $global_price,
            'content' => $content,
            'description' => $description,
            'unit' => $unit,
            'is_normal' => $is_normal,
            'is_promotion' => $is_promotion,
            'update_time' => $this->update_time,
            'update_by' => $update_by
        );

        if($image != ""){
            $data_product['image'] = $image;
        }
        $this->Api_Model->update_data(TBL_PRODUCT, array('id' => $product_id, 'active' => 1), $data_product);

        $data_pv_price = array(
            'pv_price' => $pv_price
        );
        $this->Api_Model->update_data(TBL_GLOBAL_PRICE, array('product_id' => $product_id, 'active' => 1, 'company_id' => $company_id), $data_pv_price);
        $this->Api_Model->update_multiple_data(TBL_PRODUCT_PRICE, array('product_id' => $product_id, 'active' => 1, 'company_id' => $company_id), $data_pv_price);

        if(!empty($global_price_arr)){
            foreach($global_price_arr as $global_price_id){
                $global_selling_price = isset($attr[$global_price_id]['global_price']) ? $attr[$global_price_id]['global_price'] : "";

                $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('id' => $global_price_id, 'active' => 1));
                $currency_id = isset($global_price_info['id']) ? $global_price_info['currency_id'] : 0;

                $currency_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $currency_id, 'active' => 1));
                $global_price = $global_selling_price * $currency_info['exchange_rate'];
                $number_format_price = number_format($global_price, 2);
                $global_price = str_replace(",", "", $number_format_price);

                $data_global_price = array(
                    'price' => $global_price
                );
                $this->Api_Model->update_data(TBL_GLOBAL_PRICE, array('id' => $global_price_id, 'active' => 1), $data_global_price);
            }
        }

        if(!empty($data_product_price)){
            foreach($data_product_price as $row_product_price){
                $data_price = array(
                    'price' => $row_product_price['price']
                );
                $this->Api_Model->update_data(TBL_PRODUCT_PRICE, array('id' => $row_product_price['id']), $data_price);
            }
        }

        $json['response_data'] = $data_product;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_product(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_PRODUCT, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function insert_promotion(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $unit_price = isset($this->request_data['unit_price']) ? $this->request_data['unit_price'] : "0.00";
        $promotion_price = isset($this->request_data['promotion_price']) ? $this->request_data['promotion_price'] : "0.00";
        $purchase_quantity = isset($this->request_data['purchase_quantity']) ? $this->request_data['purchase_quantity'] : 0;
        $free_quantity = isset($this->request_data['free_quantity']) ? $this->request_data['free_quantity'] : 0;

        $data_promotion = array(
            'company_id' => $company_id,
            'name' => $name,
            'unit_price' => $unit_price,
            'promotion_price' => $promotion_price,
            'purchase_quantity' => $purchase_quantity,
            'free_quantity' => $free_quantity
        );
        $this->Api_Model->insert_data(TBL_PROMOTION, $data_promotion);

        $this->load->view("output/success_response");
    }

    public function optimize_desc_image(){
        if (!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/product';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '10000'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/product')) {
                @mkdir(IMAGE_PATH . './img/product', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('image'))
            {
                $img = $this->upload->data();
                $image = $img['file_name']; 

                $json['response_data'] = DISPLAY_PATH . "img/product/" . $image;
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
                'source_image'  => IMAGE_PATH . 'img/product/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 1080,
                'new_image'     => IMAGE_PATH . 'img/product/' . $file_name
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
