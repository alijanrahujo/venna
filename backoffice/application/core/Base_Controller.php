<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: OPTIONS, HEAD, GET");
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_Controller extends CI_Controller {
    public $execlude_class = array(
        'Cron',
        'Withdraw',
        'Category',
        'Purchase',

        'Company',
        'Package',
        'Profile',
        'Admin',
        'Common',
        'Dashboard',
        'Member',
        'Order',
        'Product',
        'Currency',
        'Voucher',
        'Gallery',
        'Slider',
        'Delivery',
        'Bonus',
        'Report',
		'Course'
    );

    public $user_data_keys = array(
        'user_id', 'email', 'is_user_login', 'group_id', 'access_token', 'user_type'
    );

    public function __construct($_class_name = "")
	{
        parent::__construct();
        $this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('base');
        $this->load->library('session');
        $this->load->library('encryption');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->request_data = $_REQUEST;
        $this->page_data = array();
        $this->update_time = date('Y-m-d H:i:s');
        date_default_timezone_set("Asia/Kuala_Lumpur");

        $access_token = $this->session->userdata('access_token');

        if(!in_array($_class_name, $this->execlude_class)){
            if(!$this->session->userdata('user_id')){
                $this->unsetUserData();
            }
        }else{
            $user_id = ($this->session->userdata('user_id')) ? $this->session->userdata('user_id') : 0;
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
            $this->user_profile_info = $user_info;
        }
    }

    // load view
	protected function load($view, $data = "")
    {
        $this->load->view('include/header', $data);
        $this->load->view($view, $data);
        $this->load->view('include/footer', $data);
    }

    public function unsetUserData(){
        $array_items = array_keys($this->user_data_keys);
        $this->session->unset_userdata($array_items);
    }

    public function check_is_fake_data($param, $data = "", $load_page = "", $redirect = ""){
        if(isset($param['id']) && $param['id'] > 0){
            $this->load(ADMIN_URL . "/$load_page", $data);
        }else{
            redirect(site_url() . $redirect, "refresh");
        }
    }
}