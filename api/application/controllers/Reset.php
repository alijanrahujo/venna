<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller
{
    public $_exclude_api_code = '3838';

    public function __construct()
	{
        parent::__construct();
        date_default_timezone_set("Asia/Kuala_Lumpur");
    }

    public function index(){
        $this->load->view("index");
    }

    public function password($reset_id = 0){
        $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*",array('id' => $reset_id, 'active' => 0));
        if(isset($reset_password_log_info['id']) && $reset_password_log_info['id'] > 0){
            $current_time = date('Y-m-d H:i:s');
            $expiry_time = $reset_password_log_info['expiry_time'];

            if($current_time > $expiry_time){
                echo "Link Expired !";
            }else{
                $data_reset_password_update = array(
                    'active' => 1
                );
                $this->Api_Model->update_data(TBL_RESET_PASSWORD_LOG, array('id' => $reset_id), $data_reset_password_update);

                $data_update = array(
                    'password' => $reset_password_log_info['data']
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $reset_password_log_info['user_id'], 'active' => 1), $data_update);

                echo "Your Password is " . $reset_password_log_info['password'] . ", please use this password to login !";
            }
        }else{
            echo "Link Not Found !";
        }
    }
}