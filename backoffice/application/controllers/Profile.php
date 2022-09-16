<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $user_id = $this->user_profile_info['id'];
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
        $this->load(ADMIN_URL . "/edit_profile", $this->page_data);
    }

    public function password(){
        $this->load(ADMIN_URL . "/change_password");
    }

    public function update_password(){
        //get data
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $old_password = isset($this->request_data['old_password']) ? $this->request_data['old_password'] : "";
        $new_password = isset($this->request_data['new_password']) ? $this->request_data['new_password'] : "";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "";
        
        //validate password format
        $validate_new_password = validate_password($new_password);
        $validate_cfm_password = validate_password($cfm_password);
        if($validate_new_password == "Password must be at least " && $validate_cfm_password == "Password must be at least "){
            //get user info for checking the password
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
            //if data exist
            if(isset($user_info['id']) && $user_info['id'] > 0){
                //get database password
                $user_password = $user_info['password'];
                //verify password with database
                if(password_verify($old_password, $user_password)){
                    //verify both password is same ?
                    if($new_password == $cfm_password){
                        $cfm_password = password_hash($cfm_password,PASSWORD_DEFAULT);
                        $data = array(
                            'password' => $cfm_password
                        );
                        //update password
                        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id), $data);
                        $this->load->view("output/success_response");
                    }else{
                        //prompt out error
                        $data['message'] = "Both Password are not same !";
                        $this->load->view("output/error_response", $data);
                    }
                }else{
                    $data['message'] = "Incorrect Old Password !";
                    $this->load->view("output/error_response", $data);
                }
            }else{
                //prompt error message
                $data['message'] = $this->lang->line("empty_message");
                $this->load->view("output/error_response", $data);
            }
        }else if($validate_new_password != "Password must be at least " && $validate_cfm_password == "Password must be at least "){
            $data['message'] = $validate_new_password;
            $this->load->view("output/error_response", $data);
        }else{
            $data['message'] = $validate_cfm_password;
            $this->load->view("output/error_response", $data);
        }
    }
}
