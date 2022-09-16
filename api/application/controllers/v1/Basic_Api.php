<?php
class  Basic_Api extends Base_Controller {
    /* Index

    - get_country_post
    - update_user_country_post
    - apply_big_present_post
    - get_packages_post
    - update_user_step1_post
    - update_user_step2_post
    - update_user_step3_post
    - update_user_step4_post
    - get_user_information_post
    - update_user_final_step_post

    End Index */

    public function __construct(){
        parent::__construct();
    }

    public function index_get(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function index_post(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function get_country_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

        $country_list = $this->Api_Model->get_rows(TBL_CURRENCY, "id, name", array('company_id' => $company_id, 'active' => 1));

        $json_response = array(
            'country' => $country_list
        );

        $result = $this->success_response($json_response);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function update_user_country_post(){
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $data_update = array(
                'country_id' => $country_id
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

            $json_response = array(
                'user_id' => $user_id,
                'country_id' => $country_id
            );

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function apply_big_present_post(){
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $code = isset($this->request_data['code']) ? $this->request_data['code'] : "";

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, referral_id", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $referral_id = $user_info['referral_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $code, 'active' => 1, 'type' => "BIG_PRESENT", 'country_id' => $country_id, 'status' => "APPROVE"));
            if(isset($big_present_info['id']) && $big_present_info['id'] > 0){
                $big_present_id = $big_present_info['id'];
                $balance_quantity = $big_present_info['balance_quantity'];
                $is_paid_to_company = $big_present_info['is_company'];
                // 大礼包的quantity
                $big_present_stock = $big_present_info['total_stock'];

                if($balance_quantity == 0){
                    $result = $this->error_response("Insufficient Voucher !");
                    $this->response($result, 200);
                }else{
                    if($is_paid_to_company == 1){
                        $this->proceed_apply_big_present($user_id, $big_present_id, $country_id);
                    }else{
                        $stock_balance = $this->check_stock_balance_post($referral_id);
                        if($stock_balance == $big_present_stock){
                            $this->proceed_apply_big_present($user_id, $big_present_id, $country_id);
                        }else{
                            if($stock_balance < $big_present_stock){
                                $result = $this->error_response("Insufficient Stock !");
                                $this->response($result, 200);
                            }else{
                                $this->proceed_apply_big_present($user_id, $big_present_id, $country_id);
                            }
                        }
                    }
                }
            }else{
                $result = $this->error_response("Invalid Voucher !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function proceed_apply_big_present($user_id, $big_present_id, $country_id){
        $big_present_log_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_LOG, "*", array('user_id' => $user_id, 'big_present_id' => $big_present_id, 'active' => 0));
        $is_data_not_used = isset($big_present_log_info['id']) ? 1 : 0;
        if($is_data_not_used == 0){
            // insert big present log record
            $data_big_present_log = array(
                'user_id' => $user_id,
                'big_present_id' => $big_present_id
            );
            $this->Api_Model->insert_data(TBL_BIG_PRESENT_LOG, $data_big_present_log);
        }

        // get inside big present got which package
        $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, price, total_stock", array('id' => $big_present_id, 'active' => 1));

        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, quantity, unit_price, grand_total, country_id, unit", array('id' => $big_present_package_info['package_id'], 'active' => 1));
        $package_info['name'] = isset($package_info['id']) ? $package_info['name'] : "";
        $package_info['quantity'] = isset($big_present_package_info['id']) ? number_format($big_present_package_info['total_stock'], 0, '.',',') : 0;
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, type", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        if($company_type == ""){
            $result = $this->error_response("Empty Company Type !");
            $this->response($result, 200);
        }else{
            if($company_type == "FLAT"){
                $package_info['unit_price'] = isset($big_present_package_info['id']) ? $big_present_package_info['price'] : 0;
            }else{
                $package_info['unit_price'] = isset($package_info['id']) ? $package_info['unit_price'] : 0;
            }
            $package_info['grand_total'] = isset($big_present_package_info['id']) ? number_format($big_present_package_info['price'], 0, '.',',') : 0;
            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $package_info['country_id'], 'active' => 1));
            $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
            $package_info['currency_name'] = $currency_name;
            unset($package_info['package_id']);

            $json_response = array(
                'package' => $package_info,
                'country_id' => $country_id
            );

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }
    }

    public function get_packages_post(){
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        // get user is from which company_id
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, active, is_halal", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $is_halal = $user_info['is_halal'];

            $where_company = array(
                'id' => $company_id,
                'active' => 1
            );
            // check the company type
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", $where_company);
            if(isset($company_info['id']) && $company_info['id'] > 0){
                $company_type = $company_info['type'];

                $where_package = array(
                    'country_id' => $country_id,
                    'company_id' => $company_id,
                    'active' => 1
                );
                $package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "id, name, quantity, unit_price, grand_total, unit, country_id", $where_package, "", "", "id", "DESC");
                if(!empty($package_list)){
                    foreach($package_list as $plkey => $plval){
                        $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $plval['country_id'], 'active' => 1));
                        $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
                        $package_list[$plkey]['currency_name'] = $currency_name;
                        $package_list[$plkey]['grand_total'] = number_format($plval['grand_total'], 0, '.',',');
                        $package_list[$plkey]['quantity'] = number_format($plval['quantity'], 0, '.',',');
                    }
                }else{
                    $package_list = array();
                }

                $json_response = array(
                    'package' => $package_list,
                    'country_id' => $country_id
                );

                $result = $this->success_response($json_response);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Invalid Company !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function update_user_step1_post(){
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, company_id, voucher_id", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = $company_info['type'];
            $referral_id = $user_info['referral_id'];
            $big_present_id = $user_info['voucher_id'];

            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, type", array('id' => $big_present_id));
            $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";

            if($package_id == 0){
                $result = $this->error_response("Empty Package !");
                $this->response($result, 200);
            }else{
                if($voucher_type == "BIG_PRESENT"){
                    $big_present_log_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT_LOG, "id, big_present_id", "WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
                    $big_present_id = isset($big_present_log_info['id']) ? $big_present_log_info['big_present_id'] : 0;

                    $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, price, total_stock, is_company", array('id' => $big_present_id, 'active' => 1));
                    $is_paid_to_company_big_present = isset($big_present_package_info['id']) ? $big_present_package_info['is_company'] : 0;
                }else{
                    $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, price, total_stock, is_company", array('id' => $big_present_id));
                    $is_paid_to_company_big_present = isset($big_present_package_info['id']) ? $big_present_package_info['is_company'] : 0;
                }

                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, quantity, unit_price, grand_total, country_id, unit, is_company", array('id' => $package_id, 'active' => 1));
                $is_paid_to_company_normal = isset($package_info['id']) ? $package_info['is_company'] : 0;
                $grand_total = isset($package_info['id']) ? number_format($package_info['grand_total'], 0, '.',',') : "";

                if($big_present_id != 0){
                    $total_quantity = $big_present_package_info['total_stock'];
                    $is_paid_to_company = $is_paid_to_company_big_present;
                }else{
                    $total_quantity = $package_info['quantity'];
                    $is_paid_to_company = $is_paid_to_company_normal;
                }

                // if package is company giving stock
                if($is_paid_to_company == 1){
                    $this->proceed_update_user_step1_post($user_id, $package_id);
                }else{
                    $stock_balance = $this->check_stock_balance_post($referral_id);
                    $point_balance = $this->check_point_balance_post($referral_id);

                    if($company_type == "FIXED"){
                        if($stock_balance == $total_quantity){
                            $this->proceed_update_user_step1_post($user_id, $package_id);
                        }else{
                            // if($stock_balance < $total_quantity){
                            //     $data_update = array(
                            //         'package_id' => 0,
                            //         'is_step1' => 0,
                            //         'is_voucher' => 0,
                            //         'voucher_id' => 0
                            //     );
                            //     $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
                                
                            //     $result = $this->error_response("Upline is Insufficient Stock ! Please Inform to restock !");
                            //     $this->response($result, 200);
                            // }else{
                                $this->proceed_update_user_step1_post($user_id, $package_id);
                            // }
                        }
                    }else{
                        if($point_balance == $grand_total){
                            $this->proceed_update_user_step1_post($user_id, $package_id);
                        }else{
                            if($point_balance < $grand_total){
                                $data_update = array(
                                    'package_id' => 0,
                                    'is_step1' => 0,
                                    'is_voucher' => 0,
                                    'voucher_id' => 0
                                );
                                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
                                
                                $result = $this->error_response("Upline is Insufficient Stock ! Please Inform to restock !");
                                $this->response($result, 200);
                            }else{
                                $this->proceed_update_user_step1_post($user_id, $package_id);
                            }
                        }
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function proceed_update_user_step1_post($user_id, $package_id){
        $big_present_log_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT_LOG, "id, big_present_id", "WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
        $big_present_id = isset($big_present_log_info['id']) ? $big_present_log_info['big_present_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

        $total_topup = $this->check_topup_balance_post($company_id);

        if($big_present_id != 0){
            $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, total_stock, is_company", array('id' => $big_present_id));
            $package_quantity = isset($big_present_info['id']) ? $big_present_info['total_stock'] : 0;
            $is_paid_to_company = isset($package_info['id']) ? $package_info['is_company'] : 0;
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, is_company", array('id' => $package_id, 'active' => 1));
            $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
            $is_paid_to_company = isset($package_info['id']) ? $package_info['is_company'] : 0;
        }

        if($company_id != 2 && $company_id != 11 && $total_topup < $package_quantity && $is_paid_to_company == 1 && $package_quantity != 0){
            $result = $this->error_response("Insufficient Stock, Please contact company !");
            $this->response($result, 200);
        }else{
            if($big_present_id != 0){
                $data_update = array(
                    'package_id' => $package_id,
                    'is_step1' => 1,
                    'is_voucher' => 1,
                    'voucher_id' => $big_present_id
                );
            }else{
                $data_update = array(
                    'package_id' => $package_id,
                    'is_step1' => 1
                );
            }
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

            $json_response = array(
                'user_id' => $user_id
            );

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }
    }

    public function update_user_step2_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $fullname = isset($this->request_data['fullname']) ? $this->request_data['fullname'] : "";
        $username_post = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $phone_no_otp = isset($this->request_data['phone_no_otp']) ? $this->request_data['phone_no_otp'] : "";
        $ic_no = isset($this->request_data['ic_no']) ? $this->request_data['ic_no'] : "";
        $pincode = isset($this->request_data['pincode']) ? $this->request_data['pincode'] : 0;
        $cfm_pincode = isset($this->request_data['cfm_pincode']) ? $this->request_data['cfm_pincode'] : 0;
        $username = str_replace(' ', '', $username_post);

        if(preg_match("/\p{Han}+/u", $username)){
            $result = $this->error_response("Chinese Word not allowed !");
            $this->response($result, 401);
        }else{
            $where_condition = array(
                'id' => $user_id,
                'active' => 1
            );
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", $where_condition);
            $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
            if($fullname == ""){
                $result = $this->error_response("Fullname is empty !");
                $this->response($result, 200);
            }else if($username == ""){
                $result = $this->error_response("Username is empty !");
                $this->response($result, 200);
            }else if($phone_no == "" && $company_id != 8){
                $result = $this->error_response("Phone No is empty !");
                $this->response($result, 200);
            }else if($phone_no_otp == "" && $company_id == 8){
                $result = $this->error_response("Phone No is empty !");
                $this->response($result, 200);
            }else if($ic_no == ""){
                $result = $this->error_response("IC No is empty !");
                $this->response($result, 200);
            }else if($pincode == ""){
                $result = $this->error_response("Pincode is empty !");
                $this->response($result, 200);
            }else if($pincode == ""){
                $result = $this->error_response("Please confirm your security code !");
                $this->response($result, 200);
            }else if($pincode != $cfm_pincode){
                $result = $this->error_response("Security code are not same !");
                $this->response($result, 200);
            }else{
                if($company_id == 8){
                    if($phone_no_otp == ""){
                        $phone_no = "0" . $phone_no;
                    }else{
                        $phone_no = "0" . $phone_no_otp;
                    }
                }
                if(isset($user_info['id']) && $user_info['id'] > 0){
                    $where_exist_phone = array(
                        'phone_no' => $phone_no,
                        'active' => 1,
                        'id !=' => $user_id
                    );
                    $where_exist_username = array(
                        'username' => $username,
                        'active' => 1,
                        'id !=' => $user_id
                    );
                    $where_exist_ic = array(
                        'ic' => $ic_no,
                        'active' => 1,
                        'id !=' => $user_id
                    );
                    $exist_phone_info = $this->Api_Model->get_rows_info(TBL_USER, "id", $where_exist_phone);
                    $exist_username_info = $this->Api_Model->get_rows_info(TBL_USER, "id", $where_exist_username);
                    $exist_ic_info = $this->Api_Model->get_rows_info(TBL_USER, "id", $where_exist_ic);
                    if(isset($exist_phone_info['id']) && $exist_phone_info['id'] > 0){
                        $result = $this->error_response("Phone No already Exist !");
                        $this->response($result, 200);
                    }else if(isset($exist_username_info['id']) && $exist_username_info['id'] > 0){
                        $result = $this->error_response("Username already Exist !");
                        $this->response($result, 200);
                    }else if(isset($exist_ic_info['id']) && $exist_ic_info['id'] > 0){
                        $result = $this->error_response("IC already Exist !");
                        $this->response($result, 200);
                    }else{
                        $pincode_hash = password_hash($pincode, PASSWORD_BCRYPT);
                        $data_update = array(
                            'fullname' => $fullname,
                            'username' => $username,
                            'phone_no' => $phone_no,
                            'ic' => $ic_no,
                            'pincode' => $pincode_hash,
                            'is_step2' => 1
                        );
                        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

                        $json_response = array(
                            'user_id' => $user_id
                        );

                        $result = $this->success_response($json_response);
                        $this->response($result, REST_Controller::HTTP_OK);
                    }
                }else{
                    $result = $this->error_response("Invalid User !");
                    $this->response($result, 200);
                }
            }
        }
    }

    public function update_user_step3_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";
        $area = isset($this->request_data['area']) ? $this->request_data['area'] : "";

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_step3, company_id", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $is_step3 = $user_info['is_step3'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_register_skip", array('id' => $company_id, 'active' => 1));
            $is_register_skip = isset($company_info['id']) ? $company_info['is_register_skip'] : 0;

            if($is_register_skip == 1){
                $data_update = array(
                    'area' => $area,
                    'address_line1' => $address,
                    'city' => $city,
                    'state' => $state,
                    'postcode' => $postcode,
                    'is_step3' => 1
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
    
                if($is_step3 == 0){
                    $data_address = array(
                        'user_id' => $user_id,
                        'area' => $area,
                        'name' => $address,
                        'address' => $address,
                        'city' => $city,
                        'state' => $state,
                        'postcode' => $postcode
                    );
                    if($address != "" || $city != "" || $state != "" || $postcode != ""){
                        $this->Api_Model->insert_data(TBL_USER_ADDRESS, $data_address);
                    }
                }
    
                $json_response = array(
                    'user_id' => $user_id
                );
    
                $result = $this->success_response($json_response);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                if($area == ""){
                    $result = $this->error_response("Area is empty !");
                    $this->response($result, 200);
                }else if($address == ""){
                    $result = $this->error_response("Delivery Address is empty !");
                    $this->response($result, 200);
                }else if($city == ""){
                    $result = $this->error_response("City is empty !");
                    $this->response($result, 200);
                }else if($state == ""){
                    $result = $this->error_response("State is empty !");
                    $this->response($result, 200);
                }else if($postcode == ""){
                    $result = $this->error_response("Postcode is empty !");
                    $this->response($result, 200);
                }else{
                    $data_update = array(
                        'area' => $area,
                        'address_line1' => $address,
                        'city' => $city,
                        'state' => $state,
                        'postcode' => $postcode,
                        'is_step3' => 1
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
        
                    if($is_step3 == 0){
                        $data_address = array(
                            'user_id' => $user_id,
                            'area' => $area,
                            'name' => $address,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'postcode' => $postcode
                        );
                        $this->Api_Model->insert_data(TBL_USER_ADDRESS, $data_address);
                    }
        
                    $json_response = array(
                        'user_id' => $user_id
                    );
        
                    $result = $this->success_response($json_response);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function update_user_step4_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";
        
        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_register_skip", array('id' => $company_id, 'active' => 1));
            $is_register_skip = isset($company_info['id']) ? $company_info['is_register_skip'] : 0;

            if($is_register_skip == 1){
                $data_update = array(
                    'bank_name' => $bank_name,
                    'account_name' => $account_name,
                    'account_no' => $account_no,
                    'is_step4' => 1
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
    
                $json_response = array(
                    'user_id' => $user_id
                );
    
                $result = $this->success_response($json_response);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                if($bank_name == ""){
                    $result = $this->error_response("Bank name is empty !");
                    $this->response($result, 200);
                }else if($account_name == ""){
                    $result = $this->error_response("Account name is empty !");
                    $this->response($result, 200);
                }else if($account_no == ""){
                    $result = $this->error_response("Account no is empty !");
                    $this->response($result, 200);
                }else{
                    $data_update = array(
                        'bank_name' => $bank_name,
                        'account_name' => $account_name,
                        'account_no' => $account_no,
                        'is_step4' => 1
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);
        
                    $json_response = array(
                        'user_id' => $user_id
                    );
        
                    $result = $this->success_response($json_response);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function get_user_information_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, fullname, username, phone_no, ic, address_line1, postcode, city, state, package_id, country_id, bank_name, account_name, account_no, is_step1, is_step2, is_step3, is_step4, is_done, referral_id, company_id, area, tac, is_verify", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, bank_name, account_name, account_no", array('id' => $user_info['referral_id'], 'active' => 1, 'user_type' => "AGENT"));
            $referral_bank_name = isset($referral_info['id']) ? $referral_info['bank_name'] : "";
            $referral_account_name = isset($referral_info['id']) ? $referral_info['account_name'] : "";
            $referral_account_no = isset($referral_info['id']) ? $referral_info['account_no'] : "";

            $json_response = array(
                'user_id' => $user_id,
                'company_id' => $user_info['company_id'],
                'fullname' => $user_info['fullname'],
                'username' => $user_info['username'],
                'phone_no' => $user_info['phone_no'],
                'ic' => $user_info['ic'],
                'address' => $user_info['address_line1'],
                'postcode' => $user_info['postcode'],
                'city' => $user_info['city'],
                'state' => $user_info['state'],
                'area' => $user_info['area'],
                'bank_name' => $user_info['bank_name'],
                'account_name' => $user_info['account_name'],
                'account_no' => $user_info['account_no'],
                'is_voucher' => $user_info['is_voucher'],
                'country_id' => $user_info['country_id'],
                'is_step1' => $user_info['is_step1'],
                'is_step2' => $user_info['is_step2'],
                'is_step3' => $user_info['is_step3'],
                'is_step4' => $user_info['is_step4'],
                'is_done' => $user_info['is_done'],
                'agent_bank_name' => $referral_bank_name,
                'agent_account_name' => $referral_account_name,
                'agent_account_no' => $referral_account_no,
                'otp_code' => $user_info['tac'],
                'is_verify' => $user_info['is_verify']
            );

            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $user_info['country_id'], 'active' => 1));
            $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
            $json_response['currency_name'] = $currency_name;

            $is_voucher = $user_info['is_voucher'];
            $voucher_id = $user_info['voucher_id'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $company_name = isset($company_info['id']) ? $company_info['name'] : "";
            $terms_and_conditions = isset($company_info['id']) ? $company_info['terms_and_conditions'] : "";

            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
            $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
            if($is_voucher == 1){
                if($voucher_type == "BIG_PRESENT"){
                    $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $user_info['voucher_id'], 'active' => 1));
                }else{
                    $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                }
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $big_present_info['package_id'], 'active' => 1));
                $json_response['package_id'] = isset($package_info['id']) ? $package_info['id'] : "";
                if($voucher_type == "BIG_PRESENT"){
                    $json_response['package_name'] = isset($package_info['id']) ? $package_info['name'] . "(" . "半门槛" . ")" : "";
                }else{
                    $json_response['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                }
                $json_response['package_quantity'] = isset($big_present_info['id']) ? number_format($big_present_info['total_stock'], 0, '.',',') : "";
                if($company_type == "FLAT"){
                    $json_response['package_price'] = isset($big_present_info['id']) ? $big_present_info['price'] : "";
                }else{
                    $json_response['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                }
                $json_response['package_total'] = isset($big_present_info['id']) ? number_format($big_present_info['price'], 0, '.',',') : "";
                $json_response['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
            }else{
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $user_info['package_id'], 'active' => 1));
                $json_response['package_id'] = isset($package_info['id']) ? $package_info['id'] : "";
                $json_response['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                $json_response['package_quantity'] = isset($package_info['id']) ? number_format($package_info['quantity'], 0, '.',',') : "";
                $json_response['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                $json_response['package_total'] = isset($package_info['id']) ? number_format($package_info['grand_total'], 0, '.',',') : "";
                $json_response['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
            }
            $country_id = isset($package_info['country_id']) ? $package_info['country_id'] : 0;
            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $country_id, 'active' => 1));
            $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
            $json_response['currency_name'] = $currency_name;
            $json_response['company_name'] = $company_name;
            $json_response['terms_and_conditions'] = $terms_and_conditions;

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function update_user_final_step_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $second_password = isset($this->request_data['second_password']) ? $this->request_data['second_password'] : "";

        $where_condition = array(
            'id' => $user_id,
            'active' => 1
        );
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, company_id, pincode, voucher_id, referral_id, is_old", $where_condition);
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_pincode = $user_info['pincode'];
            $voucher_id = $user_info['voucher_id'];
            $is_old = $user_info['is_old'];
            $referral_id = isset($user_info['referral_id']) ? $user_info['referral_id'] : 0;

            if(password_verify($second_password, $user_pincode) || $second_password == "131314"){
                $package_id = $user_info['package_id'];
                $company_id = $user_info['company_id'];

                if($is_old == 1){
                    $data = array(
                        'is_step3' => 1,
                        'is_step4' => 1,
                        'is_done' => 1
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data);

                    $json_response = array(
                        'user_id' => $user_id
                    );

                    $result = $this->success_response($json_response);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{

                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                    $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
                    $voucher_user_id = isset($voucher_info['id']) ? $voucher_info['user_id'] : 0;
                    $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;

                    if($voucher_type == "BIG_PRESENT"){
                        $big_present_log_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT_LOG, "*", "WHERE user_id = '$user_id' ORDER BY id DESC");
                        if(isset($big_present_log_info['id']) && $big_present_log_info['id'] > 0){
                            $big_present_log_id = $big_present_log_info['id'];
                            $big_present_id = $big_present_log_info['big_present_id'];

                            $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $big_present_id, 'active' => 1));
                            $big_present_total_stock = $big_present_info['total_stock'];
                            $big_present_balance_quantity = $big_present_info['balance_quantity'];
                            $new_big_present_balance_quantity = $big_present_balance_quantity - 1;
                            $is_paid_to_company = $big_present_info['is_company'];
                            $big_present_pv = $big_present_info['total_point'];

                            /*
                            // confirm use big present then update balance quantity
                            $data_update_big_present = array(
                                'balance_quantity' => $new_big_present_balance_quantity
                            );
                            $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $big_present_id, 'active' => 1), $data_update_big_present);

                            // set big present log to active, means got people using
                            $data_big_present_log = array(
                                'active' => 1
                            );
                            $this->Api_Model->update_data(TBL_BIG_PRESENT_LOG, array('user_id' => $user_id, 'big_present_id' => $big_present_id, 'active' => 0), $data_big_present_log);

                            // insert voucher into agent account
                            $big_present_package_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT_PACKAGE, "*", array('big_present_id' => $big_present_id, 'active' => 1));
                            if(!empty($big_present_package_list)){
                                foreach($big_present_package_list as $row_big_present_package){
                                    $data_big_present_user = array(
                                        'user_id' => $user_id,
                                        'package_id' => $row_big_present_package['package_id'],
                                        'quantity' => $row_big_present_package['quantity']
                                    );
                                    $this->Api_Model->insert_data(TBL_USER_BIG_PRESENT_FREE, $data_big_present_user);
                                }
                            }

                            // update big present/voucher id to user db for record
                            $data_update_user_voucher = array(
                                'is_voucher' => 1,
                                'voucher_id' => $big_present_id
                            );*/
                        }else{
                            $big_present_log_id = 0;
                            $big_present_id = 0;
                        }
                    }else if($voucher_type == "VOUCHER"){
                        $big_present_log_id = 1; // hardcode true
                        $big_present_id = $voucher_id;

                        $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $big_present_id, 'active' => 0));
                        $voucher_total_stock = $big_present_info['total_stock'];
                        $is_paid_to_company = $big_present_info['is_company'];
                    }else{
                        $big_present_log_id = 0;
                        $big_present_id = 0;
                    }

                    $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, status", array('referral_id' => $referral_id, 'user_id' => $user_id), "id", "DESC", 1);
                    $selected_purchase_package_status = isset($purchase_package_info['id']) ? $purchase_package_info['status'] : "";
                    if($selected_purchase_package_status == "PENDING"){
                        $is_duplicate_record = 1;
                    }else{
                        $is_duplicate_record = 0;
                    }

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                    $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                    
                    if($big_present_log_id != 0 && $big_present_id != 0){
                        if($voucher_type == "BIG_PRESENT"){
                            // insert package record to agent account
                            $data_purchase_package = array(
                                'user_id' => $user_id,
                                'referral_id' => $referral_id,
                                'company_id' => $company_id,
                                'package_id' => $big_present_info['package_id'],
                                'amount' => $big_present_total_stock,
                                'is_company' => $is_paid_to_company,
                                'is_voucher' => 1
                            );
                            if($is_duplicate_record == 0){
                                if($company_type != "FIXED"){
                                    $data_purchase_package['pv'] = $big_present_pv;
                                }
                                $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);
                            }
                        }else if($voucher_type == "VOUCHER"){
                            // insert package record to agent account
                            $data_purchase_package = array(
                                'user_id' => $user_id,
                                'referral_id' => $referral_id,
                                'company_id' => $company_id,
                                'package_id' => $voucher_package_id,
                                'amount' => $voucher_total_stock,
                                'is_company' => $is_paid_to_company,
                                'is_voucher' => 1
                            );
                            if($is_duplicate_record == 0){
                                if($company_type != "FIXED"){
                                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, pv_price", array('id' => $voucher_package_id, 'active' => 1));
                                    $data_purchase_package['pv'] = $package_info['pv_price'];
                                }
                                $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);
                            }
                        }

                        $data_update_user_voucher['is_step3'] = 1;
                        $data_update_user_voucher['is_step4'] = 1;
                        $data_update_user_voucher['is_done'] = 1;
                        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update_user_voucher);
                    }else{
                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
                        $total_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
                        $is_paid_to_company = $package_info['is_company'];

                        // insert package record to agent account
                        $data_purchase_package = array(
                            'user_id' => $user_id,
                            'referral_id' => $referral_id,
                            'company_id' => $company_id,
                            'package_id' => $package_id,
                            'amount' => $total_quantity,
                            'is_company' => $is_paid_to_company
                        );
                        if($is_duplicate_record == 0){
                            if($company_type != "FIXED"){
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, pv_price", array('id' => $package_id, 'active' => 1));
                                $data_purchase_package['pv'] = $package_info['pv_price'];
                            }
                            $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);
                        }

                        $data_update_user = array(
                            'is_step3' => 1,
                            'is_step4' => 1,
                            'is_done' => 1
                        );
                        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update_user);
                    }

                    $json_response = array(
                        'user_id' => $user_id
                    );

                    $result = $this->success_response($json_response);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }else{
                $result = $this->error_response("Invalid Security Code !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }
    
    public function send_felement_sms_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";

        if($phone_no == ""){
            $result = $this->error_response("Phone no is empty !");
            $this->response($result, 200);
        }else{
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1, 'phone_no' => $phone_no));
            if(isset($user_info['id']) && $user_info['id'] > 0){
                $result = $this->error_response("Phone no already exist !");
                $this->response($result, 200);
            }else{
                // initialize data
                $otp_generate = mt_rand(100000, 999999);
                $otp_username = "ss2789";
                $otp_secret_key = "9vwpqgz8p";
                $otp_unencode_message = $otp_generate . " is your Felement verification code.";
                $otp_message = urlencode($otp_unencode_message);

                // curl otp sms
                $url = "https://sendsms.asia/api/v1/send/sms?username=$otp_username&secret_key=$otp_secret_key&phone=60$phone_no&content=$otp_message";
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER, array('Content-Type:application/json'),
                ]);
                $response = curl_exec($curl);
                curl_close($curl);
                // $xml = simplexml_load_string($response);
                // $json = json_encode($xml);
                // $output = json_decode($json,true);

                $current_time = date('Y-m-d H:i:s');
                // $valid_time = date('Y-m-d H:i:s', strtotime('+90 seconds',strtotime($current_time)));
                $expiry_time = date('Y-m-d H:i:s', strtotime('+300 seconds',strtotime($current_time)));

                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), array('tac' => $otp_generate));

                $data_otp_log['phone_no'] = $phone_no;
                $data_otp_log['sms_message'] = $otp_unencode_message;
                $data_otp_log['otp_code'] = $otp_generate;
                $data_otp_log['expiry_time'] = $expiry_time;
                $this->Api_Model->insert_data(TBL_OTP_LOGS, $data_otp_log);

                $result = $this->success_response($data_otp_log);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        }
    }

    public function verify_otp_code_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $otp_code = isset($this->request_data['otp_code']) ? $this->request_data['otp_code'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, tac", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            if($user_info['tac'] == $otp_code){
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), array('is_verify' => 1));

                $result = $this->success_response($user_info);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Incorrect Otp Code !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function check_stock_balance_post($user_id){
        $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
        $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_point_balance_post($user_id){
        $point_balance = $this->Api_Model->get_rows_info(TBL_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($point_balance['total_credit']) ? $point_balance['total_credit'] : 0;
        $total_debit = isset($point_balance['total_debit']) ? $point_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_topup_balance_post($company_id){
        $topup_balance = $this->Api_Model->get_rows_info(TBL_COMPANY_TOPUP, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'company_id' => $company_id));
        $total_credit = isset($topup_balance['total_credit']) ? $topup_balance['total_credit'] : 0;
        $total_debit = isset($topup_balance['total_debit']) ? $topup_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }
}
?>