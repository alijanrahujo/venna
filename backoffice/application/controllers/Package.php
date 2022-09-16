<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/package_list");
    }

    public function view(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('active' => 1, 'id' => $id));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";
        if($company_type == "FIXED"){
            $this->check_is_fake_data($company_info, $this->page_data, "package_list", "Company");
        }else{
            $this->check_is_fake_data($company_info, $this->page_data, "dynamic_package_list", "Company");
        }
    }

    public function add(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $this->page_data['country'] = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('company_id' => $id, 'active' => 1));
        $this->page_data['product'] = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $id, 'active' => 1, 'is_promotion' => 1));
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('active' => 1, 'id' => $id));
        $this->check_is_fake_data($company_info, $this->page_data, "add_package", "Package/view/" . $this->uri->segment(3));
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $id, 'active' => 1));
        $this->page_data['edit'] = $package_info;
        $this->check_is_fake_data($package_info, $this->page_data, "edit_package", "Package/view/" . $this->uri->segment(3));
    }

    public function get_package(){
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
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " quantity ".$row['dir'] : $order_query.", quantity ".$row['dir'];
                }else if ($row['column'] == 3) {
                    $order_query = $order_query == "" ? " unit_price ".$row['dir'] : $order_query.", unit_price ".$row['dir'];
                }else if ($row['column'] == 4) {
                    $order_query = $order_query == "" ? " grand_total ".$row['dir'] : $order_query.", grand_total ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $package_list = $this->Api_Model->get_datatables_list(TBL_PACKAGE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);

        foreach ($package_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, name, code", array('id' => $row['country_id'], 'active' => 1));
            $country_name = isset($country_info['id']) ? $country_info['name'] : "";
            $currency_code = isset($country_info['id']) ? $country_info['code'] : "";
            $row['unit_price'] = $currency_code . $row['unit_price'];
            $row['grand_total'] = $currency_code . $row['grand_total'];
            $row['country_name'] = $country_name;
            $btn = '';
            $btn .= "<a href='" . site_url() . "Package/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . "Edit" . "</a> <a href='#' onclick='delete_package(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_package(){
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $english_name = isset($this->request_data['english_name']) ? $this->request_data['english_name'] : "";
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $free_quantity = isset($this->request_data['free_quantity']) ? $this->request_data['free_quantity'] : 0;
        $unit_price = isset($this->request_data['unit_price']) ? $this->request_data['unit_price'] : 0;
        $grand_total = isset($this->request_data['grand_total']) ? $this->request_data['grand_total'] : 0;
        $box = isset($this->request_data['box']) ? $this->request_data['box'] : 0;
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $is_company = isset($this->request_data['is_company']) ? $this->request_data['is_company'] : 0;
        $break_away = isset($this->request_data['break_away']) ? $this->request_data['break_away'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $product_id_arr = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : [];

        if($country_id != 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            if(isset($company_info['id']) && $company_info['id'] > 0){
                $data = array(
                    'country_id' => $country_id,
                    'company_id' => $company_id,
                    'name' => $name,
                    'english_name' => $english_name,
                    'quantity' => $quantity,
                    'free_quantity' => $free_quantity,
                    'unit_price' => $unit_price,
                    'grand_total' => $grand_total,
                    'box' => $box,
                    'unit' => $unit,
                    'is_company' => $is_company,
                    'break_away' => $break_away
                );

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
                    $package_id = $this->Api_Model->insert_data(TBL_PACKAGE, $data);
    
                    $data['response_data'] = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($company_id));
                    $this->load->view("output/success_response", $data);
                }else{
                    if($is_empty_price){
                        $data['message'] = "Empty Package Price !";
                        $this->load->view("output/error_response", $data);
                    }else if($is_empty_quantity){
                        $data['message'] = "Empty Package Quantity !";
                        $this->load->view("output/error_response", $data);
                    }else{
                        $package_id = $this->Api_Model->insert_data(TBL_PACKAGE, $data);
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
                                    'package_id' => $package_id,
                                    'product_id' => $row_voucher_package['product_id'],
                                    'quantity' => $row_voucher_package['quantity'],
                                    'price' => $row_voucher_package['price']
                                );
    
                                $this->Api_Model->insert_data(TBL_PACKAGE_VOUCHER, $data_voucher_package);
                            }
                        }

                        $data['response_data'] = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($company_id));
                        $this->load->view("output/success_response", $data);
                    }
                }
            }else{
                $data['message'] = "Invalid Company !";
                $this->load->view("output/error_response", $data);
            }
        }else{
            $data['message'] = "Invalid Country !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function edit_package(){
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $english_name = isset($this->request_data['english_name']) ? $this->request_data['english_name'] : "";
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $free_quantity = isset($this->request_data['free_quantity']) ? $this->request_data['free_quantity'] : 0;
        $unit_price = isset($this->request_data['unit_price']) ? $this->request_data['unit_price'] : 0;
        $grand_total = isset($this->request_data['grand_total']) ? $this->request_data['grand_total'] : 0;
        $box = isset($this->request_data['box']) ? $this->request_data['box'] : 0;
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $unit = isset($this->request_data['unit']) ? $this->request_data['unit'] : "";
        $is_company = isset($this->request_data['is_company']) ? $this->request_data['is_company'] : 0;
        $break_away = isset($this->request_data['break_away']) ? $this->request_data['break_away'] : 0;
        $attr = isset($this->request_data['attr']) ? $this->request_data['attr'] : [];
        $product_id_arr = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : [];

        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
        if(isset($package_info['id']) && $package_info['id'] > 0){
            $data_package = array(
                'name' => $name,
                'english_name' => $english_name,
                'quantity' => $quantity,
                'free_quantity' => $free_quantity,
                'unit_price' => $unit_price,
                'grand_total' => $grand_total,
                'box' => $box,
                'unit' => $unit,
                'is_company' => $is_company,
                'break_away' => $break_away
            );

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
                // clear all to active 0
                $data_clear_voucher_package = array(
                    'active' => 0
                );
                $this->Api_Model->update_multiple_data(TBL_PACKAGE_VOUCHER, array('package_id' => $package_id), $data_clear_voucher_package);

                $this->Api_Model->update_data(TBL_PACKAGE, array('id' => $package_id), $data_package);

                $data['response_data'] = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($package_id));
                $this->load->view("output/success_response", $data);
            }else{
                if($is_empty_price){
                    $data['message'] = "Empty Package Price !";
                    $this->load->view("output/error_response", $data);
                }else if($is_empty_quantity){
                    $data['message'] = "Empty Package Quantity !";
                    $this->load->view("output/error_response", $data);
                }else{
                    // clear all to active 0
                    $data_clear_voucher_package = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_multiple_data(TBL_PACKAGE_VOUCHER, array('package_id' => $package_id), $data_clear_voucher_package);

                    $this->Api_Model->update_data(TBL_PACKAGE, array('id' => $package_id), $data_package);
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
                                'product_id' => $row_voucher_package['product_id'],
                                'quantity' => $row_voucher_package['quantity'],
                                'price' => $row_voucher_package['price'],
                                'active' => 1
                            );

                            $voucher_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE_VOUCHER, "*", array('package_id' => $package_id, 'product_id' => $row_voucher_package['product_id']));
                            if(isset($voucher_package_info['id']) && $voucher_package_info['id'] > 0){
                                $voucher_id = $voucher_package_info['id'];
                                $this->Api_Model->update_data(TBL_PACKAGE_VOUCHER, array('id' => $voucher_id, 'package_id' => $package_id), $data_voucher_package);
                            }else{
                                $data_voucher_package['package_id'] = $package_id;
                                $this->Api_Model->insert_data(TBL_PACKAGE_VOUCHER, $data_voucher_package);
                            }
                        }
                    }

                    $data['response_data'] = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($package_id));
                    $this->load->view("output/success_response", $data);
                }
            }
        }else{
            $data['message'] = "Invalid Package !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function delete_package(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_PACKAGE, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }
}
