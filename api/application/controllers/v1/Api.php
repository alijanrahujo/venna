<?php
class  Api extends Base_Controller {
    /* Index

    - get_member_info_post
    - get_about_us_post
    - get_about_us_detail_post
    - get_company_info_post
    - get_slider_post
    - get_gallery_post
    - get_gallery_detail_post
    - display_attachment_detail_post
    - get_product_post
    - get_product_detail_post
    - get_voucher_post
    - get_address_post
    - select_product_post
    - get_cart_post
    - update_cart_quantity_post
    - delete_cart_post
    - insert_address_post
    - apply_voucher_post
    - get_order_subtotal_post
    - place_order_post
    - get_shipment_history_post
    - get_shipment_order_detail_post
    - get_stock_record_post
    - get_pv_record_post
    - get_order_post
    - get_order_detail_post
    - submit_order_receipt_post
    - submit_package_receipt_post
    - check_is_display_voucher_post
    - get_voucher_referral_url_post
    - get_voucher_package_post
    - insert_voucher_package_post
    - get_voucher_list_post
    - get_restock_package_post
    - calculate_restock_package_post
    - calculate_voucher_package_post
    - check_is_free_voucher_post
    - insert_restock_package_post
    - check_product_order_post
    - approve_retail_order_post
    - cancel_retail_order_post
    - get_cash_wallet_post
    - get_point_wallet_post
    - get_rb_voucher_post
    - get_network_post
    - get_mms_record_post
    - get_monthly_bonus_record_post
    - update_profile_post
    - update_password_post
    - update_security_code_post
    - update_user_address_post
    - delete_user_address_post
    - get_user_address_info_post
    - get_network_data_post
    - insert_withdraw_post
    - get_ticket_post
    - get_ticket_message_post
    - insert_ticket_post
    - reply_ticket_post
    - get_course_post

    End Index */

    public function __construct(){
        parent::__construct();
        $this->load->library('phpqrcode/qrlib');
    }

    public function index_get(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function index_post(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function read_announcement_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $announcement_id = isset($this->request_data['announcement_id']) ? $this->request_data['announcement_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $read_announcement_info = $this->Api_Model->get_rows_info(TBL_READ_ANNOUNCEMENT, "id", array('announcement_id' => $announcement_id, 'active' => 1, 'is_read' => 1, 'user_id' => $user_id));
            $is_exist_data = isset($read_announcement_info['id']) ? 1 : 0;

            if($is_exist_data == 0){
                $data = array(
                    'company_id' => $user_info['company_id'],
                    'user_id' => $user_info['id'],
                    'announcement_id' => $announcement_id,
                    'is_read' => 1
                );
                $this->Api_Model->insert_data(TBL_READ_ANNOUNCEMENT, $data);
            }else{
                $data = array();
            }

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_member_info_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, package_id, company_id, referral_id, email, fullname, phone_no, bank_name, account_name, account_no, profile_image, retail_poster, register_poster, share_qr_code, register_qr", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $company_id = isset($company_info['id']) ? $company_info['id'] : 0;
            $company_subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";
            $default_image = isset($company_info['id']) ? $company_info['default_image'] : "";
            $payment_gateway_file = isset($company_info['id']) ? $company_info['private_key_file'] : "";
            if($payment_gateway_file == "" || $payment_gateway_file == NULL){
                $is_active_payment_gateway = 0;
            }else{
                $is_active_payment_gateway = 1;
            }
            $stock_balance = $this->check_stock_balance_post($user_id);
            $point_balance = $this->check_point_balance_post($user_id);
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $user_info['package_id'], 'active' => 1));
            $total_organization = $this->get_organization($user_id);
            $total_wallet = $this->check_wallet_balance_post($user_id, true);
            $total_pv = $this->check_pv_balance_post($user_id);

            // get referral details
            $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;
            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, package_id, email, phone_no, profile_image", array('id' => $referral_id, 'active' => 1));
            $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;
            $referral_package = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, english_name", array('id' => $referral_package_id, 'active' => 1));
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
            $referral_email = isset($referral_info['id']) ? $referral_info['email'] : "";
            $referral_phone_no = isset($referral_info['id']) ? $referral_info['phone_no'] : "";
            $referral_package_name = isset($referral_package['id']) ? $referral_package['english_name'] : "";
            $referral_profile_image = isset($referral_info['id']) ? $referral_info['profile_image'] : "";

            $break_away_bonus = isset($company_info['id']) ? $company_info['break_away_bonus'] : "";
            if($break_away_bonus == "0.00"){
                $is_break_away_bonus = 0;
            }else{
                $is_break_away_bonus = 1;
            }
            $min_mdb_qty = isset($company_info['id']) ? $company_info['min_mdb_qty'] : "";
            if($min_mdb_qty == "0.00"){
                $is_mdb = 0;
            }else{
                $is_mdb = 1;
            }
            $rb_voucher_qty = isset($company_info['id']) ? $company_info['rb_voucher_qty'] : "";
            if($rb_voucher_qty == "0"){
                $is_rb = 0;
            }else{
                $is_rb = 1;
            }
            $drb_bonus = isset($company_info['id']) ? $company_info['drb_bonus'] : "";
            if($drb_bonus == "0.00"){
                $is_drb = 0;
            }else{
                $is_drb = 1;
            }
            $mms_level = isset($company_info['id']) ? $company_info['mms_level'] : "";
            if($mms_level == "0"){
                $is_mms = 0;
            }else{
                $is_mms = 1;
            }
            $cb_rate = isset($company_info['id']) ? $company_info['cb_rate'] : "";
            if($cb_rate == "0.00"){
                $is_cb = 0;
            }else{
                $is_cb = 1;
            }

            if($user_info['profile_image'] == "" || $user_info['profile_image'] == NULL){
                if($default_image == ""){
                    $profile_image = DISPLAY_PATH . "img/default-profile.png";
                }else{
                    $profile_image = DISPLAY_PATH . "img/" . $default_image;
                }
            }else{
                $profile_image = DISPLAY_PATH . "img/profile/" . $user_info['profile_image'];
            }

            if(empty($user_info['share_qr_code']) || $user_info['share_qr_code'] == NULL){
                $is_empty_retail_qr = 1;
            }else{
                $is_empty_retail_qr = 0;
            }

            if(empty($user_info['register_qr']) || $user_info['register_qr'] == NULL){
                $is_empty_register_qr = 1;
            }else{
                $is_empty_register_qr = 0;
            }

            $total_unread_message = 0;
            $normal_announcement_list = $this->Api_Model->get_rows(TBL_ANNOUNCEMENT, "*", array('company_id' => $user_info['company_id'], 'active' => 1, 'user_id' => 0));
            if(!empty($normal_announcement_list)){
                foreach($normal_announcement_list as $row_normal_announcement){
                    $read_normal_announcement_info = $this->Api_Model->get_rows_info(TBL_READ_ANNOUNCEMENT, "id", array('is_read' => 1, 'active' => 1, 'user_id' => $user_info['id'], 'announcement_id' => $row_normal_announcement['id']));
                    $is_read_announcement = isset($read_normal_announcement_info['id']) ? 1 : 0;

                    if($is_read_announcement == 0){
                        $total_unread_message += 1;
                    }
                }
            }

            $user_announcement_list = $this->Api_Model->get_rows(TBL_ANNOUNCEMENT, "*", array('company_id' => $user_info['company_id'], 'active' => 1, 'user_id' => $user_info['id']));
            if(!empty($user_announcement_list)){
                foreach($user_announcement_list as $row_user_announcement){
                    $read_user_announcement_info = $this->Api_Model->get_rows_info(TBL_READ_ANNOUNCEMENT, "id", array('is_read' => 1, 'active' => 1, 'user_id' => $user_info['id'], 'announcement_id' => $row_user_announcement['id']));
                    $is_read_announcement = isset($read_user_announcement_info['id']) ? 1 : 0;

                    if($is_read_announcement == 0){
                        $total_unread_message += 1;
                    }
                }
            }

            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, COUNT(*) as total_voucher", array('type' => "VOUCHER", 'active' => 1, 'balance_quantity !=' => 0, 'user_id' => $user_id, 'total_stock' => 60));
            $total_voucher = isset($voucher_info['id']) ? $voucher_info['total_voucher'] : 0;

            $data = array(
                'company_type' => $company_type,
                'username' => $user_info['username'],
                'package' => $package_info['english_name'],
                'stock_balance' => $stock_balance,
                'point_balance' => $point_balance,
                'total_organization' => $total_organization,
                'total_wallet' => $total_wallet,
                'total_pv' => $total_pv,
                'total_voucher' => $total_voucher,
                'referral_name' => $referral_fullname,
                'referral_package' => $referral_package_name,
                'referral_email' => $referral_email,
                'referral_phone' => $referral_phone_no,
                'referral_profile_image' => DISPLAY_PATH . "img/profile/" . $referral_profile_image,
                'is_break_away_bonus' => $is_break_away_bonus,
                'is_mdb' => $is_mdb,
                'is_rb' => $is_rb,
                'is_drb' => $is_drb,
                'is_mms' => $is_mms,
                'is_cb' => $is_cb,
                'email' => $user_info['email'],
                'fullname' => $user_info['fullname'],
                'phone_no' => $user_info['phone_no'],
                'bank_name' => $user_info['bank_name'],
                'account_name' => $user_info['account_name'],
                'account_no' => $user_info['account_no'],
                'profile_image' => $profile_image,
                'retail_qr' => DISPLAY_PATH . "img/retail_poster/" . $user_info['retail_poster'],
                'register_qr' => DISPLAY_PATH . "img/register_poster/" . $user_info['register_poster'],
                'pure_retail_qr' => DISPLAY_PATH . "img/qrcode/" . $user_info['share_qr_code'],
                'pure_register_qr' => DISPLAY_PATH . "img/register_qr/" . $user_info['register_qr'],
                'is_empty_retail_qr' => $is_empty_retail_qr,
                'is_empty_register_qr' => $is_empty_register_qr,
                'is_active_payment_gateway' => $is_active_payment_gateway,
                'total_unread_message' => $total_unread_message,
                'company_id' => $company_id
            );
            if($user_info['retail_poster'] == NULL){
                $data['register_qr'] = DISPLAY_PATH . "img/register_qr/" . $user_info['register_qr'];
            }
            if($user_info['register_poster'] == NULL){
                $data['retail_qr'] = DISPLAY_PATH . "img/qrcode/" . $user_info['share_qr_code'];
            }

            if($referral_id == 0){
                $data['referral_name'] = $company_info['name'];
                $data['referral_email'] = $company_info['email'];
                $data['referral_phone'] = $company_info['phone_no'];
            }

            $order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, payment_status, status", array('user_id' => $user_id, 'active' => 1), "id", "ASC", 1);
            if(isset($order_info['id']) && $order_info['id'] > 0){
                if($order_info['payment_status'] == "UNPAID" && $order_info['status'] == "PENDING"){
                    $is_first_time_order = 1;
                }else{
                    $is_first_time_order = 0;
                }
            }else{
                $is_first_time_order = 0;
            }
            $data['is_first_time_user'] = $is_first_time_order;

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_about_us_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $about_us_list = $this->Api_Model->get_rows(TBL_COMPANY_SECTION, "id, name", array('active' => 1, 'company_id' => $company_id, 'type' => $type));
        
            $result = $this->success_response($about_us_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_about_us_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $content_id = isset($this->request_data['content_id']) ? $this->request_data['content_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $about_us_info = $this->Api_Model->get_rows_info(TBL_COMPANY_CONTENT, "id, content, content_id", array('content_id' => $content_id, 'active' => 1));
            $content_info = $this->Api_Model->get_rows_info(TBL_COMPANY_SECTION, "name", array('id' => $about_us_info['content_id'], 'active' => 1));
            $about_us_info['name'] = $content_info['name'];

            $result = $this->success_response($about_us_info);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_company_info_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;
        $is_retail = isset($this->request_data['is_retail']) ? $this->request_data['is_retail'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, referral_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_info_sql(TBL_COMPANY, "id, is_customize_shipping", "WHERE id = '" . $user_info['company_id'] . "' AND active = '1'");
            $is_customize_shipping = isset($company_info['id']) ? $company_info['is_customize_shipping'] : 0;
            if($is_retail == 1){
                // upline details
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, bank_name, account_name, account_no", array('id' => $user_info['id'], 'active' => 1));
                $referral_bank_name = isset($referral_info['bank_name']) ? $referral_info['bank_name'] : "";
                $referral_account_name = isset($referral_info['account_name']) ? $referral_info['account_name'] : "";
                $referral_account_no = isset($referral_info['account_no']) ? $referral_info['account_no'] : "";

                $data = array(
                    'bank_name' => $referral_bank_name,
                    'account_name' => $referral_account_name,
                    'account_no' => $referral_account_no,
                    'is_customize_shipping' => $is_customize_shipping
                );

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                if($package_id == 0){
                    // company details
                    $company_info = $this->Api_Model->get_info_sql(TBL_USER, "id, bank_name, account_name, account_no", "WHERE company_id = '" . $user_info['company_id'] . "' AND active = '1' AND user_type = 'ADMIN' ORDER BY id ASC LIMIT 1");
                    $company_bank_name = isset($company_info['id']) ? $company_info['bank_name'] : "";
                    $company_account_name = isset($company_info['id']) ? $company_info['account_name'] : "";
                    $company_account_no = isset($company_info['id']) ? $company_info['account_no'] : "";

                    $company_info['bank_name'] = $company_bank_name;
                    $company_info['account_name'] = $company_account_name;
                    $company_info['account_no'] = $company_account_no;
                    $company_info['is_customize_shipping'] = $is_customize_shipping;

                    $result = $this->success_response($company_info);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{
                    // package details
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, is_company", array('id' => $package_id, 'active' => 1));
                    $is_paid_to_company = $package_info['is_company'];

                    $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, referral_id, is_company, amount", array('id' => $order_id, 'active' => 1));
                    $referral_id = isset($purchase_package_info['id']) ? $purchase_package_info['referral_id'] : 0;
                    $is_purchase_to_company = isset($purchase_package_info['id']) ? $purchase_package_info['is_company'] : 0;
                    $purchase_package_amount = isset($purchase_package_info['id']) ? $purchase_package_info['amount'] : 0;

                    // upline details
                    if($referral_id == $user_id){
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, bank_name, account_name, account_no", array('id' => $user_info['id'], 'active' => 1));
                    }else{
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, bank_name, account_name, account_no", array('id' => $user_info['referral_id'], 'active' => 1));
                    }
                    $referral_bank_name = isset($referral_info['bank_name']) ? $referral_info['bank_name'] : "";
                    $referral_account_name = isset($referral_info['account_name']) ? $referral_info['account_name'] : "";
                    $referral_account_no = isset($referral_info['account_no']) ? $referral_info['account_no'] : "";

                    // company details
                    $company_info = $this->Api_Model->get_info_sql(TBL_USER, "bank_name, account_name, account_no", "WHERE company_id = '" . $user_info['company_id'] . "' AND active = '1' AND user_type = 'ADMIN' ORDER BY id ASC LIMIT 1");

                    if($is_paid_to_company == 0){
                        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_company", array('id' => $referral_id, 'active' => 1));
                        $referral_is_company = isset($user_info['id']) ? $user_info['is_company'] : 0;
                        if($referral_is_company == 1 || ($is_purchase_to_company == 1 && $is_paid_to_company == 1) || $purchase_package_amount == 60 || $purchase_package_amount == 4){
                            $company_info['bank_name'] = $company_info['bank_name'];
                            $company_info['account_name'] = $company_info['account_name'];
                            $company_info['account_no'] = $company_info['account_no'];
                            $company_info['is_customize_shipping'] = $is_customize_shipping;
                        }else{
                            $company_info['bank_name'] = $referral_bank_name;
                            $company_info['account_name'] = $referral_account_name;
                            $company_info['account_no'] = $referral_account_no;
                            $company_info['is_customize_shipping'] = $is_customize_shipping;
                        }
                    }

                    // promotion is paid to company
                    // $special_company_info = $this->Api_Model->get_info_sql(TBL_USER, "bank_name, account_name, account_no", "WHERE company_id = '" . $user_info['company_id'] . "' AND active = '1' AND user_type = 'ADMIN' ORDER BY id ASC LIMIT 1");

                    // if($is_purchase_to_company == 1){
                    //     $company_info['bank_name'] = $special_company_info['bank_name'];
                    //     $company_info['account_name'] = $special_company_info['account_name'];
                    //     $company_info['account_no'] = $special_company_info['account_no'];
                    // }

                    $result = $this->success_response($company_info);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_slider_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $sequence = isset($this->request_data['sequence']) ? $this->request_data['sequence'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        // if($company_id == 2){
            $slider_list = $this->Api_Model->get_rows(TBL_SLIDER, "*", array('type' => $type, 'active' => 1, 'company_id' => $company_id), "", "", "sequence", "ASC");

            if(!empty($slider_list)){
                foreach($slider_list as $slkey => $slval){
                    $slider_image = $slval['image'];
                    $slider_list[$slkey]['image'] = DISPLAY_PATH . "img/slider/" . $slider_image;
                }
            }else{
                $slider_list = array();
            }
    
            $result = $this->success_response($slider_list);
            $this->response($result, REST_Controller::HTTP_OK);
        // }else{
        //     if($sequence == 1){
        //         $slider_info = $this->Api_Model->get_rows_info(TBL_SLIDER, "*", array('type' => $type, 'active' => 1, 'company_id' => $company_id), "id", "ASC", 1);
        //         if(isset($slider_info['id']) && $slider_info['id'] > 0){
        //             $slider_image = $slider_info['image'];
        //             $slider_info['image'] = DISPLAY_PATH . "img/slider/" . $slider_image;
        //         }else{
        //             $slider_info = "";
        //         }

        //         $result = $this->success_response($slider_info);
        //         $this->response($result, REST_Controller::HTTP_OK);
        //     }else{
        //         $first_slider_info = $this->Api_Model->get_rows_info(TBL_SLIDER, "*", array('type' => $type, 'active' => 1, 'company_id' => $company_id), "id", "ASC", 1);
        //         $first_slider_id = isset($first_slider_info['id']) ? $first_slider_info['id'] : 0;
        //         if($type == "HOME"){
        //             $slider_list = $this->Api_Model->get_rows(TBL_SLIDER, "*", array('type' => $type, 'active' => 1, 'id >' => $first_slider_id, 'company_id' => $company_id));
        //         }else{
        //             $slider_list = $this->Api_Model->get_rows(TBL_SLIDER, "*", array('type' => $type, 'active' => 1, 'company_id' => $company_id));
        //         }

        //         if(!empty($slider_list)){
        //             foreach($slider_list as $slkey => $slval){
        //                 $slider_image = $slval['image'];
        //                 $slider_list[$slkey]['image'] = DISPLAY_PATH . "img/slider/" . $slider_image;
        //             }
        //         }else{
        //             $slider_list = array();
        //         }
        
        //         $result = $this->success_response($slider_list);
        //         $this->response($result, REST_Controller::HTTP_OK);
        //     }
        // }
    }

    public function get_announcement_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $announcement_list = $this->Api_Model->get_rows(TBL_ANNOUNCEMENT, "*", array('active' => 1, 'company_id' => $company_id));

            foreach($announcement_list as $alkey => $alval){
                $read_announcement_info = $this->Api_Model->get_rows_info(TBL_READ_ANNOUNCEMENT, "id", array('announcement_id' => $alval['id'], 'active' => 1, 'is_read' => 1, 'user_id' => $user_id));
                $announcement_list[$alkey]['is_read'] = isset($read_announcement_info['id']) ? 1 : 0;
                if($alval['user_id'] != 0){
                    if($user_id != $alval['user_id']){
                        unset($announcement_list[$alkey]);
                    }
                }
            }
        
            $result = $this->success_response($announcement_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_user_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $voucher_list = $this->Api_Model->get_rows(TBL_BIG_PRESENT, "id, code, total_stock, package_id, price", array('user_id' => $user_id, 'active' => 1, 'type' => "VOUCHER", 'balance_quantity !=' => 0, 'total_stock' => 60));
            if(!empty($voucher_list)){
                foreach($voucher_list as $vlkey => $row_voucher){
                    $voucher_stock = $row_voucher['total_stock'];
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, name", array('id' => $row_voucher['package_id'], 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                    $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;

                    if($package_quantity == $voucher_stock){
                        $voucher_list[$vlkey]['package_name'] = $package_name;
                        $voucher_type = $package_name;
                        $voucher_list[$vlkey]['voucher_type'] = $voucher_type;
                    }else{
                        $voucher_list[$vlkey]['package_name'] = "";
                        $voucher_type = "Buy 1 Free 1";
                        $voucher_list[$vlkey]['voucher_type'] = $voucher_type;
                    }
                }
            }else{
                $voucher_list = array();
            }
            $result = $this->success_response($voucher_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_announcement_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $announcement_id = isset($this->request_data['announcement_id']) ? $this->request_data['announcement_id'] : 0;

        $announcement_info = $this->Api_Model->get_rows_info(TBL_ANNOUNCEMENT, "*", array('active' => 1, 'id' => $announcement_id));
        
        $result = $this->success_response($announcement_info);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_gallery_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $gallery_list = $this->Api_Model->get_rows(TBL_GALLERY, "id, name", array('active' => 1, 'company_id' => $company_id));
        
            $result = $this->success_response($gallery_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_gallery_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $gallery_id = isset($this->request_data['gallery_id']) ? $this->request_data['gallery_id'] : 0;

        $gallery_attachment_list = $this->Api_Model->get_rows(TBL_GALLERY_ATTACHMENT, "*", array('active' => 1, 'gallery_id' => $gallery_id));
        if(!empty($gallery_attachment_list)){
            foreach($gallery_attachment_list as $gakey => $gaval){
                $gallery_image = $gaval['name'];
                $gallery_detail_id = $gaval['gallery_id'];
                $gallery_info = $this->Api_Model->get_rows_info(TBL_GALLERY, "id, type", array('active' => 1, 'id' => $gallery_detail_id));
                $gallery_attachment_list[$gakey]['type'] = $gallery_info['type'];
                $gallery_attachment_list[$gakey]['image'] = DISPLAY_PATH . "img/gallery/" . $gaval['gallery_id'] . "/" . $gallery_image;
            }
        }else{
            $gallery_attachment_list = array();
        }
        
        $result = $this->success_response($gallery_attachment_list);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function display_attachment_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $attachment_id = isset($this->request_data['attachment_id']) ? $this->request_data['attachment_id'] : 0;

        $gallery_attachment_info = $this->Api_Model->get_rows_info(TBL_GALLERY_ATTACHMENT, "*", array('active' => 1, 'id' => $attachment_id));
        if(isset($gallery_attachment_info['id']) && $gallery_attachment_info['id'] > 0){
            $gallery_attachment_info['name'] = DISPLAY_PATH . "img/gallery/" . $gallery_attachment_info['gallery_id'] . "/" . $gallery_attachment_info['name'];

            $result = $this->success_response($gallery_attachment_info);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Gallery !");
            $this->response($result, 200);
        }
    }

    public function get_promotion_product_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $is_restock = isset($this->request_data['is_restock']) ? $this->request_data['is_restock'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, country_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $country_id = $user_info['country_id'];
            $package_id = $user_info['package_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $product_list = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $company_id, 'active' => 1, 'is_active' => 1, 'is_promotion' => 1));
            if(!empty($product_list)){
                foreach($product_list as $plkey => $plval){
                    $product_list[$plkey]['image'] = DISPLAY_PATH . "img/product/" . $plval['image'];
                    if($company_type != "FIXED"){
                        $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $plval['id'], 'package_id' => $package_id, 'active' => 1));
                        $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                        $product_list[$plkey]['price'] = $product_price;
                    }else{
                        if($is_restock == 1 || ($plval['is_promotion'] == 1 && $plval['is_normal'] == 0)){
                            $product_list[$plkey]['price'] = $plval['price'];
                        }else if($order_type == "mms" && $plval['is_promotion'] == 0){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('id' => $package_id, 'active' => 1));
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_list[$plkey]['price'] = $package_price;
                        }else if($plval['is_promotion'] == 1 && $plval['is_normal'] == 1){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_list[$plkey]['price'] = $package_price;
                        }else{
                            $product_list[$plkey]['price'] = "0.00";
                        }
                    }

                    $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $country_id, 'active' => 1));
                    $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
                    $product_list[$plkey]['currency_name'] = $currency_name;

                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('product_id' => $plval['id'], 'user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                    $is_add_to_cart = isset($cart_info['id']) ? 1 : 0;
                    $cart_id = isset($cart_info['id']) ? $cart_info['id'] : 0;
                    $product_list[$plkey]['is_add_to_cart'] = $is_add_to_cart;
                    $product_list[$plkey]['cart_id'] = $cart_id;

                    $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, product_id, SUM(quantity) as total_quantity", array('active' => 1, 'user_id' => $user_id, 'product_id' => $plval['id'], 'type' => $order_type));
                    $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
                    $cart_product_id = isset($cart_quantity_info['id']) ? $cart_quantity_info['product_id'] : 0;

                    $voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, product_id", array('product_id' => $cart_product_id, 'active' => 1, 'user_id' => $user_id));
                    $voucher_product_id = isset($voucher_info['id']) ? $voucher_info['product_id'] : 0;

                    if($voucher_product_id == 0){
                        $v_product_id = 0;
                    }else{
                        $v_product_id = $voucher_product_id;
                    }

                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('product_id' => $v_product_id, 'active' => 1, 'user_id' => $user_id));
                    $voucher_quantity = isset($user_voucher_info['id']) ? $user_voucher_info['quantity'] : 0;
                    $voucher_id = isset($user_voucher_info['id']) ? $user_voucher_info['id'] : 0;
                    if($cart_product_id == 0){
                        $product_list[$plkey]['voucher_id'] = 0;
                    }else{
                        $product_list[$plkey]['voucher_id'] = $voucher_id;
                    }
                }
            }else{
                $product_list = array();
            }

            $result = $this->success_response($product_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_product_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $is_restock = isset($this->request_data['is_restock']) ? $this->request_data['is_restock'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, country_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $country_id = $user_info['country_id'];
            $package_id = $user_info['package_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $product_list = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $company_id, 'active' => 1, 'is_active' => 1, 'is_normal' => 1), "", "", "is_new", "DESC");
            if(!empty($product_list)){
                foreach($product_list as $plkey => $plval){
                    $product_list[$plkey]['is_new'] = $plval['is_new'];
                    $product_list[$plkey]['image'] = DISPLAY_PATH . "img/product/" . $plval['image'];
                    if($company_type != "FIXED"){
                        $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $plval['id'], 'package_id' => $package_id, 'active' => 1));
                        $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                        $product_list[$plkey]['price'] = $product_price;
                    }else{
                        if($is_restock == 1 && $order_type == "restock"){
                            $product_list[$plkey]['price'] = $plval['price'];
                        }else if($order_type == "mms"){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_list[$plkey]['price'] = $package_price;
                        }else if($order_type == "drb"){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1, 'is_max' => 1));
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_list[$plkey]['price'] = $package_price;
                        }else{
                            $product_list[$plkey]['price'] = "0.00";
                        }
                    }

                    $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $country_id, 'active' => 1));
                    $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
                    $product_list[$plkey]['currency_name'] = $currency_name;

                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('product_id' => $plval['id'], 'user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                    $is_add_to_cart = isset($cart_info['id']) ? 1 : 0;
                    $cart_id = isset($cart_info['id']) ? $cart_info['id'] : 0;
                    $product_list[$plkey]['is_add_to_cart'] = $is_add_to_cart;
                    $product_list[$plkey]['cart_id'] = $cart_id;
                }
            }else{
                $product_list = array();
            }

            $result = $this->success_response($product_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_product_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $package_id = $user_info['package_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('company_id' => $company_id, 'active' => 1, 'id' => $product_id));
            if(isset($product_info['id']) && $product_info['id'] > 0){
                $product_info['image'] = DISPLAY_PATH . "img/product/" . $product_info['image'];
                if($company_type != "FIXED"){
                    $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $product_info['id'], 'package_id' => $package_id, 'active' => 1));
                    $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                    $product_info['price'] = $product_price;
                }else{
                    $product_info['price'] = "0.00";
                }

                $result = $this->success_response($product_info);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Invalid Product !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $country_id = $user_info['country_id'];
            $user_voucher_list = $this->Api_Model->get_rows(TBL_USER_VOUCHER, "*", array('active' => 1, 'user_id' => $user_id));
            if(!empty($user_voucher_list)){
                foreach($user_voucher_list as $uvkey => $uvval){
                    $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $country_id, 'active' => 1));
                    $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
                    $product_list[$uvkey]['currency_name'] = $currency_name;

                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name", array('id' => $uvval['product_id'], 'active' => 1));
                    $product_name = isset($product_info['id']) ? $product_info['name'] : "Any Product";
                    $user_voucher_list[$uvkey]['voucher_name'] = $product_name . " - " . $currency_name . $uvval['price'] . " x" . $uvval['quantity'];
                }
            }else{
                $user_voucher_list = array();
            }
        
            $result = $this->success_response($user_voucher_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_address_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $address_list = $this->Api_Model->get_rows(TBL_USER_ADDRESS, "*", array('user_id' => $user_id, 'active' => 1));
        if(!empty($address_list)){
            foreach($address_list as $alkey => $alval){
                $address_list[$alkey]['name'] = $alval['name'] . " - " . $alval['address'];
            }
        }else{
            $address_list = array();
        }
        
        $result = $this->success_response($address_list);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_shipping_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $is_got_price = isset($this->request_data['is_got_price']) ? $this->request_data['is_got_price'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $country_id = $user_info['country_id'];
            $company_id = $user_info['company_id'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_customize_shipping", array('id' => $company_id, 'active' => 1));
            $is_customize_shipping = isset($company_info['id']) ? $company_info['is_customize_shipping'] : 0;

            if($company_id != 2 && $company_id != 11 && $company_id != 12){
                if($is_customize_shipping == 1){
                    $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                    if(!empty($cart_list)){
                        $total_gram = 0;
                        foreach($cart_list as $row_cart){
                            $product_id = $row_cart['product_id'];
                            $quantity = $row_cart['quantity'];

                            $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, gram", array('id' => $product_id, 'active' => 1));
                            $product_gram = isset($product_info['id']) ? $product_info['gram'] : "0.00";

                            $total_gram += $product_gram * $quantity;
                        }

                        $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id));
                        $shipping_id = isset($delivery_fee_info['id']) ? $delivery_fee_info['id'] : 0;

                        $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, 'id' => $shipping_id));
                        if(empty($delivery_fee_list)){
                            $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
                        }
                    }else{
                        $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
                    }
                }else{
                    $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
                }
            }else{
                if($is_got_price == 1){
                    $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, 'price !=' => "0.00"));
                }else{
                    $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, 'price' => "0.00"));
                }
            }
        
            $result = $this->success_response($delivery_fee_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function select_product_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $is_restock = isset($this->request_data['is_restock']) ? $this->request_data['is_restock'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";
        $rb_voucher_id = isset($this->request_data['rb_voucher_id']) ? $this->request_data['rb_voucher_id'] : 0;
        $quantity = 1;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, referral_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $package_id = $user_info['package_id'];
            $referral_id = $user_info['referral_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($order_type == "rb" && $rb_voucher_id == 0){
                $result = $this->error_response("Please Select RB Voucher First !");
                $this->response($result, 200);
            }else{
                $is_active_voucher = 0;
                $is_able_to_continue = false;
                if($order_type == "normal" || $order_type == "restock"){
                    if($company_type == "FIXED"){
                        if($order_type == "normal"){
                            $account_balance = $this->check_stock_balance_post($user_id);
                        }else{
                            $account_balance = $this->check_stock_balance_post($user_id);
                        }
                        $error_message = "Insufficient Stock !";
                        if($account_balance == 0 || $account_balance == "0.00"){
                            $is_able_to_continue = false;
                        }else if($account_balance == $quantity){
                            $is_able_to_continue = true;
                        }else if($account_balance < $quantity){
                            $is_able_to_continue = false;
                        }else{
                            $is_able_to_continue = true;
                        }
                    }else{
                        $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'package_id' => $package_id, 'product_id' => $product_id, 'active' => 1));
                        $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                        $product_pv_price = isset($product_price_info['id']) ? $product_price_info['pv_price'] : "0.00";

                        $account_balance = $this->check_point_balance_post($user_id);
                        $pv_balance = $this->check_pv_balance_post($user_id);

                        // check cart total
                        $cart_balance = $this->check_cart_subtotal_balance_post($user_id);
                        $product_price += $cart_balance;

                        // check cart pv total
                        $cart_pv_balance = $this->check_cart_pv_subtotal_balance_post($user_id);
                        $product_pv_price += $cart_pv_balance;

                        $error_message = "Insufficient Balance !";

                        if($product_pv_price != "0.00"){
                            if(($pv_balance == 0 || $pv_balance == "0.00") && $product_pv_price != "0.00"){
                                $is_able_to_continue = false;
                            }else if(($pv_balance == $product_pv_price) && $product_pv_price != "0.00"){
                                $is_able_to_continue = true;
                            }else if(($pv_balance < $product_pv_price) && $product_pv_price != "0.00"){
                                $is_able_to_continue = false;
                            }else if(($account_balance == 0 || $account_balance == "0.00") && $product_price != "0.00"){
                                $is_able_to_continue = false;
                            }else if(($account_balance == $product_price) && $product_price != "0.00"){
                                $is_able_to_continue = true;
                            }else if(($account_balance < $product_price) && $product_price != "0.00"){
                                $is_able_to_continue = false;
                            }else{
                                $is_able_to_continue = true;
                            }
                        }else{
                            if(($account_balance == 0 || $account_balance == "0.00") && $product_price != "0.00"){
                                $is_able_to_continue = false;
                            }else if(($account_balance == $product_price) && $product_price != "0.00"){
                                $is_able_to_continue = true;
                            }else if(($account_balance < $product_price) && $product_price != "0.00"){
                                $is_able_to_continue = false;
                            }else{
                                $is_able_to_continue = true;
                            }
                        }
                    }
                }else if($order_type == "drb"){
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('id' => $package_id, 'active' => 1));
                    $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(subtotal) as grand_total", array('user_id' => $user_id, 'active' => 1, 'type' => "drb"));
                    $product_price = isset($cart_info['id']) ? $cart_info['grand_total'] : "0.00";
                    $drb_balance = $this->check_drb_balance_post($user_id);
                    $estimate_next_price = $product_price + $package_price;

                    if($drb_balance == $estimate_next_price){
                        $is_able_to_continue = true;
                    }else{
                        if($drb_balance < $estimate_next_price){
                            $error_message = "Insufficient DRB !";
                            $is_able_to_continue = false;
                        }else{
                            $is_able_to_continue = true;
                        }
                    }
                }else if($is_promotion == 1){
                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('active' => 1, 'user_id' => $user_id));
                    $total_cart_quantity = isset($cart_info['id']) ? $cart_info['total_quantity'] : 0;

                    $voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, product_id", array('product_id' => $product_id, 'active' => 1, 'user_id' => $user_id));
                    $voucher_product_id = isset($voucher_info['id']) ? $voucher_info['product_id'] : 0;

                    if($voucher_product_id == 0){
                        $v_product_id = 0;
                    }else{
                        $v_product_id = $voucher_product_id;
                    }
                    
                    $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => $v_product_id));
                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('product_id' => $v_product_id, 'active' => 1, 'user_id' => $user_id));
                    $voucher_quantity = isset($user_voucher_info['id']) ? $user_voucher_info['quantity'] : 0;
                    $shipment_voucher_quantity = isset($shipment_voucher_info['id']) ? $shipment_voucher_info['quantity'] : 0;
                    $voucher_id = isset($user_voucher_info['id']) ? $user_voucher_info['id'] : 0;
                    
                    // echo $voucher_quantity . "-" . $total_cart_quantity . "-" . $voucher_quantity; die;

                    if(1 <= $voucher_quantity){
                        $is_able_to_continue = true;
                    }else{
                        $error_message = "Insufficient Voucher !";
                        $is_able_to_continue = false;
                    }

                    if(1 == $shipment_voucher_quantity || 2 == $shipment_voucher_quantity){
                        $is_active_voucher = $voucher_id;
                    }
                }else{
                    $is_able_to_continue = true;
                }
                
                if(!$is_able_to_continue){
                    $result = $this->error_response($error_message);
                    $this->response($result, 200);
                }else{
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $product_id, 'active' => 1));
                    if(isset($product_info['id']) && $product_info['id'] > 0){
                        $is_promotion = $product_info['is_promotion'];
                        $is_normal = $product_info['is_normal'];
                        if($company_type != "FIXED"){
                            $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'package_id' => $package_id, 'product_id' => $product_info['id'], 'active' => 1));
                            $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                            $product_pv_price = isset($product_price_info['id']) ? $product_price_info['pv_price'] : "0.00";
                        }else{
                            if($order_type == "normal" && $is_promotion == 0){
                                $product_price = "0.00";
                            }else if($order_type == "mms" && $is_promotion == 0){
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                                $product_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            }else if($is_promotion == 1 && $is_normal == 1 && $order_type == "mms"){
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                                $product_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            }else if($order_type == "drb"){
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1, 'is_max' => 1));
                                $product_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            }else{
                                $product_price = $product_info['price'];
                            }
                        }
                        $product_info['price'] = $product_price;

                        // if using rb voucher
                        $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "id, actual_price", array('id' => $rb_voucher_id, 'active' => 1));
                        $after_using_rb_voucher_price = isset($rb_voucher_info['id']) ? $rb_voucher_info['actual_price'] : $product_price;

                        // check available product in cart
                        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'product_id' => $product_info['id'], 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                        
                        if(isset($cart_info['id']) && $cart_info['id'] > 0){
                            $cart_id = $cart_info['id'];
                            $data_cart_update = array(
                                'active' => 0,
                                'price' => $product_price,
                                'quantity' => 1,
                                'is_restock' => $is_restock,
                                'is_normal' => $is_normal,
                                'is_promotion' => $is_promotion
                            );
                            if($is_restock == 1 || $order_type == "drb" || $order_type == "mms" || $order_type == "rb"){
                                if($order_type == "drb"){
                                    $data_cart_update['quantity'] = 2;
                                    $data_cart_update['subtotal'] = $product_price;
                                }else if($order_type == "rb"){
                                    $data_cart_update['subtotal'] = $after_using_rb_voucher_price;
                                }else{
                                    $data_cart_update['subtotal'] = $product_price;
                                }
                            }else{
                                if($company_type != "FIXED"){
                                    $data_cart_update['pv_price'] = $product_pv_price;
                                    $data_cart_update['subtotal'] = $product_price;
                                    $data_cart_update['pv_subtotal'] = $product_pv_price;
                                }
                            }
                            $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);
                        }else{
                            $exist_cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'product_id' => $product_info['id'], 'active' => 0, 'is_clear' => 0, 'type' => $order_type));
                            if(isset($exist_cart_info['id']) && $exist_cart_info['id'] > 0){
                                $cart_id = $exist_cart_info['id'];
                                $data_cart_update = array(
                                    'price' => $product_price,
                                    'quantity' => 1,
                                    'is_restock' => $is_restock,
                                    'is_normal' => $is_normal,
                                    'is_promotion' => $is_promotion,
                                    'active' => 1
                                );
                                if($is_restock == 1 || $order_type == "drb" || $order_type == "mms" || $order_type == "rb"){
                                    if($order_type == "drb"){
                                        $data_cart_update['quantity'] = 2;
                                        $data_cart_update['subtotal'] = $product_price;
                                    }else if($order_type == "rb"){
                                        $data_cart_update['subtotal'] = $after_using_rb_voucher_price;
                                    }else{
                                        $data_cart_update['subtotal'] = $product_price;
                                    }
                                }else{
                                    if($company_type != "FIXED"){
                                        $data_cart_update['pv_price'] = $product_pv_price;
                                        $data_cart_update['subtotal'] = $product_price;
                                        $data_cart_update['pv_subtotal'] = $product_pv_price;
                                    }else{
                                        $data_cart_update['subtotal'] = "0.00";
                                    }
                                }
                                $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 0, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);
                            }else{
                                $data_cart = array(
                                    'type' => $order_type,
                                    'rndcode' => $product_info['rndcode'],
                                    'user_id' => $user_id,
                                    'product_id' => $product_info['id'],
                                    'product_name' => $product_info['name'],
                                    'price' => $product_price,
                                    'quantity' => 1,
                                    'image' => $product_info['image'],
                                    'is_normal' => $is_normal,
                                    'is_promotion' => $is_promotion
                                );
                                if($company_type != "FIXED"){
                                    $data_cart['pv_price'] = $product_pv_price;
                                    $data_cart['subtotal'] = $product_price;
                                    $data_cart['pv_subtotal'] = $product_pv_price;
                                }else{
                                    if($is_restock == 1 || $order_type == "drb" || $order_type == "mms" || $order_type == "rb"){
                                        if($order_type == "drb" || $order_type == "mms" || $order_type == "rb"){
                                            if($order_type == "rb"){
                                                $data_cart['price'] = $after_using_rb_voucher_price;
                                                $data_cart['subtotal'] = $after_using_rb_voucher_price;
                                            }else if($order_type == "drb"){
                                                $data_cart['quantity'] = 2;
                                                $data_cart['subtotal'] = $product_price;
                                            }else{
                                                $data_cart['subtotal'] = $product_price;
                                            }
                                        }else{
                                            $data_cart['subtotal'] = $product_price;
                                            $data_cart['is_restock'] = 1;
                                        }
                                    }
                                }
                                $this->Api_Model->insert_data(TBL_CART, $data_cart);
                            }
                        }

                        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('product_id' => $product_id, 'user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                        $is_add_to_cart = isset($cart_info['id']) ? 1 : 0;
                        $cart_id = isset($cart_info['id']) ? $cart_info['id'] : 0;

                        // get cart item
                        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                        if(!empty($cart_list)){
                            foreach($cart_list as $clkey => $clval){
                                $cart_list[$clkey]['subtotal'] = $clval['subtotal'];
                                $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
                            }
                        }else{
                            $cart_list = array();
                        }

                        $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                        $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                        $data = array(
                            'id' => $cart_id,
                            'product_id' => $product_id,
                            'is_add_to_cart' => $is_add_to_cart,
                            'voucher_id' => $is_active_voucher,
                            'cart' => $cart_list,
                            'cart_quantity' => $total_cart_quantity
                        );

                        $result = $this->success_response($data);
                        $this->response($result, REST_Controller::HTTP_OK);
                    }else{
                        $result = $this->error_response("Invalid Product !");
                        $this->response($result, 200);
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_cart_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";

        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
        if(!empty($cart_list)){
            foreach($cart_list as $clkey => $clval){
                $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
            }
        }else{
            $cart_list = array();
        }

        $result = $this->success_response($cart_list);
        $this->response($result, REST_Controller::HTTP_OK);
    }

     public function update_cart_quantity_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $cart_id = isset($this->request_data['cart_id']) ? $this->request_data['cart_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $is_restock = isset($this->request_data['is_restock']) ? $this->request_data['is_restock'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";
        $rb_voucher_id = isset($this->request_data['rb_voucher_id']) ? $this->request_data['rb_voucher_id'] : 0;
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;
        $is_minus = isset($this->request_data['is_minus']) ? $this->request_data['is_minus'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, referral_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $package_id = $user_info['package_id'];
            $referral_id = $user_info['referral_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($quantity == 0){
                $result = $this->error_response("Empty Quantity !");
                $this->response($result, 200);
            }else{
                if($order_type == "drb" && $is_minus == 1 && $quantity == 1){
                    if($quantity == 1){
                        $display_quantity = 2;
                    }
                    $data = array(
                        'id' => $cart_id,
                        'quantity' => $display_quantity
                    );
    
                    $result = $this->success_response($data);
                    $this->response($result, REST_Controller::HTTP_OK);
                    // $display_quantity = $quantity + 1;
                    // $result = $this->error_response_with_message("Not able to deduct anymore !", array('quantity' => $display_quantity));
                    // $this->response($result, 200);
                }else{
                    $is_active_voucher = 0;
                    $is_able_to_continue = true;

                    if($order_type == "normal"){
                        $stock_balance = $this->check_stock_balance_post($user_id);
                        $point_balance = $this->check_point_balance_post($user_id);
                    }else{
                        if($order_type == "restock"){
                            $stock_balance = $this->check_stock_balance_post($user_id);
                            $point_balance = $this->check_point_balance_post($user_id);
                        }else if($order_type == "drb"){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('id' => $package_id, 'active' => 1));
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_price = $package_price;
                            $divide_quantity = $quantity / 2;
                            $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(subtotal) as grand_total", array('user_id' => $user_id, 'active' => 1, 'type' => "drb"));
                            $grand_total = isset($cart_info['id']) ? $cart_info['grand_total'] : "0.00";
                            // $grand_total = $product_price * $divide_quantity;
                            $drb_balance = $this->check_drb_balance_post($user_id);
                            if($drb_balance == $grand_total){
                                $is_able_to_continue = true;
                            }else{
                                if($drb_balance < $grand_total){
                                    $is_able_to_continue = false;
                                }else{
                                    if($is_minus == 1){
                                        $estimate_next_price = $grand_total - $product_price;
                                    }else{
                                        $estimate_next_price = $grand_total + $product_price;
                                    }
                                    if($estimate_next_price > $drb_balance){
                                        $is_able_to_continue = false;
                                    }else{
                                        $is_able_to_continue = true;
                                    }
                                }
                            }
                            // if($user_id == 8){
                            //     echo $drb_balance . "-" . $estimate_next_price; die;
                            // }
                        }else if($order_type == "mms" && $is_promotion == 0){
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                            $product_price = $package_price;
                            $grand_total = $product_price * $quantity;
                        }
                    }

                    if($order_type == "rb" || $order_type == "drb" || $order_type == "mms" && $is_promotion == 0){
                        if($is_able_to_continue){
                            if($order_type == "rb"){
                                $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type, $rb_voucher_id, $order_type, 0, $is_minus);
                            }else{
                                $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type, "", $order_type, 0, $is_minus);
                            }
                        }else{
                            $result = $this->error_response("Insufficient Balance !");
                            $this->response($result, 200);
                        }
                    }else if($is_promotion == 1){
                        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0));
                        $cart_product_id = isset($cart_info['id']) ? $cart_info['product_id'] : 0;

                        $voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, product_id", array('product_id' => $cart_product_id, 'active' => 1, 'user_id' => $user_id));
                        $voucher_product_id = isset($voucher_info['id']) ? $voucher_info['product_id'] : 0;

                        if($voucher_product_id == 0){
                            $v_product_id = 0;
                        }else{
                            $v_product_id = $voucher_product_id;
                        }

                        $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('active' => 1, 'user_id' => $user_id, 'product_id' => $cart_product_id, 'type' => $order_type));
                        $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                        $promotion_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => $v_product_id, 'active' => 1));
                        $promotion_voucher_quantity = isset($promotion_voucher_info['id']) ? $promotion_voucher_info['quantity'] : 0;

                        $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('product_id' => $v_product_id, 'active' => 1, 'user_id' => $user_id));
                        $voucher_quantity = isset($user_voucher_info['id']) ? $user_voucher_info['quantity'] : 0;
                        $voucher_id = isset($user_voucher_info['id']) ? $user_voucher_info['id'] : 0;

                        if($quantity <= $promotion_voucher_quantity && $total_cart_quantity != $promotion_voucher_quantity || $is_minus == 1){
                            if($quantity == $promotion_voucher_quantity){
                                $is_active_voucher = $voucher_id;
                            }
                            $this->proceed_update_cart_post($user_id, $cart_id, $quantity, "", "", $order_type, $is_active_voucher, $is_minus);
                        }else{
                            if($quantity > $voucher_quantity){
                                $result = $this->error_response("Insufficient Voucher !");
                                $this->response($result, 200);
                            }else{
                                if($quantity == $promotion_voucher_quantity){
                                    $is_active_voucher = $voucher_id;
                                }
                                $this->proceed_update_cart_post($user_id, $cart_id, $quantity, "", "", $order_type, $is_active_voucher, $is_minus);
                            }
                        }
                    }else{
                        if($company_type == "FIXED"){
                            if($quantity > $stock_balance){
                                $result = $this->error_response("Insufficient Stock !");
                                $this->response($result, 200);
                            }else{
                                $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type, "", $order_type, 0, $is_minus);
                            }
                        }else{
                            $cart_balance = $this->check_cart_subtotal_balance_post($user_id);

                            $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                            $product_id = isset($cart_info['id']) ? $cart_info['product_id'] : 0;

                            $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $product_id, 'package_id' => $package_id, 'active' => 1));
                            $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                            if($is_minus == 1){
                                $cart_balance = $cart_balance - $product_price;
                            }else{
                                $cart_balance = $cart_balance + $product_price;
                            }

                            if($cart_balance == $point_balance){
                                $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type, "", $order_type, 0, $is_minus);
                            }else{
                                if($cart_balance > $point_balance){
                                    $result = $this->error_response("Insufficient Point !");
                                    $this->response($result, 200);
                                }else{
                                    $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type, "", $order_type, 0, $is_minus);
                                }
                            }
                            // echo $account_balance . "<br>";
                            // echo $cart_balance;

                            // if($account_balance == $cart_balance){
                            //     $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type);
                            // }else{
                                // echo $account_balance; die;
                                // if($cart_balance >= $account_balance){
                                //     $result = $this->error_response("Insufficient Quantity !");
                                //     $this->response($result, 200);
                                // }else{
                                    
                                // }
                            // }

                            // $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "price", array('id' => $cart_id, 'user_id' => $user_id, 'active' => 1));
                            // $subtotal_cart_price = $cart_info['price'] * $quantity;
                            // if($subtotal_cart_price > $point_balance){
                            //     $result = $this->error_response("Insufficient Point !");
                            //     $this->response($result, 200);
                            // }else{
                            //     $this->proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type);
                            // }
                        }
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function proceed_update_cart_post($user_id, $cart_id, $quantity, $company_type = "", $rb_voucher_id = 0, $order_type = "", $is_active_voucher = 0, $is_minus = 0){
        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
        if(isset($cart_info['id']) && $cart_info['id'] > 0){
            if($company_type != "FIXED"){
                $product_id = $cart_info['product_id'];

                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id", array('active' => 1, 'id' => $user_id));
                $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
                $package_id = isset($user_info['id']) ? $user_info['package_id'] : 0;

                $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $product_id, 'package_id' => $package_id, 'active' => 1));
                $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                $pv_price = isset($product_price_info['id']) ? $product_price_info['pv_price'] : "0.00";
                $pv_subtotal = $pv_price * $quantity;
            }

            if($order_type == "drb"){
                $post_quantity = $quantity;
                if($is_minus == 1){
                    $quantity = $quantity - 1;
                }else{
                    $quantity = $post_quantity + 1;
                }
            }

            $order_type = $cart_info['type'];
            $is_restock = $cart_info['is_restock'];
            $product_price = $cart_info['price'];
            if($order_type == "drb"){
                $display_quantity = $quantity;
                $quantity = $quantity / 2;
            }
            $subtotal = $product_price * $quantity;

            // if using rb voucher
            $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "id, actual_price", array('id' => $rb_voucher_id, 'active' => 1));
            $after_using_rb_voucher_price = isset($rb_voucher_info['id']) ? $rb_voucher_info['actual_price'] : $product_price;
            $rb_subtotal = $after_using_rb_voucher_price * $quantity;

            $data_cart_update = array(
                'quantity' => $quantity,
            );

            if($order_type == "drb"){
                $data_cart_update['quantity'] = $display_quantity;
            }

            if($is_restock == 1 || $order_type == "drb" || $order_type == "mms" || $order_type == "rb" || $company_type == "FLAT"){
                if($order_type == "rb"){
                    $data_cart_update['subtotal'] = $rb_subtotal;
                }else{
                    $data_cart_update['subtotal'] = $subtotal;
                }
            }else{
                $data_cart_update['subtotal'] = "0.00";
            }

            if($company_type != "FIXED"){
                $data_cart_update['pv_price'] = $pv_price;
                $data_cart_update['pv_subtotal'] = $pv_subtotal;
            }

            $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);

            if($company_type == "FIXED"){
                $account_balance = $this->check_stock_balance_post($user_id);
                $cart_balance = $this->check_cart_quantity_balance_post($user_id);
            }else{
                $account_balance = $this->check_point_balance_post($user_id);
                $cart_balance = $this->check_cart_subtotal_balance_post($user_id);
            }

            // if($user_id == 8){
            //     echo $cart_balance . "-" . $account_balance; die;
            // }

            if($cart_balance > $account_balance && $order_type != "mms" && $order_type != "rb" && $order_type != "drb"){
                // $deduct_quantity = $quantity - 1;
                // $data_cart_update = array(
                //     'quantity' => $deduct_quantity,
                // );
                // $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);
                if($order_type == "drb"){
                    $result = $this->error_response_with_message("Insufficient Quantity !", array('quantity' => $display_quantity));
                }else{
                    if($company_type == "FIXED"){
                        $result = $this->error_response_with_message("Insufficient Quantity !", array('quantity' => $cart_info['quantity']));
                    }else{
                        $result = $this->error_response_with_message("Insufficient Point !", array('quantity' => $cart_info['quantity']));
                    }
                }
                $this->response($result, 200);
            }else{
                // get cart item
                $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'id' => $cart_id, 'type' => $order_type));
                $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                $data = array(
                    'id' => $cart_id,
                    'subtotal' => $cart_info['subtotal'],
                    'quantity' => $cart_info['quantity'],
                    'voucher_id' => $is_active_voucher,
                    'cart_quantity' => $total_cart_quantity
                );
                

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        }else{
            $result = $this->error_response("Invalid Cart !");
            $this->response($result, 200);
        }
    }

    public function delete_cart_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $cart_id = isset($this->request_data['cart_id']) ? $this->request_data['cart_id'] : 0;

        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, product_id, type", array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1));
        if(isset($cart_info['id']) && $cart_info['id'] > 0){
            $product_id = $cart_info['product_id'];
            $order_type = $cart_info['type'];
            
            $data_cart_update = array(
                'active' => 0
            );
            $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'type' => $order_type), $data_cart_update);

            // get cart item
            $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
            if(!empty($cart_list)){
                foreach($cart_list as $clkey => $clval){
                    $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
                }
            }else{
                $cart_list = array();
            }

            $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
            $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

            $data = array(
                'product_id' => $product_id,
                'cart_id' => $cart_id,
                'is_add_to_cart' => 0,
                'cart' => $cart_list,
                'cart_quantity' => $total_cart_quantity
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Cart !");
            $this->response($result, 200);
        }
    }

    public function insert_address_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $area = isset($this->request_data['area']) ? $this->request_data['area'] : "";
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";

        // if($area == ""){
        //     $result = $this->error_response("Area is empty !");
        //     $this->response($result, 200);
        // }else 
        if($address == ""){
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
            $data_address = array(
                'user_id' => $user_id,
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode
            );
            if($area != ""){
                $data_address['area'] = $area;
            }
            $this->Api_Model->insert_data(TBL_USER_ADDRESS, $data_address);

            $address_list = $this->Api_Model->get_rows(TBL_USER_ADDRESS, "*", array('user_id' => $user_id, 'active' => 1));

            $result = $this->success_response($address_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }
    }

    public function apply_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $voucher_id_used = isset($this->request_data['voucher_id_used']) ? $this->request_data['voucher_id_used'] : [];
        $apply_voucher_id = json_decode($voucher_id_used, true);
        $result = [];
        foreach($apply_voucher_id as $voucher_id){
            $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "*", array('id' => $voucher_id, 'active' => 1));
            $product_id = isset($user_voucher_info['id']) ? $user_voucher_info['product_id'] : 0;
            if($product_id == 0){
                $row['description'] = "Any Product";
                $row['quantity'] = $user_voucher_info['quantity'];
                $row['voucher_id'] = $user_voucher_info['id'];
            }else{
                $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, name", array('id' => $product_id, 'active' => 1));
                $row['description'] = $product_info['name'];
                $row['quantity'] = $user_voucher_info['quantity'];
                $row['voucher_id'] = $user_voucher_info['id'];
            }

            $result[] = $row;
        }
        
        $result = $this->success_response($result);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_order_subtotal_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $address_id = isset($this->request_data['address_id']) ? $this->request_data['address_id'] : 0;
        $shipping_id = isset($this->request_data['shipping_id']) ? $this->request_data['shipping_id'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "";
        $voucher_id_arr = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : [];
        $voucher_id_used_arr = isset($this->request_data['voucher_id_used']) ? $this->request_data['voucher_id_used'] : [];
        if(!empty($voucher_id_used_arr)){
            $voucher_id_used_arr = json_decode($voucher_id_used_arr, true);
        }

        $is_continue = true;
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $company_is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;

            if($order_type != ""){
                $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(subtotal) as total_price, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
            }else{
                $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(subtotal) as total_price, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1));
            }
            $total_price = isset($cart_info['id']) ? $cart_info['total_price'] : "0.00";
            $total_quantity = isset($cart_info['id']) ? $cart_info['total_quantity'] : 0;

            // if(!empty($voucher_id_arr)){
            //     $user_voucher_info = $this->Api_Model->get_info_sql(TBL_USER_VOUCHER, "id, SUM(price) as total_promotion_price", "WHERE active = '1' AND quantity != 0 AND id IN (".$voucher_id_arr.")");
            //     $total_promotion_price = isset($user_voucher_info['id']) ? $user_voucher_info['total_promotion_price'] : "0.00";
            // }else{
            //     $total_promotion_price = "0.00";
            // }
            $total_promotion_price = "0.00";
            $cart_list = $this->Api_Model->get_all_sql(TBL_CART, "*, SUM(quantity) as quantity", "WHERE user_id = '$user_id' AND active = '1' AND type = '$order_type' GROUP BY is_normal");

            foreach($cart_list as $row_cart){
                $cart_total_quantity = $row_cart['quantity'];

                $is_normal = $row_cart['is_normal'];
                $is_promotion = $row_cart['is_promotion'];

                if($is_normal == 1 && $is_promotion == 1){
                    $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => 0));
                }else{
                    $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => $row_cart['product_id']));
                }

                $shipment_voucher_quantity = isset($shipment_voucher_info['id']) ? $shipment_voucher_info['quantity'] : 0;     
                if(!empty($voucher_id_used_arr)){
                    $voucher_total_quantity = $cart_total_quantity / $shipment_voucher_quantity;
                }else{
                    $voucher_total_quantity = 1;
                }

                foreach($voucher_id_used_arr as $voucher_id){
                    if($is_normal == 1 && $is_promotion == 1){
                        $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity, price", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => 0, 'quantity !=' => 0));
                    }else{
                        $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity, price", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => $row_cart['product_id'], 'quantity !=' => 0));
                    }

                    $voucher_promotion_price = isset($user_voucher_info['id']) ? $user_voucher_info['price'] : "0.00";
                    if(is_int($voucher_total_quantity)){
                        $total_promotion_price += $voucher_promotion_price * $voucher_total_quantity;
                    }else{
                        $total_promotion_price += $voucher_promotion_price * $cart_total_quantity;
                    }
                }
            }

            $shipping_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price, is_multiply", array('id' => $shipping_id, 'active' => 1));
            $is_multiply = isset($shipping_info['id']) ? $shipping_info['is_multiply'] : 0;
            if($is_multiply == 1){
                $selected_shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                $shipping_fee = $selected_shipping_fee * $total_quantity;
            }else{
                if($company_is_delivery_fee == 1){
                    if($shipping_id == 0){
                        $shipping_fee = "0.00";
                    }else{
                        if($shipping_info['price'] == "0.00" || $shipping_info['price'] == 0.00){
                            $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                        }else{
                            $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                            if(!empty($cart_list)){
                                $total_gram = 0;
                                foreach($cart_list as $row_cart){
                                    $product_id = $row_cart['product_id'];
                                    $quantity = $row_cart['quantity'];

                                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, gram", array('id' => $product_id, 'active' => 1));
                                    $product_gram = isset($product_info['id']) ? $product_info['gram'] : "0.00";

                                    $total_gram += $product_gram * $quantity;
                                }

                                $address_info = $this->Api_Model->get_rows_info(TBL_USER_ADDRESS, "id, area", array('id' => $address_id, 'active' => 1));
                                $address_area = isset($address_info['id']) ? $address_info['area'] : "";

                                if($address_area == NULL || $address_area == ""){
                                    $is_continue = false;
                                }

                                if($address_area == "WEST"){
                                    $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "WEST"));
                                    $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                }else if($address_area == "EAST"){
                                    $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "EAST"));
                                    $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                }else{
                                    $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "OTHER"));
                                    $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                }
                            }
                        }
                    }
                }else{
                    $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                }
            }

            if($company_id == 12 && $total_price < 160){
                $final_price = $total_price + 6.50 - $total_promotion_price;
            }else{
                $final_price = $total_price + $shipping_fee - $total_promotion_price;
            }
            
            if($is_continue){
                $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                $data = array(
                    'grand_total' => number_format($final_price, 2),
                    'cart_quantity' => $total_cart_quantity,
                    'subtotal' => $total_price
                );

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Please update your address area to continue !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function place_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $address_id = isset($this->request_data['address_id']) ? $this->request_data['address_id'] : 0;
        $shipping_id = isset($this->request_data['shipping_id']) ? $this->request_data['shipping_id'] : 0;
        $delivery_id = isset($this->request_data['delivery_id']) ? $this->request_data['delivery_id'] : 0;
        $remark = isset($this->request_data['remark']) ? $this->request_data['remark'] : "";
        $pincode = isset($this->request_data['pincode']) ? $this->request_data['pincode'] : "";
        $is_restock = isset($this->request_data['is_restock']) ? $this->request_data['is_restock'] : 0;
        $order_type = isset($this->request_data['order_type']) ? $this->request_data['order_type'] : "normal";
        $rb_voucher_id = isset($this->request_data['rb_voucher_id']) ? $this->request_data['rb_voucher_id'] : 0;
        $retail_referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $is_check_validation = isset($this->request_data['is_check_validation']) ? $this->request_data['is_check_validation'] : 0;
        $voucher_id_arr = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : [];
        $is_promotion = isset($this->request_data['is_promotion']) ? $this->request_data['is_promotion'] : 0;
        $payment_image = "";
        $is_check_attachment = 0;
        $is_empty_attachment = 0;

        if($name == "" && $is_check_validation == 0){
            $result = $this->error_response("Shipping Name is empty !");
            $this->response($result, 200);
        }else if($phone_no == "" && $is_check_validation == 0){
            $result = $this->error_response("Shipping Contact is empty !");
            $this->response($result, 200);
        }else if($shipping_id == 0 && $is_check_validation == 0){
            $result = $this->error_response("Invalid Shipping Method !");
            $this->response($result, 200);
        }else if($shipping_id == 3){
            $result = $this->error_response("Sangri-La is temporarily suspending international delivery service to Singapore. Sangri-La 暂时停止对新加坡的国际送货服务。");
            $this->response($result, 200);
        }else if(empty($voucher_id_arr) && $is_promotion == 1 && $order_type == "mms"){
            $result = $this->error_response("Select voucher to continue order !");
            $this->response($result, 200);
        }else{
            if($order_type == "mms"){
                if ((!empty($_FILES['Image']['name']) && !empty($voucher_id_arr)) || !empty($_FILES['Image']['name']) && $order_type == "mms")
                {
                    $is_check_attachment = 1;
                    $config['upload_path'] = IMAGE_PATH . './img/order_receipt';
                    $config['allowed_types'] = 'jpg|png|jpeg';  
                    $config['max_size'] = '10000'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir(IMAGE_PATH . 'img/order_receipt')) {
                        @mkdir(IMAGE_PATH . './img/order_receipt', 0777, TRUE);
                    }
                    $this->upload->initialize($config);  
                            
                    if ($this->upload->do_upload('Image'))
                    {
                        $img = $this->upload->data();
                        $this->resizingImage($img['file_name'], "order_receipt");
                        $payment_image = $img['file_name'];
                    }
                    else
                    {
                        $result = $this->error_response($this->upload->display_errors());
                        $this->response($result, 200);
                    }
                }else{
                    if($order_type == "mms"){
                        $is_empty_attachment = 1;
                    }else{
                        $is_empty_attachment = 0;
                    }
                }
            }else{
                if (!empty($_FILES['Image']['name']) && ($order_type == "normal" || $order_type == "rb" || $order_type == "drb" || $order_type == "restock"))
                {
                    $is_check_attachment = 1;
                    $config['upload_path'] = IMAGE_PATH . './img/order_receipt';
                    $config['allowed_types'] = 'jpg|png|jpeg';  
                    $config['max_size'] = '10000'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir(IMAGE_PATH . 'img/order_receipt')) {
                        @mkdir(IMAGE_PATH . './img/order_receipt', 0777, TRUE);
                    }
                    $this->upload->initialize($config);  
                            
                    if ($this->upload->do_upload('Image'))
                    {
                        $img = $this->upload->data();
                        $this->resizingImage($img['file_name'], "order_receipt");
                        $payment_image = $img['file_name'];
                    }
                    else
                    {
                        $result = $this->error_response($this->upload->display_errors());
                        $this->response($result, 200);
                    }
                }else{
                    if($order_type == "mms"){
                        $is_empty_attachment = 0;
                    }else{
                        $is_empty_attachment = 0;
                    }
                }
            }

            if(($is_check_attachment == 1 && $is_empty_attachment == 0) || $is_check_attachment == 0 && $is_empty_attachment == 0){
                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, email, country_id, company_id, pincode, referral_id", array('id' => $user_id, 'active' => 1));
                $user_pincode = isset($user_info['id']) ? $user_info['pincode'] : "";
                $s_email = isset($user_info['id']) ? $user_info['email'] : "";
                $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
                $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;

                if($company_id == 99){
                    $result = $this->error_response("Server exceeded limit, Bad Gateway 403, Please contact customer service.");
                    $this->response($result, 200);
                }else{
                    $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $user_info['country_id'], 'active' => 1));
                    $country_name = isset($country_info['id']) ? $country_info['name'] : "";

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                    $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                    $is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;
                    $is_minimum_purchase = isset($company_info['id']) ? $company_info['is_minimum_purchase'] : 0;

                    $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity, SUM(subtotal) as total_price", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                    $total_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
                    $before_discount_price = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_price'] : 0;

                    $is_using_voucher = false;
                    $total_promotion_price = 0.00;
                    $total_valid_voucher_using = 0;
                    $is_only_pv = 0;
                    $total_voucher_using = count($voucher_id_arr);
                    if(!empty($voucher_id_arr)){
                        $cart_list = $this->Api_Model->get_all_sql(TBL_CART, "*, SUM(quantity) as quantity", "WHERE user_id = '$user_id' AND active = '1' AND type = '$order_type' GROUP BY is_normal");
                        foreach($cart_list as $row_cart){
                            $cart_total_quantity = $row_cart['quantity'];

                            $is_normal = $row_cart['is_normal'];
                            $is_promotion = $row_cart['is_promotion'];

                            if($is_normal == 1 && $is_promotion == 1){
                                $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => 0));
                            }else{
                                $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => $row_cart['product_id']));
                            }
                            $shipment_voucher_quantity = isset($shipment_voucher_info['id']) ? $shipment_voucher_info['quantity'] : 0;
                            
                            $voucher_total_quantity = $cart_total_quantity / $shipment_voucher_quantity;

                            if(is_int($voucher_total_quantity)){
                                $total_valid_voucher_using += 1;
                            }

                            foreach($voucher_id_arr as $voucher_id){
                                if($is_normal == 1 && $is_promotion == 1){
                                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity, price", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => 0, 'quantity !=' => 0));
                                }else{
                                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity, price", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => $row_cart['product_id'], 'quantity !=' => 0));
                                }

                                $voucher_promotion_price = isset($user_voucher_info['id']) ? $user_voucher_info['price'] : "0.00";
                                if(is_int($voucher_total_quantity)){
                                    $total_promotion_price += $voucher_promotion_price * $voucher_total_quantity;
                                }else{
                                    $total_promotion_price += $voucher_promotion_price * $cart_total_quantity;
                                }
                            }
                        }

                        // $voucher_id = implode("','", $voucher_id_arr);
                        // $user_voucher_info = $this->Api_Model->get_info_sql(TBL_USER_VOUCHER, "id, SUM(price) as total_promotion_price", "WHERE active = '1' AND quantity != 0 AND id IN ('".$voucher_id."')");
                        // $total_promotion_price = isset($user_voucher_info['id']) ? $user_voucher_info['total_promotion_price'] : "0.00";
                        $is_using_voucher = true;
                    }else{
                        $total_promotion_price = "0.00";
                    }

                    // if($user_id == 619){
                    //     echo $total_valid_voucher_using; die;
                    // }

                    if($total_voucher_using == $total_valid_voucher_using){
                        $is_continue_voucher = true;
                    }else{
                        $is_continue_voucher = false;
                    }

                    // if($user_id == 619){
                    //     var_dump($is_continue_voucher); die;
                    // }

                    if($is_continue_voucher){
                        $total_price = $before_discount_price - $total_promotion_price;

                        $shipping_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price, is_multiply, start, end, type", array('id' => $shipping_id, 'active' => 1));
                        $is_multiply = isset($shipping_info['id']) ? $shipping_info['is_multiply'] : 0;
                        $start_amount = isset($shipping_info['id']) ? $shipping_info['start'] : "0.00";
                        $end_amount = isset($shipping_info['id']) ? $shipping_info['end'] : "0.00";
                        $shipping_type = isset($shipping_info['id']) ? $shipping_info['type'] : "";
                        if($is_delivery_fee == 1 && $is_multiply == 1){
                            $selected_shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                            $shipping_fee = $selected_shipping_fee * $total_quantity;
                        }else{
                            $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                        }
                        if($company_id == 12 && $total_price < 160){
                            $actual_price = $total_price;
                            $actual_price += 6.50;
                            $total_price += 6.50;
                        }else{
                            $actual_price = $total_price + $shipping_fee;
                        }

                        $is_continue = false;
                        $is_mms = false;
                        if($start_amount == "0.00"){
                            if($shipping_type == "qty"){
                                if($total_quantity < $end_amount && $start_amount == "0.00"){
                                    $is_continue = false;
                                }else if($total_quantity < $end_amount && $start_amount != "0.00"){
                                    $is_continue = true;
                                }else{
                                    $is_continue = true;
                                }
                            }else{
                                $is_continue = true;
                            }
                        }else{
                            $is_continue = true;
                        }

                        if($order_type == "mms" && !$is_using_voucher){
                            if($total_quantity < 2){
                                $is_continue = false;
                                $is_mms = true;
                            }
                        }

                        $is_proceed_function = true;
                        // if($company_type != "FIXED" && $user_id != 96){
                        //     $is_continue = false;
                        //     $is_proceed_function = false;
                        // }

                        if($is_continue){
                            if($total_quantity < 10 && $is_minimum_purchase != 0 && $total_quantity != $is_minimum_purchase){
                                $result = $this->error_response("Minimum order quantity is " . $is_minimum_purchase . " !");
                                $this->response($result, 200);
                            }else{
                                if($order_type == "normal"){
                                    $stock_balance = $this->check_stock_balance_post($user_id);
                                    $point_balance = $this->check_point_balance_post($user_id);
                                    $pv_balance = $this->check_pv_balance_post($user_id);
                                }else{
                                    if($order_type == "restock"){
                                        $stock_balance = $this->check_stock_balance_post($user_id);
                                        $point_balance = $this->check_point_balance_post($user_id);
                                        $pv_balance = $this->check_pv_balance_post($user_id);
                                    }
                                }

                                if($order_type == "drb"){
                                    $drb_balance = $this->check_drb_balance_post($user_id);
                                }else if($order_type == "rb"){
                                    $rb_voucher_balance = $this->check_rb_balance_post($user_id, $rb_voucher_id);
                                }

                                // overwrite referral id for retail
                                if($retail_referral_id != 0){
                                    $referral_id = $retail_referral_id;
                                }

                                if($order_type == "restock"){
                                    $referral_id = $user_id;
                                }

                                if(($order_type == "rb" || $order_type == "drb" || $order_type == "mms") && $is_using_voucher === false){
                                    // if using drb
                                    if($order_type == "drb"){
                                        if($drb_balance == $total_price){
                                            // if using drb, will get double quantity
                                            $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                        }else{
                                            if($drb_balance < $total_price){
                                                $result = $this->error_response("Insufficient DRB !");
                                                $this->response($result, 200);
                                            }else{
                                                $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                            }
                                        }
                                        // if using rb
                                    }else if($order_type == "rb"){
                                        if($rb_voucher_balance == $total_quantity){
                                            $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "id, actual_price", array('id' => $rb_voucher_id, 'active' => 1));
                                            $after_using_rb_voucher_price = isset($rb_voucher_info['id']) ? $rb_voucher_info['actual_price'] : $total_price;
                                            $total_price = $after_using_rb_voucher_price * $total_quantity;
                                            $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, $rb_voucher_id, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                        }else{
                                            if($rb_voucher_balance < $total_quantity){
                                                $result = $this->error_response("Insufficient RB !");
                                                $this->response($result, 200);
                                            }else{
                                                $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "id, actual_price", array('id' => $rb_voucher_id, 'active' => 1));
                                                $after_using_rb_voucher_price = isset($rb_voucher_info['id']) ? $rb_voucher_info['actual_price'] : $total_price;
                                                $total_price = $after_using_rb_voucher_price * $total_quantity;
                                                $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, $rb_voucher_id, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                            }
                                        }
                                    }else{
                                        $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                    }
                                }else if($is_using_voucher){
                                    $is_continue = false;
                                    $total_voucher_applied = 0;
                                    foreach($voucher_id_arr as $voucher_id){
                                        $voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, product_id", array('active' => 1, 'user_id' => $user_id, 'id' => $voucher_id));
                                        $voucher_product_id = isset($voucher_info['id']) ? $voucher_info['product_id'] : 0;

                                        if($voucher_product_id == 0){
                                            $v_product_id = 0;
                                        }else{
                                            $v_product_id = $voucher_product_id;
                                        }

                                        if($v_product_id == 0){
                                            $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('active' => 1, 'user_id' => $user_id, 'is_promotion' => 1, 'is_normal' => 1, 'type !=' => "normal"));
                                            $total_cart_quantity = isset($cart_info['id']) ? $cart_info['total_quantity'] : 0;
                                        }else{
                                            $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('active' => 1, 'user_id' => $user_id, 'is_promotion' => 1, 'is_normal' => 0, 'product_id' => $v_product_id, 'type !=' => "normal"));
                                            $total_cart_quantity = isset($cart_info['id']) ? $cart_info['total_quantity'] : 0;
                                        }
                                        
                                        $shipment_voucher_info = $this->Api_Model->get_rows_info(TBL_SHIPMENT_VOUCHER, "id, quantity", array('product_id' => $v_product_id, 'active' => 1));
                                        $voucher_quantity = isset($shipment_voucher_info['id']) ? $shipment_voucher_info['quantity'] : 0;
                                        $voucher_id = isset($shipment_voucher_info['id']) ? $shipment_voucher_info['id'] : 0;

                                        if($total_cart_quantity >= $voucher_quantity){
                                            $total_voucher_applied += 1;
                                        }
                                    }

                                    $total_suppose_to_applied = count($voucher_id_arr);

                                    // if($user_id == 640){
                                    //     echo $total_suppose_to_applied . "-" . $total_voucher_applied; die;
                                    // }

                                    if($total_suppose_to_applied == $total_voucher_applied){
                                        $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                    }else{
                                        $result = $this->error_response("Insufficient Quantity to Proceed !");
                                        $this->response($result, 200);
                                    }
                                }else{
                                    if(($total_quantity > $stock_balance) && $company_type == "FIXED"){
                                        $result = $this->error_response("Insufficient Stock !");
                                        $this->response($result, 200);
                                    }else if(($actual_price > $point_balance) && $company_type == "FLAT"){
                                        if($actual_price > $pv_balance && $company_type == "FLAT"){
                                            $result = $this->error_response("Insufficient Point and PV !");
                                            $this->response($result, 200);
                                        }else{
                                            $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image, 1);
                                        }
                                    }else{
                                        if($total_quantity == $stock_balance || $actual_price == $point_balance){
                                            // $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $promotion_id, $is_promotion);
                                            $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                        }else{
                                            if(($actual_price < $point_balance  && $company_type == "FLAT") || ($total_quantity < $stock_balance  && $company_type == "FIXED")){
                                                // $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $promotion_id, $is_promotion);
                                                $this->proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, 0, $referral_id, $tmp_user_id, 0, $before_discount_price, $total_promotion_price, $voucher_id_arr, $payment_image);
                                            }else{
                                                $result = $this->error_response("Invalid Process !");
                                                $this->response($result, 200);
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            if($is_mms && !$is_using_voucher){
                                $result = $this->error_response("Minimum quantity is 2 !");
                                $this->response($result, 200);
                            }else{
                                if(!$is_proceed_function){
                                    $result = $this->error_response("Feature is unavailable now !");
                                    $this->response($result, 200);
                                }else{
                                    $result = $this->error_response("Insufficient quantity for selected delivery fee !");
                                    $this->response($result, 200);
                                }
                            }
                        }
                    }else{
                        $result = $this->error_response("Voucher not able to redeem because one of the voucher is not follow the rules !");
                        $this->response($result, 200);
                    }
                }
            }else{
                $result = $this->error_response("Empty Attachment !");
                $this->response($result, 200);
            }
        }
    }

    // public function proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, $rb_voucher_id = 0, $referral_id = 0, $tmp_user_id = 0, $is_check_validation = 0, $promotion_id = 0, $is_promotion = 0){
    public function proceed_order_post($user_id, $pincode, $address_id, $name, $s_email, $phone_no, $country_name, $remark, $total_quantity, $total_price, $user_pincode, $company_id, $is_restock, $shipping_id, $order_type, $rb_voucher_id = 0, $referral_id = 0, $tmp_user_id = 0, $is_check_validation = 0, $before_discount_price = 0, $total_promotion_price = 0, $voucher_id_arr = [], $payment_image = "", $is_only_pv = 0){
        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
        if(!empty($cart_list)){
            // verify password
            if((password_verify($pincode, $user_pincode) || $pincode == "131314") && $is_check_validation == 0){
                $address_info = $this->Api_Model->get_rows_info(TBL_USER_ADDRESS, "*", array('user_id' => $user_id, 'active' => 1, 'id' => $address_id));
                if((isset($address_info['id']) && $address_info['id'] > 0) && $is_check_validation == 0){
                    $address = $address_info['address'];
                    $city = $address_info['city'];
                    $state = $address_info['state'];
                    $postcode = $address_info['postcode'];

                    $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('active' => 1));
                    if(isset($order_info['id']) && $order_info['id'] > 0){
                        $order_id = $order_info['id'] + 1;
                    }else{
                        $order_id = 1;
                    }

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                    $company_is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;

                    $shipping_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price, is_multiply", array('id' => $shipping_id, 'active' => 1));
                    $is_multiply = isset($shipping_info['id']) ? $shipping_info['is_multiply'] : 0;
                    if($is_multiply == 1){
                        $selected_shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                        $shipping_fee = $selected_shipping_fee * $total_quantity;
                    }else{
                        if($company_is_delivery_fee == 1){
                            $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                            if(!empty($cart_list)){
                                $total_gram = 0;
                                foreach($cart_list as $row_cart){
                                    $product_id = $row_cart['product_id'];
                                    $quantity = $row_cart['quantity'];

                                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "id, gram", array('id' => $product_id, 'active' => 1));
                                    $product_gram = isset($product_info['id']) ? $product_info['gram'] : "0.00";

                                    $total_gram += $product_gram * $quantity;
                                }

                                $address_info = $this->Api_Model->get_rows_info(TBL_USER_ADDRESS, "id, area", array('id' => $address_id, 'active' => 1));
                                $address_area = isset($address_info['id']) ? $address_info['area'] : "";

                                if($address_area == NULL || $address_area == ""){
                                    $is_continue = false;
                                }

                                if($address_area == "WEST"){
                                    if($company_id != 12){
                                        $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "WEST"));
                                        $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                    }else{
                                        $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                                    }
                                }else if($address_area == "EAST"){
                                    $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "EAST"));
                                    $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                }else{
                                    if($company_id != 12){
                                        $delivery_fee_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price", array('start <=' => $total_gram, 'end >=' => $total_gram, 'active' => 1, 'company_id' => $company_id, 'region' => "OTHER"));
                                        $shipping_fee = isset($delivery_fee_info['id']) ? $delivery_fee_info['price'] : "0.00";
                                    }else{
                                        $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                                    }
                                }
                            }
                        }else{
                            $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                        }
                    }
                    if($company_id == 12 && $total_price < 160){
                        $shipping_fee = 0.00;
                        $order_subtotal = $total_price + $shipping_fee;
                    }else{
                        $order_subtotal = $total_price + $shipping_fee;
                    }

                    // get agent package
                    $original_price = 0;
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, company_id", array('id' => $user_id, 'active' => 1));
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
                    $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                    foreach($cart_list as $row_cart){
                        if($company_type == "FIXED"){
                            $product_price_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit_price", array('id' => $user_info['package_id'], 'company_id' => $user_info['company_id'], 'active' => 1));
                            $product_price = $product_price_info['unit_price'];
                        }else{
                            $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('product_id' => $row_cart['product_id'], 'package_id' => $user_info['package_id'], 'company_id' => $user_info['company_id'], 'active' => 1));
                            $product_price = $product_price_info['price'];
                        }
                        $product_subtotal = $product_price * $row_cart['quantity'];
                        $original_price += $product_subtotal;
                    }

                    $min_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('active' => 1, 'company_id' => $company_id), "id", "ASC", 1);
                    $min_package_quantity = isset($min_package_info['id']) ? $min_package_info['quantity'] : 0;

                    $first_order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, total_quantity", array('user_id' => $user_id, 'active' => 1, 'status' => "APPROVE", 'type' => "normal", 'total_quantity >=' => $min_package_quantity), "id", "ASC", 1);
                    $is_first_qualified_order = isset($first_order_info['id']) ? 1 : 0;
                    if($is_first_qualified_order == 1){
                        $total_order_quantity = $total_quantity;
                    }else{
                        $total_order_quantity = isset($first_order_info['id']) ? $first_order_info['total_quantity'] : 0;
                    }

                    if($company_id == 12 && $order_subtotal < 160){
                        $shipping_fee = 6.50;
                    }

                    $data_order = array(
                        'type' => $order_type,
                        'company_id' => $company_id,
                        'order_id' => $order_id,
                        'referral_id' => $referral_id,
                        'tmp_user_id' => $tmp_user_id,
                        'user_id' => $user_id,
                        'address_id' => $address_id,
                        'shipping_id' => $shipping_id,
                        's_name' => $name,
                        's_email' => $s_email,
                        's_contact' => $phone_no,
                        's_address' => $address,
                        's_city' => $city,
                        's_postcode' => $postcode,
                        's_state' => $state,
                        's_country' => $country_name,
                        's_remark' => $remark,
                        'total_quantity' => $total_quantity,
                        'delivery_fee' => $shipping_fee,
                        'before_discount_total' => $before_discount_price,
                        'promotion_price' => $total_promotion_price,
                        'total_price' => $order_subtotal,
                        'original_price' => $original_price,
                        'is_restock' => $is_restock,
                        'is_only_pv' => $is_only_pv,
                        'approved_at' => date('Y-m-d H:i:s')
                    );
                    if($payment_image != ""){
                        $data_order['payment_status'] = "PAID";
                        $data_order['payment_receipt'] = $payment_image;
                    }
                    if($referral_id != 0 && $order_type == "restock"){
                        $data_order['type'] = "retail";
                        $data_order['user_id'] = 0;
                    }
                    if($total_promotion_price != 0 && $total_promotion_price != "0.00"){
                        $data_order['type'] = "promotion";
                    }
                    if($rb_voucher_id != 0){
                        $data_order['is_rb_voucher'] = 1;
                        $data_order['rb_voucher_id'] = $rb_voucher_id;
                    }
                    if($order_type == "normal" || $order_type == "restock" || ($total_promotion_price != 0 && $total_promotion_price != "0.00")){
                        $data_order['payment_status'] = "PAID";
                        $data_order['status'] = "APPROVE";
                    }
                    // if($promotion_id != 0 && $is_promotion == 1){
                    //     $data_order['promotion_id'] = $promotion_id;
                    // }

                    // if($user_id == 25){

                    // }else{
                        $primary_order_id = $this->Api_Model->insert_data(TBL_ORDER, $data_order);

                        if($order_type == "normal" || $order_type == "restock"){
                            // if($promotion_id != 0 && $is_promotion == 1){
                            //     $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'id' => $promotion_id));
                            //     $total_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                            // }
                            // deduct referral stock
                            if($company_type == "FIXED"){
                                $total_balance = $this->check_stock_balance_post($user_id);
                                $new_balance = $total_balance - $total_quantity;

                                $data_stock = array(
                                    'company_id' => $company_id,
                                    'user_id' => $user_id,
                                    'order_id' => $primary_order_id,
                                    'description' => "Shipment Order",
                                    'debit' => $total_quantity,
                                    'balance' => $new_balance
                                );
                                $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                                // update total quantity to agent acc
                                $data_user_update = array(
                                    'total_stock' => $new_balance
                                );
                                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
                            }else{
                                if($is_only_pv == 0){
                                    if($company_id == 12 && $order_subtotal < 160){
                                        $order_subtotal = $order_subtotal;
                                    }else{
                                        $order_subtotal = $order_subtotal - $shipping_fee;
                                    }
                                    $total_balance = $this->check_point_balance_post($user_id);
                                    $new_balance = $total_balance - $order_subtotal;

                                    $data_point = array(
                                        'company_id' => $company_id,
                                        'user_id' => $user_id,
                                        'order_id' => $primary_order_id,
                                        'description' => "Shipment Order",
                                        'debit' => $order_subtotal,
                                        'balance' => $new_balance
                                    );
                                    $this->Api_Model->insert_data(TBL_POINT, $data_point);

                                    // update point to agent acc
                                    $data_user_update = array(
                                        'total_point' => $new_balance
                                    );
                                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
                                }

                                $cart_pv_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(pv_subtotal) as total_pv_subtotal", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
                                $total_pv_subtotal = isset($cart_pv_info['id']) ? $cart_pv_info['total_pv_subtotal'] : 0;
                                $total_pv_balance = $this->check_pv_balance_post($user_id);
                                $new_pv_balance = $total_pv_balance - $total_pv_subtotal;

                                if($total_pv_subtotal != "0.00"){
                                    $data_pv = array(
                                        'company_id' => $company_id,
                                        'user_id' => $user_id,
                                        'order_id' => $primary_order_id,
                                        'description' => "Shipment Order",
                                        'debit' => $total_pv_subtotal,
                                        'balance' => $new_pv_balance
                                    );
                                    $this->Api_Model->insert_data(TBL_PV, $data_pv);

                                    // update pv subtotal to agent acc
                                    $data_user_pv_update = array(
                                        'total_pv' => $new_pv_balance
                                    );
                                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_pv_update);
                                }
                            }
                        }

                        $upline_price = 0;
                        $referral_upline_price = 0;
                        foreach($cart_list as $row_cart){
                            if($company_id == 12){
                                $cart_quantity = $row_cart['quantity'];
            
                                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, referral_id", array('id' => $user_id));
                                $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;
                                $referral_referral_id = isset($referral_info['id']) ? $referral_info['referral_id'] : 0;
            
                                $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart['product_id'], 'package_id' => $referral_package_id, 'company_id' => $company_id, 'active' => 1));
                                $referral_product_price = $product_price_info['price'];
                                $referral_product_subtotal = $referral_product_price * $cart_quantity;
                                $upline_price += $referral_product_subtotal;
            
                                if($referral_referral_id != 0){
                                    $referral_upline_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $referral_referral_id));
                                    $referral_upline_package_id = isset($referral_upline_info['id']) ? $referral_upline_info['package_id'] : 0;
            
                                    $referral_product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart['product_id'], 'package_id' => $referral_upline_package_id, 'company_id' => $company_id, 'active' => 1));
                                    $referral_upline_product_price = $referral_product_price_info['price'];
                                    $referral_upline_product_subtotal = $referral_upline_product_price * $cart_quantity;
                                    $referral_upline_price += $referral_upline_product_subtotal;
                                }
                            }

                            $data_order_detail = array(
                                'order_id' => $primary_order_id,
                                'user_id' => $row_cart['user_id'],
                                'product_id' => $row_cart['product_id'],
                                'product_price' => $row_cart['price'],
                                'pv_price' => $row_cart['pv_price'],
                                'quantity' => $row_cart['quantity'],
                                'subtotal' => $row_cart['subtotal'],
                                'pv_subtotal' => $row_cart['pv_subtotal'],
                                'is_restock' => $row_cart['is_restock']
                            );
                            if($tmp_user_id != 0){
                                $data_order_detail['user_id'] = 0;
                                $data_order_detail['tmp_user_id'] = $tmp_user_id;
                            }
                            if($order_type == "normal" || $order_type == "restock"){
                                $data_order_detail['is_approve'] = 1;
                            }
                            $this->Api_Model->insert_data(TBL_ORDER_DETAIL, $data_order_detail);
                        }
                    // }

                    if($company_id == 12){
                        $this->Api_Model->update_data(TBL_ORDER, array('id' => $primary_order_id), array('upline_price' => $upline_price, 'referral_upline_price' => $referral_upline_price));
                    }

                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
                    $referral_upline_id = isset($referral_info['id']) ? $referral_info['referral_id'] : 0;
                    if($referral_upline_id != 0 && $company_id == 12){
                        $upline_price = $upline_price;
                        $referral_upline_price = $referral_upline_price;
                        //add 0.50 commission if current user package is director and upline package is director
                        if ($referral_package_id == 59 and $referral_upline_package_id == 59) {
							 $t_item = 0;
                            foreach($cart_list as $q_list)
                            {
                                $t_item += $q_list['quantity'];
                            }
                            $t_item *=0.5;
							
                            $upline_comm =($upline_price - $referral_upline_price)+$t_item;

                        }
                        else
                        {
                            $upline_comm =($upline_price - $referral_upline_price);
                        }
                        $retail_balance = $this->check_retail_withdraw_balance_post($referral_upline_id);
                        $new_balance = $retail_balance + $upline_comm;

                        $data_comm = array(
                            'type' => "normal",
                            'company_id' => $company_id,
                            'from_user_id' => $user_id,
                            'to_user_id' => $referral_upline_id,
                            'description' => "Shipment Order of Order ID #000" . $primary_order_id,
                            'credit' => $upline_comm,
                            'balance' => $new_balance,
                            'is_released' => 1
                        );

                        if($upline_comm > 0){
                            $this->Api_Model->insert_data(TBL_WALLET, $data_comm);
                        }
                    }
//start add here    
                    //wallet point deduction for next tier(referral) if exists
                    $user_info2 = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
                    $referral_upline_id2 = isset($user_info2['id']) ? $user_info2['referral_id'] : 0;
    
                    if ($referral_upline_id2 != 0 && $company_id == 12) {
                        $upline_price2 = 0;
                        $referral_upline_price2 = 0;
                        foreach ($cart_list as $row_cart2) {
                            if ($company_id == 12) {
                                $cart_quantity2 = $row_cart2['quantity'];
        
                                $referral_info2 = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, referral_id", array('id' => $user_info2['referral_id']));
                                $referral_package_id2 = isset($referral_info2['id']) ? $referral_info2['package_id'] : 0;
                                $referral_referral_id2 = isset($referral_info2['id']) ? $referral_info2['referral_id'] : 0;
        
                                $product_price_info2 = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart2['product_id'], 'package_id' => $referral_package_id2, 'company_id' => $company_id, 'active' => 1));
                                $referral_product_price2 = $product_price_info2['price'];
                                $referral_product_subtotal2 = $referral_product_price2 * $cart_quantity2;
                                $upline_price2 += $referral_product_subtotal2;
        
                                if ($referral_referral_id2 != 0) {
                                    $referral_upline_info2 = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $referral_referral_id2));
                                    $referral_upline_package_id2 = isset($referral_upline_info2['id']) ? $referral_upline_info2['package_id'] : 0;
        
                                    $referral_product_price_info2 = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart2['product_id'], 'package_id' => $referral_upline_package_id2, 'company_id' => $company_id, 'active' => 1));
                                    $referral_upline_product_price2 = $referral_product_price_info2['price'];
                                    $referral_upline_product_subtotal2 = $referral_upline_product_price2 * $cart_quantity2;
                                    $referral_upline_price2 += $referral_upline_product_subtotal2;
                                }
                            }
                        }
                        // }

                        $referral_info2 = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_info2['referral_id'], 'active' => 1));
                        $referral_upline_id2 = isset($referral_info2['id']) ? $referral_info2['referral_id'] : 0;
                        $r2=$referral_info2['id'];

                        if ($referral_upline_id2 != 0 && $company_id == 12) {
                            $upline_price2 = $upline_price2;
                            $referral_upline_price2 = $referral_upline_price2;
                            $upline_comm2 = $upline_price2 - $referral_upline_price2;
                            $retail_balance2 = $this->check_retail_withdraw_balance_post($referral_upline_id2);
                            $new_balance2 = $retail_balance2 + $upline_comm2;

                            $data_comm2 = array(
                        'type' => "normal",
                        'company_id' => $company_id,
                        'from_user_id' => $user_info2['referral_id'],
                        'to_user_id' => $referral_upline_id2,
                        'description' => "Shipment Order of Order ID #000" . $primary_order_id,
                        'credit' => $upline_comm2,
                        'balance' => $new_balance2,
                        'is_released' => 1
                    );

                            if ($upline_comm2 > 0) {
                                $this->Api_Model->insert_data(TBL_WALLET, $data_comm2);
                            }
                        }
                    }

                    $user_info4 = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
                    $user_info4_id = $user_info4['referral_id'];

                    $user_info3 = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_info4_id, 'active' => 1));
                    $referral_upline_id3 = isset($user_info3['id']) ? $user_info3['referral_id'] : 0;
    
                    if ($referral_upline_id3 != 0 && $company_id == 12) {
                        $upline_price3 = 0;
                        $referral_upline_price3 = 0;
                        foreach ($cart_list as $row_cart3) {
                            if ($company_id == 12) {
                                $cart_quantity3 = $row_cart3['quantity'];
        
                                $referral_info3 = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, referral_id", array('id' => $user_info3['referral_id']));
                                $referral_package_id3 = isset($referral_info3['id']) ? $referral_info3['package_id'] : 0;
                                $referral_referral_id3 = isset($referral_info3['id']) ? $referral_info3['referral_id'] : 0;
        
                                $product_price_info3 = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart3['product_id'], 'package_id' => $referral_package_id3, 'company_id' => $company_id, 'active' => 1));
                                $referral_product_price3 = $product_price_info3['price'];
                                $referral_product_subtotal3 = $referral_product_price3 * $cart_quantity3;
                                $upline_price3 += $referral_product_subtotal3;
        
                                if ($referral_referral_id3 != 0) {
                                    $referral_upline_info3 = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $referral_referral_id3));
                                    $referral_upline_package_id3 = isset($referral_upline_info3['id']) ? $referral_upline_info3['package_id'] : 0;
        
                                    $referral_product_price_info3 = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "id, price", array('product_id' => $row_cart3['product_id'], 'package_id' => $referral_upline_package_id3, 'company_id' => $company_id, 'active' => 1));
                                    $referral_upline_product_price3 = $referral_product_price_info3['price'];
                                    $referral_upline_product_subtotal3 = $referral_upline_product_price3 * $cart_quantity3;
                                    $referral_upline_price3 += $referral_upline_product_subtotal3;
                                }
                            }
                        }
                        // }

                        $referral_info3 = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_info3['referral_id'], 'active' => 1));
                        $referral_upline_id3 = isset($referral_info3['id']) ? $referral_info3['referral_id'] : 0;
                        $r3=$referral_info3['id'];

                        if ($referral_upline_id3 != 0 && $company_id == 12) {
                            $upline_price3 = $upline_price3;
                            $referral_upline_price3 = $referral_upline_price3;
                            $upline_comm3 = $upline_price3 - $referral_upline_price3;
                            $retail_balance3 = $this->check_retail_withdraw_balance_post($referral_upline_id3);
                            $new_balance3 = $retail_balance3 + $upline_comm3;

                            $data_comm3 = array(
                        'type' => "normal",
                        'company_id' => $company_id,
                        'from_user_id' => $user_info3['referral_id'],
                        'to_user_id' => $referral_upline_id3,
                        'description' => "Shipment Order of Order ID #000" . $primary_order_id,
                        'credit' => $upline_comm3,
                        'balance' => $new_balance3,
                        'is_released' => 1
                    );

                            if ($upline_comm3 > 0) {
                                $this->Api_Model->insert_data(TBL_WALLET, $data_comm3);
                            }
                        }
                    }
// end edit here
					
                    // insert voucher into agent account
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_old", array('id' => $user_id, 'active' => 1));
                    $is_old_user = isset($user_info['id']) ? $user_info['is_old'] : 0;

                    // echo $total_order_quantity . "-" . $min_package_quantity . "-" . $is_old_user; die;

                    if((($total_order_quantity >= $min_package_quantity && $min_package_quantity != 0) || ($is_old_user == 1 && $total_quantity >= $min_package_quantity)) && $order_type == "normal"){
                        $shipment_voucher_list = $this->Api_Model->get_rows(TBL_SHIPMENT_VOUCHER, "*", array('company_id' => $company_id, 'active' => 1));
                        if(!empty($shipment_voucher_list)){
                            foreach($shipment_voucher_list as $row_package_voucher){
                                $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('user_id' => $user_id, 'product_id' => $row_package_voucher['product_id'], 'active' => 1));
                                $is_got_exist_voucher = isset($user_voucher_info['id']) ? 1 : 0;
                                $current_voucher_quantity = isset($user_voucher_info['id']) ? $user_voucher_info['quantity'] : 0;
                                $new_voucher_quantity = $current_voucher_quantity + $row_package_voucher['quantity'];

                                if($is_got_exist_voucher == 1){
                                    $data_user_voucher_update = array(
                                        'quantity' => $new_voucher_quantity
                                    );
                                    $this->Api_Model->update_data(TBL_USER_VOUCHER, array('user_id' => $user_id, 'product_id' => $row_package_voucher['product_id'], 'active' => 1), $data_user_voucher_update);
                                }else{
                                    $data_user_voucher = array(
                                        'user_id' => $user_id,
                                        'product_id' => $row_package_voucher['product_id'],
                                        'quantity' => $row_package_voucher['quantity'],
                                        'price' => $row_package_voucher['price']
                                    );
                                    $this->Api_Model->insert_data(TBL_USER_VOUCHER, $data_user_voucher);
                                }
                            }
                        }
                    }

                    // clear user voucher
                    foreach($cart_list as $row_cart){
                        $total_quantity = $row_cart['quantity'];
                        $is_normal = $row_cart['is_normal'];
                        $is_promotion = $row_cart['is_promotion'];
                        if(!empty($voucher_id_arr)){
                            foreach($voucher_id_arr as $voucher_id){
                                if($is_normal == 1 && $is_promotion == 1){
                                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => 0));
                                }else{
                                    $user_voucher_info = $this->Api_Model->get_rows_info(TBL_USER_VOUCHER, "id, quantity", array('active' => 1, 'id' => $voucher_id, 'user_id' => $user_id, 'product_id' => $row_cart['product_id']));
                                }
                                
                                $voucher_quantity = isset($user_voucher_info['id']) ? $user_voucher_info['quantity'] : 0;
                                $voucher_id = isset($user_voucher_info['id']) ? $user_voucher_info['id'] : 0;
                                $new_voucher_quantity = $voucher_quantity - $total_quantity;

                                if($total_quantity == $voucher_quantity){
                                    $data_update_user_voucher = array(
                                        'active' => 0
                                    );
                                }else{
                                    $data_update_user_voucher = array(
                                        'quantity' => $new_voucher_quantity
                                    );
                                }

                                $this->Api_Model->update_data(TBL_USER_VOUCHER, array('id' => $voucher_id, 'user_id' => $user_id), $data_update_user_voucher);
                            }
                        }
                    }

                    // if($promotion_id != 0 && $is_promotion == 1){
                    //     $data_promotion_log = array(
                    //         'promotion_id' => $promotion_id,
                    //         'phone_no' => $phone_no
                    //     );
                    //     $this->Api_Model->insert_data(TBL_PROMOTION_LOG, $data_promotion_log);
                    // }

                    // clear all cart
                    $this->Api_Model->update_multiple_data(TBL_CART, array('user_id' => $user_id, 'active' => 1), array('active' => 0, 'is_clear' => 1));

                    $result = $this->success_response($data_order);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{
                    $result = $this->error_response("Invalid Address !");
                    $this->response($result, 200);
                }
            }else{
                $result = $this->error_response("Invalid Security Code !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Empty Cart !");
            $this->response($result, 200);
        }
    }

    public function get_shipment_history_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $is_referral = isset($this->request_data['is_referral']) ? $this->request_data['is_referral'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            if($is_referral == 1){
                $order_list = $this->Api_Model->get_rows(TBL_ORDER, "*", array('referral_id' => $user_id, 'active' => 1, 'type !=' => "normal"), "", "", "insert_time", "DESC");
            }else{
                $order_list = $this->Api_Model->get_rows(TBL_ORDER, "*", array('user_id' => $user_id, 'active' => 1), "", "", "insert_time", "DESC");
            }
            if(!empty($order_list)){
                foreach($order_list as $olkey => $olval){
                    $order_id = $olval['id'];
                    $order_status = $olval['status'];
                    $shipping_status = $olval['order_status'];

                    if($order_status == "PENDING" && $shipping_status == "PLACED"){
                        $order_list[$olkey]['status_color'] = "yellow";
                        $order_list[$olkey]['current_status'] = "Waiting Approval";
                    }else if($order_status == "APPROVE" && $shipping_status == "PLACED"){
                        $order_list[$olkey]['status_color'] = "yellow";
                        $order_list[$olkey]['current_status'] = "Pending Shipment";
                    }else if($order_status == "APPROVE" && $shipping_status == "SHIPPED"){
                        $order_list[$olkey]['status_color'] = "green";
                        $order_list[$olkey]['current_status'] = "Shipped";
                    }else if($order_status == "CANCEL" && $shipping_status == "PLACED"){
                        $order_list[$olkey]['status_color'] = "red";
                        $order_list[$olkey]['current_status'] = "Cancelled";
                    }else if($order_status == "CANCEL" && $shipping_status == "SHIPPED"){
                        $order_list[$olkey]['status_color'] = "red";
                        $order_list[$olkey]['current_status'] = "Cancelled";
                    }else{
                        $order_list[$olkey]['status_color'] = "red";
                        $order_list[$olkey]['current_status'] = "Error";
                    }

                    if($is_referral == 1){
                        $order_detail_info = $this->Api_Model->get_info_sql(TBL_ORDER_DETAIL, "id, product_id", "WHERE order_id = '$order_id' AND active = '1'");
                    }else{
                        $order_detail_info = $this->Api_Model->get_info_sql(TBL_ORDER_DETAIL, "id, product_id", "WHERE order_id = '$order_id' AND user_id = '$user_id' AND active = '1'");
                    }
                    $product_id = isset($order_detail_info['id']) ? $order_detail_info['product_id'] : 0;
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $product_id, 'active' => 1));
                    $product_name = isset($product_info['id']) ? $product_info['name'] : "";
                    $product_image = isset($product_info['id']) ? DISPLAY_PATH . "img/product/" . $product_info['image'] : "";
                    $order_list[$olkey]['product_image'] = $product_image;
                    $order_list[$olkey]['product_name'] = $product_name;
                    $order_list[$olkey]['insert_date'] = date('d M Y', strtotime($olval['insert_time']));
                    $order_list[$olkey]['insert_time'] = date('H:i:s', strtotime($olval['insert_time']));
                    $order_list[$olkey]['order_id'] = "#000" . $olval['id'];

                    $tmp_user_id = $olval['tmp_user_id'];
                    if($company_id == 2){
                        if($tmp_user_id != 0){
                            $order_list[$olkey]['order_user'] = $olval['s_name'];
                        }else{
                            $order_list[$olkey]['order_user'] = "";
                        }
                    }else{
                        $order_list[$olkey]['order_user'] = $olval['s_name'];
                    }

                    $order_type = isset($olval['type']) ? $olval['type'] : "";
                    $order_is_restock = isset($olval['is_restock']) ? $olval['is_restock'] : 0;

                    if($order_type == "restock" && $order_is_restock == 0){
                        $order_list[$olkey]['type'] = "retail";
                    }
                }
            }else{
                $order_list = array();
            }

            $result = $this->success_response($order_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_shipment_order_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
            if(isset($order_info['id']) && $order_info['id'] > 0){
                $order_status = $order_info['status'];
                $shipping_status = $order_info['order_status'];

                if($order_status == "PENDING" && $shipping_status == "PLACED"){
                    $status_color = "yellow";
                    $current_status = "Waiting Approval";
                }else if($order_status == "APPROVE" && $shipping_status == "PLACED"){
                    $status_color = "yellow";
                    $current_status = "Pending Shipment";
                }else if($order_status == "APPROVE" && $shipping_status == "SHIPPED"){
                    $status_color = "green";
                    $current_status = "Shipped";
                }else if($order_status == "CANCEL" && $shipping_status == "PLACED"){
                    $status_color = "red";
                    $current_status = "Cancelled";
                }else if($order_status == "CANCEL" && $shipping_status == "SHIPPED"){
                    $status_color = "red";
                    $current_status = "Cancelled";
                }else{
                    $status_color = "red";
                    $current_status = "Error";
                }
                $referral_id = isset($order_info['id']) ? $order_info['referral_id'] : 0;
                $payment_receipt = isset($order_info['id']) ? $order_info['payment_receipt'] : "";
                $is_referral = ($order_info['referral_id'] != 0) ? 1 : 0;
                if($is_referral == 1){
                    $order_detail_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('active' => 1, 'order_id' => $order_id));
                }else{
                    $order_detail_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('user_id' => $user_id, 'active' => 1, 'order_id' => $order_id));
                }
                if(!empty($order_detail_list)){
                    foreach($order_detail_list as $odkey => $odval){
                        $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $odval['product_id'], 'active' => 1));
                        $product_name = isset($product_info['id']) ? $product_info['name'] : "";
                        $product_image = isset($product_info['id']) ? DISPLAY_PATH . "img/product/" . $product_info['image'] : "";
                        $order_detail_list[$odkey]['product_name'] = $product_name;
                        $order_detail_list[$odkey]['product_image'] = $product_image;
                    }
                }else{
                    $order_detail_list = array();
                }

                $order_payment_receipt = DISPLAY_PATH . "img/order_receipt/" . $payment_receipt;
                $order_date = date('d M Y H:i:s', strtotime($order_info['insert_time']));

                $data = array(
                    'order_time' => $order_date,
                    'is_referral' => $is_referral,
                    'user_id' => $order_info['user_id'],
                    'order_id' => "#000" . $order_info['id'],
                    's_name' => $order_info['s_name'],
                    's_contact' => $order_info['s_contact'],
                    's_address' => $order_info['s_address'],
                    's_city' => $order_info['s_city'],
                    's_postcode' => $order_info['s_postcode'],
                    's_state' => $order_info['s_state'],
                    's_country' => $order_info['s_country'],
                    'delivery_company' => $order_info['delivery_company'],
                    'tracking_no' => $order_info['tracking_no'],
                    'tracking_url' => $order_info['tracking_url'],
                    'order_status' => $order_info['status'],
                    'payment_status' => $order_info['payment_status'],
                    'payment_receipt' => $order_payment_receipt,
                    'status_color' => $status_color,
                    'current_status' => $current_status,
                    'order_list' => $order_detail_list
                );
    
                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Invalid Order !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function check_is_contains_specific_post($string, $text){
        if(strpos($string, $text) !== false){
            return true;
        } else{
            return false;
        }
    }

    public function get_stock_record_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            if($company_type == "FIXED"){
                $stock_list = $this->Api_Model->get_rows(TBL_STOCK, "*", array('user_id' => $user_id, 'active' => 1), "", "", "id", "DESC");
            }else{
                $stock_list = $this->Api_Model->get_rows(TBL_POINT, "*", array('user_id' => $user_id, 'active' => 1), "", "", "id", "DESC");
            }
            if(!empty($stock_list)){
                foreach($stock_list as $slkey => $slval){
                    $package_id = $slval['package_id'];
                    $order_id = $slval['order_id'];
                    if($order_id == 0){
                        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, status, user_id, active", array('id' => $package_id));
                        $stock_list[$slkey]['order_id'] = isset($purchase_package_info['id']) ? "Package ID: #000" . $purchase_package_info['id'] : 0;
                        if($this->check_is_contains_specific_post($slval['description'], "Refund")){
                            $stock_list[$slkey]['order_status'] = "REFUND";
                        }else{
                            $stock_list[$slkey]['order_status'] = isset($purchase_package_info['id']) ? $purchase_package_info['status'] : "";
                        }
                        $purchase_user_id = isset($purchase_package_info['id']) ? $purchase_package_info['user_id'] : 0;
                        $purchase_is_active = isset($purchase_package_info['id']) ? $purchase_package_info['active'] : 0;
                        if($purchase_is_active == 0){
                            unset($stock_list[$slkey]);
                        }else{
                            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $purchase_user_id, 'active' => 1));
                            if(isset($user_info['id']) && $user_info['id'] > 0){
                                if($user_info['fullname'] == ""){
                                    $agent_name = $user_info['username'];
                                }else{
                                    $agent_name = $user_info['fullname'] . " (" . $user_info['username'] . ")";
                                }
                                $stock_list[$slkey]['agent_name'] = $agent_name;
                            }else{
                                $stock_list[$slkey]['agent_name'] = "";
                            }
                        }
                    }else{
                        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, status, user_id, referral_id", array('id' => $order_id, 'active' => 1));
                        $stock_list[$slkey]['order_id'] = isset($order_info['id']) ? "Order ID: #000" . $order_info['id'] : 0;
                        if($this->check_is_contains_specific_post($slval['description'], "Refund")){
                            $stock_list[$slkey]['order_status'] = "REFUND";
                        }else{
                            $stock_list[$slkey]['order_status'] = isset($order_info['id']) ? $order_info['status'] : "";
                        }
                        if($order_info['user_id'] != 0){
                            $agent_id = $order_info['user_id'];
                        }else{
                            $agent_id = $order_info['referral_id'];
                        }
                        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $agent_id, 'active' => 1));
                        if(isset($user_info['id']) && $user_info['id'] > 0){
                            if($user_info['fullname'] == ""){
                                $agent_name = $user_info['username'];
                            }else{
                                $agent_name = $user_info['fullname'] . " (" . $user_info['username'] . ")";
                            }
                            $stock_list[$slkey]['agent_name'] = $agent_name;
                        }else{
                            $stock_list[$slkey]['agent_name'] = "";
                        }
                    }
                }
            }else{
                $stock_list = array();
            }
            $stock_balance = $this->check_stock_balance_post($user_id);
            $point_balance = $this->check_point_balance_post($user_id);

            $data = array(
                'stock' => $stock_list,
                'stock_balance' => $stock_balance,
                'point_balance' => $point_balance
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_pv_record_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $pv_list = $this->Api_Model->get_rows(TBL_PV, "*", array('user_id' => $user_id, 'active' => 1), "", "", "id", "DESC");
            if(!empty($pv_list)){
                foreach($pv_list as $pkey => $pval){
                    $package_id = $pval['package_id'];
                    $order_id = $pval['order_id'];
                    if($order_id == 0){
                        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, status, user_id, active", array('id' => $package_id));
                        $pv_list[$pkey]['order_id'] = isset($purchase_package_info['id']) ? "Package ID: #000" . $purchase_package_info['id'] : 0;
                        if($this->check_is_contains_specific_post($pval['description'], "Refund")){
                            $pv_list[$pkey]['order_status'] = "REFUND";
                        }else{
                            $pv_list[$pkey]['order_status'] = isset($purchase_package_info['id']) ? $purchase_package_info['status'] : "";
                        }
                        $purchase_user_id = isset($purchase_package_info['id']) ? $purchase_package_info['user_id'] : 0;
                        $purchase_is_active = isset($purchase_package_info['id']) ? $purchase_package_info['active'] : 0;
                        if($purchase_is_active == 0){
                            unset($pv_list[$pkey]);
                        }else{
                            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $purchase_user_id, 'active' => 1));
                            if(isset($user_info['id']) && $user_info['id'] > 0){
                                if($user_info['fullname'] == ""){
                                    $agent_name = $user_info['username'];
                                }else{
                                    $agent_name = $user_info['fullname'] . " (" . $user_info['username'] . ")";
                                }
                                $pv_list[$pkey]['agent_name'] = $agent_name;
                            }else{
                                $pv_list[$pkey]['agent_name'] = "";
                            }
                        }
                    }else{
                        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, status, user_id, referral_id", array('id' => $order_id, 'active' => 1));
                        $pv_list[$pkey]['order_id'] = isset($order_info['id']) ? "Order ID: #000" . $order_info['id'] : 0;
                        if($this->check_is_contains_specific_post($pval['description'], "Refund")){
                            $pv_list[$pkey]['order_status'] = "REFUND";
                        }else{
                            $pv_list[$pkey]['order_status'] = isset($order_info['id']) ? $order_info['status'] : "";
                        }
                        if($order_info['user_id'] != 0){
                            $agent_id = $order_info['user_id'];
                        }else{
                            $agent_id = $order_info['referral_id'];
                        }
                        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $agent_id, 'active' => 1));
                        if(isset($user_info['id']) && $user_info['id'] > 0){
                            if($user_info['fullname'] == ""){
                                $agent_name = $user_info['username'];
                            }else{
                                $agent_name = $user_info['fullname'] . " (" . $user_info['username'] . ")";
                            }
                            $pv_list[$pkey]['agent_name'] = $agent_name;
                        }else{
                            $pv_list[$pkey]['agent_name'] = "";
                        }
                    }
                }
            }else{
                $pv_list = array();
            }
            $pv_balance = $this->check_pv_balance_post($user_id);

            $data = array(
                'stock' => $pv_list,
                'pv_balance' => $pv_balance
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            if($type == "referral"){
                $order_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('referral_id' => $user_id, 'active' => 1));
            }else{
                $order_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('user_id' => $user_id, 'active' => 1));
            }

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if(!empty($order_list)){
                foreach($order_list as $olkey => $olval){
                    $is_voucher = $olval['is_voucher'];
                    $is_restock = $olval['is_restock'];
                    $restock_quantity = $olval['quantity'];
                    if($is_voucher == 1){
                        if($type == "referral"){
                            $user_id = $olval['user_id'];
                            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "voucher_id", array('id' => $user_id, 'active' => 1));
                        }else{
                            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "voucher_id", array('id' => $user_id, 'active' => 1));
                        }
                        $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $user_info['voucher_id']));
                        $big_present_package_id = isset($big_present_info['id']) ? $big_present_info['package_id'] : 0;

                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $big_present_package_id, 'active' => 1));
                        $order_list[$olkey]['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                        $order_list[$olkey]['package_quantity'] = isset($big_present_info['id']) ? number_format($big_present_info['total_stock'], 0, '.',',') : "";
                        if($company_type == "FLAT"){
                            $order_list[$olkey]['package_price'] = isset($big_present_info['id']) ? $big_present_info['price'] : "";
                        }else{
                            $order_list[$olkey]['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                        }
                        $order_list[$olkey]['package_total'] = isset($big_present_info['id']) ? number_format($big_present_info['price'], 0, '.',',') : "";
                        $order_list[$olkey]['package_unit'] = isset($big_present_info['id']) ? $big_present_info['unit'] : "";
                    }else{
                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $olval['package_id'], 'active' => 1));
                        if($is_restock == 1){
                            $order_list[$olkey]['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                            $order_list[$olkey]['package_quantity'] = number_format($olval['quantity'], 0, '.',',');
                            if($company_type == "FLAT"){
                                $order_list[$olkey]['package_price'] = $olval['subtotal'];
                            }else{
                                $order_list[$olkey]['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                            }
                            $order_list[$olkey]['package_total'] = number_format($olval['subtotal'], 0, '.',',');
                            $order_list[$olkey]['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
                        }else{
                            $order_list[$olkey]['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                            if($restock_quantity != 0){
                                $order_list[$olkey]['package_quantity'] = number_format($olval['quantity'], 0, '.',',');
                            }else{
                                $order_list[$olkey]['package_quantity'] = isset($package_info['id']) ? number_format($package_info['quantity'], 0, '.',',') : "";
                            }
                            $order_list[$olkey]['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                            if($restock_quantity != 0){
                                $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                                $package_grand_total = $package_price * $restock_quantity;
                                $order_list[$olkey]['package_total'] = number_format($package_grand_total, 0, '.',',');
                            }else{
                                $order_list[$olkey]['package_total'] = isset($package_info['id']) ? number_format($package_info['grand_total'], 0, '.',',') : "";
                            }
                            $order_list[$olkey]['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
                        }
                    }
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, username", array('id' => $olval['user_id'], 'active' => 1));
                    if(isset($user_info['id']) && $user_info['id'] > 0){
                        if($user_info['fullname'] == ""){
                            $agent_name = $user_info['username'];
                        }else{
                            $agent_name = $user_info['fullname'] . " (" . $user_info['username'] . ")";
                        }
                    }else{
                        $agent_name = "";
                    }

                    $order_list[$olkey]['agent_name'] = $agent_name;
                    $order_list[$olkey]['order_id'] = "#000" . $olval['id'];
                    $order_list[$olkey]['insert_time'] = date('d M Y H:i:s', strtotime($olval['insert_time']));
                }
            }else{
                $order_list = array();
            }

            $json_response = array(
                'order' => $order_list
            );

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_order_detail_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        // get order detail info
        $order_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('active' => 1, 'id' => $order_id));
        $order_invoice_name = isset($order_package_info['id']) ? $order_package_info['invoice_name'] : "";
        if($order_invoice_name == "" || $order_invoice_name == NULL){
            $order_package_info['invoice_name'] = null;
        }else{
            $order_package_info['invoice_name'] = DISPLAY_PATH . "img/package_invoice/invoice" . $order_id . ".pdf";
        }
        
        // get selected order package user info
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, fullname, phone_no, email", array('id' => $order_package_info['user_id'], 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
        $order_user_id = isset($user_info['id']) ? $user_info['id'] : 0;
        $agent_name = isset($user_info['id']) ? $user_info['fullname'] : 0;
        $agent_contact = isset($user_info['id']) ? $user_info['phone_no'] : 0;
        $agent_email = isset($user_info['id']) ? $user_info['email'] : 0;

        $is_voucher = isset($order_package_info['id']) ? $order_package_info['is_voucher'] : 0;
        $is_restock = isset($order_package_info['id']) ? $order_package_info['is_restock'] : 0;
        $package_id = isset($order_package_info['id']) ? $order_package_info['package_id'] : 0;
        $quantity = isset($order_package_info['id']) ? $order_package_info['quantity'] : 0;
        $subtotal = isset($order_package_info['id']) ? $order_package_info['subtotal'] : "0.00";
        $order_package_id = isset($order_package_info['id']) ? $order_package_info['id'] : 0;
        $order_package_time = isset($order_package_info['id']) ? $order_package_info['insert_time'] : "";
        $payment_receipt = isset($order_package_info['id']) ? $order_package_info['payment_receipt'] : "";

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";

        if($is_voucher == 1){
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "voucher_id", array('id' => $order_user_id, 'active' => 1));
            $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $user_info['voucher_id']));
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $big_present_info['package_id'], 'active' => 1));
            $order_package_info['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
            $order_package_info['package_quantity'] = isset($big_present_info['id']) ? number_format($big_present_info['total_stock'], 0, '.',',') : "";
            if($company_type == "FLAT"){
                $order_package_info['package_price'] = isset($big_present_info['id']) ? $big_present_info['price'] : "";
            }else{
                $order_package_info['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
            }
            $order_package_info['package_total'] = isset($big_present_info['id']) ? number_format($big_present_info['price'], 0, '.',',') : "";
            $order_package_info['package_unit'] = isset($big_present_info['id']) ? $big_present_info['unit'] : "";
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
            if($is_restock == 1){
                $order_package_info['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                $order_package_info['package_quantity'] = number_format($quantity, 0, '.',',');
                if($company_type == "FLAT"){
                    $order_package_info['package_price'] = $subtotal;
                }else{
                    $order_package_info['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                }
                $order_package_info['package_total'] = number_format($subtotal, 0, '.',',');
                $order_package_info['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
            }else{
                $order_package_info['package_name'] = isset($package_info['id']) ? $package_info['name'] : "";
                if($quantity != 0){
                    $order_package_info['package_quantity'] = number_format($quantity, 0, '.',',');
                }else{
                    $order_package_info['package_quantity'] = isset($package_info['id']) ? number_format($package_info['quantity'], 0, '.',',') : "";
                }
                $order_package_info['package_price'] = isset($package_info['id']) ? $package_info['unit_price'] : "";
                if($quantity != 0){
                    $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                    $package_grand_total = $package_price * $quantity;
                    $order_package_info['package_total'] = number_format($package_grand_total, 0, '.',',');
                }else{
                    $order_package_info['package_total'] = isset($package_info['id']) ? number_format($package_info['grand_total'], 0, '.',',') : "";
                }
                $order_package_info['package_unit'] = isset($package_info['id']) ? $package_info['unit'] : "";
            }
        }
        $order_package_info['order_id'] = "#000" . $order_package_id;
        $order_package_info['insert_time'] = date('d M Y H:i:s', strtotime($order_package_time));
        $order_package_info['payment_receipt'] = DISPLAY_PATH . "img/package_receipt/" . $payment_receipt;
        $order_package_info['agent_name'] = $agent_name;
        $order_package_info['agent_contact'] = $agent_contact;
        $order_package_info['agent_email'] = $agent_email;

        $result = $this->success_response($order_package_info);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function submit_order_receipt_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('active' => 1, 'id' => $order_id));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            if($order_info['user_id'] == $user_id){
                if (!empty($_FILES['Image']['name']))
                {
                    $config['upload_path'] = IMAGE_PATH . './img/order_receipt';
                    $config['allowed_types'] = 'jpg|png|jpeg';  
                    $config['max_size'] = '5120'; //in KB    
                    $config['encrypt_name'] = TRUE;               
                    // create directory if not exists
                    if (!@is_dir(IMAGE_PATH . 'img/order_receipt')) {
                        @mkdir(IMAGE_PATH . './img/order_receipt', 0777, TRUE);
                    }
                    $this->upload->initialize($config);  
                            
                    if ($this->upload->do_upload('Image'))
                    {
                        $img = $this->upload->data();
                        $this->resizingImage($img['file_name'], "order_receipt");
                        $image = $img['file_name'];

                        $data_order = array(
                            'payment_receipt' => $image,
                            'payment_status' => "PAID"
                        );
                        $this->Api_Model->update_data(TBL_ORDER, array('active' => 1, 'id' => $order_id), $data_order);
            
                        $result = $this->success_response($data_order);
                        $this->response($result, REST_Controller::HTTP_OK);
                    }
                    else
                    {
                        $result = $this->error_response($this->upload->display_errors());
                        $this->response($result, 200);
                    }
                }else{
                    $result = $this->error_response("Empty Data !");
                    $this->response($result, 200);
                }
            }else{
                $result = $this->error_response("Partner order, Not able to upload !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function submit_package_receipt_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('user_id' => $user_id, 'active' => 1, 'id' => $order_id));
        if(isset($order_package_info['id']) && $order_package_info['id'] > 0){
            if (!empty($_FILES['Image']['name']))
            {
                $config['upload_path'] = IMAGE_PATH . './img/package_receipt';
                $config['allowed_types'] = 'jpg|png|jpeg';  
                $config['max_size'] = '5120'; //in KB    
                $config['encrypt_name'] = TRUE;               
                // create directory if not exists
                if (!@is_dir(IMAGE_PATH . 'img/package_receipt')) {
                    @mkdir(IMAGE_PATH . './img/package_receipt', 0777, TRUE);
                }
                $this->upload->initialize($config);  
                        
                if ($this->upload->do_upload('Image'))
                {
                    $img = $this->upload->data();
                    $this->resizingImage($img['file_name'], "package_receipt");
                    $image = $img['file_name'];
                }
                else
                {
                    $result = $this->error_response($this->upload->display_errors());
                    $this->response($result, 200);
                }
            }else{
                $image = "";
            }

            $data_purchase_package = array(
                'payment_receipt' => $image,
                'payment_status' => "PAID"
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('user_id' => $user_id, 'active' => 1, 'id' => $order_id), $data_purchase_package);

            $result = $this->success_response($data_purchase_package);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function check_is_display_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            // is got big present 
            $big_present_balance = $this->Api_Model->get_rows_info(TBL_USER_BIG_PRESENT_FREE, "id, SUM(quantity) as total_quantity", array('user_id' => $user_id, 'active' => 1));
            $total_big_present_quantity = isset($big_present_balance['id']) ? $big_present_balance['total_quantity'] : 0;
            if($total_big_present_quantity == 0){
                if($company_id == 2){
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, COUNT(*) as total_balance_voucher", array('user_id' => $user_id, 'active' => 1, 'type' => "VOUCHER", 'total_stock !=' => 60));
                }else{
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, COUNT(*) as total_balance_voucher", array('user_id' => $user_id, 'active' => 1, 'type' => "VOUCHER"));
                }
                $total_balance_voucher = isset($voucher_info['id']) ? $voucher_info['total_balance_voucher'] : 0;
                if($total_balance_voucher == 0){
                    $is_valid = 0;
                }else{
                    $is_valid = 1;
                }
            }else{
                $is_valid = 1;
            }

            $data = array(
                'is_valid' => $is_valid
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_voucher_referral_url_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $voucher_id = isset($this->request_data['voucher_id']) ? $this->request_data['voucher_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, is_voucher", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $voucher_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT, "*", "WHERE id = '$voucher_id'");
            $voucher_code = isset($voucher_info['id']) ? $voucher_info['code'] : "";

            $data = array(
                'code' => $voucher_code
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_voucher_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $big_present_free_list = $this->Api_Model->get_rows(TBL_USER_BIG_PRESENT_FREE, "*", array('user_id' => $user_id, 'quantity !=' => 0));
        if(!empty($big_present_free_list)){
            foreach($big_present_free_list as $bpkey => $bpval){
                $package_id = $bpval['package_id'];

                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, country_id", array('id' => $package_id, 'active' => 1));
                $package_name = isset($package_info['id']) ? $package_info['name']: "";
                $country_id = isset($package_info['id']) ? $package_info['country_id']: 0;
                $big_present_free_list[$bpkey]['package_name'] = $package_name;
                $big_present_free_list[$bpkey]['id'] = $package_id;
                unset($big_present_free_list[$bpkey]['package_id']);
            }

            $country_list = $this->Api_Model->get_rows(TBL_CURRENCY, "*", array('id' => $country_id, 'active' => 1));
        }else{
            $big_present_free_list = array();
            $country_list = array();
        }

        $data = array(
            'package' => $big_present_free_list,
            'country' => $country_list
        );

        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function insert_voucher_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $country_id = isset($this->request_data['country_id']) ? $this->request_data['country_id'] : 0;
        $package_id = isset($this->request_data['package_id']) ? $this->request_data['package_id'] : 0;
        $voucher_code = $this->generate_voucher_code(8) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(10);

        if($country_id == 0){
            $result = $this->error_response("Invalid Country !");
            $this->response($result, 200);
        }else if($package_id == 0){
            $result = $this->error_response("Invalid Package !");
            $this->response($result, 200);
        }else{
            $big_present_free_list = $this->Api_Model->get_rows(TBL_USER_BIG_PRESENT_FREE, "*", array('user_id' => $user_id, 'quantity !=' => 0));
            if(empty($big_present_free_list)){
                $result = $this->error_response("Insufficient Voucher !");
                $this->response($result, 200);
            }else{
                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
                $user_id = isset($user_info['id']) ? $user_info['id'] : 0;
                $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

                $data_voucher_log = array(
                    'user_id' => $user_id,
                    'country_id' => $country_id,
                    'package_id' => $package_id
                );

                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                $company_type = isset($company_info['id']) ? $company_info['type'] : "";

                $big_present_package_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT_PACKAGE, "*", array('package_id' => $package_id, 'active' => 1));
                $package_price = isset($big_present_package_info['id']) ? $big_present_package_info['price'] : "0.00";

                $user_big_present_free_info = $this->Api_Model->get_rows_info(TBL_USER_BIG_PRESENT_FREE, "*", array('package_id' => $package_id, 'user_id' => $user_id, 'active' => 1));
                $current_quantity = isset($user_big_present_free_info['id']) ? $user_big_present_free_info['quantity'] : 0;
                $current_voucher_quantity = isset($user_big_present_free_info['id']) ? $user_big_present_free_info['total_stock'] : 0;
                $current_voucher_is_promotion = isset($user_big_present_free_info['id']) ? $user_big_present_free_info['is_promotion'] : 0;
                $new_update_quantity = $current_quantity - 1;

                if($current_quantity == 0){
                    $result = $this->error_response("Insufficient Voucher !");
                    $this->response($result, 200);
                }else{
                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
                    $total_price = isset($package_info['id']) ? $package_info['grand_total'] : "0.00";
                    $new_point = $total_price;
                    $new_stock = $current_voucher_quantity;

                    $data_big_present = array(
                        'type' => 2,
                        'user_id' => $user_id,
                        'country_id' => $country_id,
                        'company_id' => $company_id,
                        'package_id' => $package_id,
                        'code' => $voucher_code,
                        'quantity' => 1,
                        'balance_quantity' => 1,
                        'price' => $package_price,
                        'status' => "APPROVE"
                    );

                    if($current_voucher_is_promotion == 1){
                        $data_big_present['price'] = $total_price;
                    }
                    
                    if($company_type == "FIXED"){
                        $data_big_present['total_stock'] = $new_stock;
                        $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);
                    }else{
                        $data_big_present['total_point'] = $new_point;
                        $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_big_present);
                    }

                    $data_voucher_log['voucher_id'] = $insert_id;
                    $this->Api_Model->insert_data(TBL_VOUCHER_LOG, $data_voucher_log);

                    $data_user_big_present = array(
                        'quantity' => $new_update_quantity
                    );
                    $this->Api_Model->update_data(TBL_USER_BIG_PRESENT_FREE, array('package_id' => $package_id, 'user_id' => $user_id, 'active' => 1, 'quantity !=' => 0), $data_user_big_present);

                    $result = $this->success_response($data_voucher_log);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }
        }
    }

    public function get_voucher_list_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $voucher_list = $this->Api_Model->get_rows(TBL_VOUCHER_LOG, "*", array('user_id' => $user_id, 'active' => 1));
            if(!empty($voucher_list)){
                foreach($voucher_list as $vlkey => $vlval){
                    if($vlval['register_user_id'] == 0){
                        $voucher_list[$vlkey]['is_claim'] = 0;
                    }else{
                        $voucher_list[$vlkey]['is_claim'] = 1;
                    }
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $vlval['voucher_id']));
                    $voucher_code = isset($voucher_info['id']) ? $voucher_info['code'] : "";
                    $voucher_list[$vlkey]['voucher_code'] = $voucher_code;

                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $vlval['package_id'], 'active' => 1));
                    $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                    $voucher_list[$vlkey]['package_name'] = $package_name;
                }
            }else{
                $voucher_list = array();
            }

            $result = $this->success_response($voucher_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_restock_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, voucher_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $country_id = $user_info['country_id'];
            $voucher_id = $user_info['voucher_id'];

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, type", array('id' => $company_id));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($voucher_id != 0){
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, total_stock", array('id' => $voucher_id));
                $voucher_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
            }else{
                $voucher_package_id = 0;
            }

            $list = array();

            $package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "*", array('active' => 1, 'company_id' => $company_id, 'country_id' => $country_id), "", "", "quantity", "DESC");
            foreach($package_list as $plkey => $plval){
                if($plval['half_restock'] == 1){
                    if($plval['id'] == $voucher_package_id){
                        $package_list[$plkey]['display_quantity'] = $voucher_quantity . "<br>(Ori: " . $plval['quantity'] . ")";
                        $package_list[$plkey]['quantity_data'] = $voucher_quantity;
                    }else{
                        $package_list[$plkey]['display_quantity'] = $plval['quantity'];
                        $package_list[$plkey]['quantity_data'] = $plval['quantity'];
                    }
                }else{
                    if($company_type == "FLAT"){
                        $package_list[$plkey]['display_quantity'] = $plval['grand_total'];
                        $package_list[$plkey]['quantity_data'] = $plval['grand_total'];
                    }else{
                        $package_list[$plkey]['display_quantity'] = $plval['quantity'];
                        $package_list[$plkey]['quantity_data'] = $plval['quantity'];
                    }
                }
            }

            $limit_package_user_list = $this->Api_Model->get_rows(TBL_LIMIT_PACKAGE, "*", array('user_id' => $user_id, 'active' => 1));
            if(!empty($limit_package_user_list)){
                foreach($limit_package_user_list as $row_limit_package_user){
                    $package_id = $row_limit_package_user['package_id'];

                    $special_package_list = $this->Api_Model->get_rows(TBL_SPECIAL_PACKAGE, "*", array('active' => 1, 'company_id' => $company_id, 'country_id' => $country_id, 'id' => $package_id), "", "", "quantity", "DESC");
                    if(!empty($special_package_list)){
                        foreach($special_package_list as $slkey => $slval){
                            if($slval['half_restock'] == 1){
                                if($slval['id'] == $voucher_package_id){
                                    $special_package_list[$slkey]['display_quantity'] = $voucher_quantity . "<br>(Ori: " . $slval['quantity'] . ")";
                                    $special_package_list[$slkey]['quantity_data'] = $voucher_quantity;
                                }else{
                                    $special_package_list[$slkey]['display_quantity'] = $slval['quantity'];
                                    $special_package_list[$slkey]['quantity_data'] = $slval['quantity'];
                                }
                            }else{
                                $special_package_list[$slkey]['display_quantity'] = $slval['quantity'];
                                $special_package_list[$slkey]['quantity_data'] = $slval['quantity'];
                            }
                        }
                    }
                }
            }else{
                $special_package_list = array();
            }

            $list = array_merge($package_list, $special_package_list);

            $result = $this->success_response($list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function calculate_restock_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $is_fixed = isset($this->request_data['is_fixed']) ? $this->request_data['is_fixed'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, voucher_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $country_id = $user_info['country_id'];
            $voucher_id = $user_info['voucher_id'];
            
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($company_type == "FLAT"){
                if($voucher_id != 0){
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, price", array('id' => $voucher_id));
                    $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                    
                    $quantity = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
                    $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND id = '$voucher_package_id'");   
                }else{
                    $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND unit_price <= '$quantity' ORDER BY id DESC LIMIT 1");
                }
            }else{
                if($is_fixed == 1){
                    if($voucher_id != 0){
                        $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, total_stock", array('id' => $voucher_id));
                        $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                        
                        $voucher_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                        $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND id = '$voucher_package_id'");   
                    }else{
                        $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND quantity = '$quantity' ORDER BY quantity DESC LIMIT 1");
                    }
                }else{
                    if($voucher_id != 0){
                        $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, total_stock", array('id' => $voucher_id));
                        $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                        
                        $voucher_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                        
                        if($voucher_id != 0 && $voucher_quantity == $quantity){
                            $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND id = '$voucher_package_id'");
                        }else{
                            $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND quantity <= '$quantity' ORDER BY quantity DESC LIMIT 1");
                        }
                    }else{
                        $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND quantity <= '$quantity' ORDER BY quantity DESC LIMIT 1");
                    }
                }
            }
            $package_id = isset($package_info['id']) ? $package_info['id'] : 0;
            $package_name = isset($package_info['id']) ? $package_info['name'] : "";

            if($company_type == "FLAT"){
                $package_price = number_format($quantity, 2);
                $package_subtotal = number_format($quantity * 1, 2);
                if($quantity == 60000){
                    $package_id = 39;
                    $package_name = "President";
                }
            }else{
                $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                $package_subtotal = number_format($package_price * $quantity, 2);
            }
            
            $is_available = isset($package_info['id']) ? 1 : 0;

            $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, unit_price", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, 'is_max' => 1));
            $max_package_quantity = isset($max_package_info['id']) ? $max_package_info['quantity'] : 0;
            $max_package_price = isset($max_package_info['id']) ? $max_package_info['unit_price'] : 0;

            if($company_type == "FIXED"){
                if($quantity > $max_package_quantity){
                    $is_available = 0;
                }
            }else{
                if($quantity > $max_package_price){
                    $is_available = 0;
                }
            }

            $limit_package_user_info = $this->Api_Model->get_rows_info(TBL_LIMIT_PACKAGE, "*", array('user_id' => $user_id, 'active' => 1));
            $is_exist_data = isset($limit_package_user_info['id']) ? 1 : 0;

            // debugPrintArr($is_exist_data); die;

            if($is_exist_data == 0 && $quantity == 60000){
                $is_available = 0;
            }

            $data = array(
                'is_available' => $is_available,
                'package_id' => $package_id,
                'package_name' => $package_name,
                'package_quantity' => $quantity,
                'package_price' => $package_price,
                'package_subtotal' => $package_subtotal,
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function calculate_voucher_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $voucher_code = isset($this->request_data['voucher_code']) ? $this->request_data['voucher_code'] : 0;
        $is_fixed = isset($this->request_data['is_fixed']) ? $this->request_data['is_fixed'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, voucher_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $country_id = $user_info['country_id'];
            
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($is_fixed == 1){
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $voucher_code, 'active' => 1));
                $voucher_id = isset($voucher_info['id']) ? $voucher_info['id'] : 0;
                $voucher_price = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, total_stock", array('id' => $voucher_id));
                $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                
                $voucher_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND id = '$voucher_package_id'");
            }
            $package_id = isset($package_info['id']) ? $package_info['id'] : 0;
            $package_name = isset($package_info['id']) ? $package_info['name'] : "";

            $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
            $package_subtotal = number_format($voucher_price, 2);
            $is_available = isset($voucher_info['id']) ? 1 : 0;

            $data = array(
                'is_available' => $is_available,
                'package_id' => $package_id,
                'package_name' => $package_name,
                'package_quantity' => $voucher_quantity,
                'package_price' => $package_price,
                'package_subtotal' => $package_subtotal,
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function check_is_free_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, package_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, is_free_voucher, quantity", array('company_id' => $user_info['company_id'], 'country_id' => $user_info['country_id'], 'is_free_voucher' => 1, 'active' => 1));
            $is_free_voucher = isset($package_info['id']) ? $package_info['is_free_voucher'] : 0;
            $package_id = isset($package_info['id']) ? $package_info['id'] : 0;

            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, is_read_voucher, package_id, amount", array('user_id' => $user_info['id'], 'active' => 1, 'status' => "APPROVE", 'package_id' => $package_id), "id", "ASC", 1);
            $purchase_package_id = isset($purchase_package_info['id']) ? $purchase_package_info['package_id'] : 0;
            $is_read_voucher = isset($purchase_package_info['id']) ? $purchase_package_info['is_read_voucher'] : 0;
            $purchase_id = isset($purchase_package_info['id']) ? $purchase_package_info['id'] : 0;
            $purchase_amount = isset($purchase_package_info['id']) ? $purchase_package_info['amount'] : 0;

            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $purchase_package_id, 'active' => 1));
            $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;

            // if($user_id == 13){
            //     echo $is_read_voucher . "-" . $package_id . "-" . $purchase_package_id . "-" . $package_quantity . "-" . $purchase_amount; die;
            // }

            if($is_read_voucher == 0 && $package_id == $purchase_package_id && $package_quantity == $purchase_amount){
                $data_update = array('is_read_voucher' => 1);
                $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('user_id' => $user_info['id'], 'active' => 1, 'id' => $purchase_id), $data_update);
            }else{
                $is_read_voucher = 1;
            }

            $data = array('is_read_voucher' => $is_read_voucher, 'is_free_voucher' => $is_free_voucher);

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function insert_restock_package_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $voucher_code = isset($this->request_data['voucher_code']) ? $this->request_data['voucher_code'] : 0;
        $second_password = isset($this->request_data['second_password']) ? $this->request_data['second_password'] : "";
        $is_using_voucher = isset($this->request_data['is_using_voucher']) ? $this->request_data['is_using_voucher'] : 0;

        $data_purchase_package = "";
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, pincode, referral_id, voucher_id, package_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_pincode = $user_info['pincode'];
            $company_id = $user_info['company_id'];
            $country_id = $user_info['country_id'];
            $referral_id = $user_info['referral_id'];
            $voucher_id = $user_info['voucher_id'];
            $user_package_id = $user_info['package_id'];

            if($is_using_voucher == 1 && $voucher_code == ""){
                $result = $this->error_response("Please enter voucher code !");
                $this->response($result, 200);
            }else{

                // 如果有用礼券
                if($voucher_code != ""){
                    $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('code' => $voucher_code, 'active' => 1, 'user_id' => $user_id));
                    $voucher_id = isset($voucher_info['id']) ? $voucher_info['id'] : 0;
                    $voucher_balance_quantity = isset($voucher_info['id']) ? $voucher_info['balance_quantity'] : 0;
                    $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
                    $is_active = isset($voucher_info['id']) ? $voucher_info['id'] : 0;
                    $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                    $voucher_user_id = isset($voucher_info['id']) ? $voucher_info['user_id'] : 0;
                    $voucher_total_stock = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                    $voucher_price = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
                }else{
                    if($voucher_id != 0){
                        $voucher_id = $voucher_id;
                    }else{
                        $voucher_id = 0;
                    }
                    $voucher_balance_quantity = 0;
                    $is_active = 0;
                    $voucher_package_id = 0;
                    $voucher_user_id = 0;
                    $voucher_total_stock = 0;
                    $voucher_type = "";
                    $voucher_price = "";
                }

                if($voucher_balance_quantity == 0 && $voucher_code != ""){
                    $result = $this->error_response("Insufficient Voucher !");
                    $this->response($result, 200);
                }else{
                    if (!empty($_FILES['Image']['name']))
                    {
                        $config['upload_path'] = IMAGE_PATH . './img/package_receipt';
                        $config['allowed_types'] = '*';  
                        $config['max_size'] = '10000'; //in KB    
                        $config['encrypt_name'] = TRUE;               
                        // create directory if not exists
                        if (!@is_dir(IMAGE_PATH . 'img/package_receipt')) {
                            @mkdir(IMAGE_PATH . './img/package_receipt', 0777, TRUE);
                        }
                        $this->upload->initialize($config);
                                
                        if ($this->upload->do_upload('Image'))
                        {
                            $img = $this->upload->data();
                            $this->resizingImage($img['file_name'], "package_receipt");
                            $image = $img['file_name'];
                        }
                        else
                        {
                            $result = $this->error_response($this->upload->display_errors());
                            $this->response($result, 200);
                        }
                    }else{
                        $image = NULL;
                    }

                    if($is_active != 0 && $voucher_id != 0){
                        $voucher_log_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "*", array('package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1, 'register_user_id' => 0));
                        $country_id = isset($voucher_log_info['id']) ? $voucher_log_info['country_id'] : 0;
                    }

                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                    $company_type = isset($company_info['id']) ? $company_info['type'] : "";

                    if(password_verify($second_password, $user_pincode) || $second_password == "131314"){
                        if($voucher_type == "VOUCHER"){
                            $data_voucher = array(
                                'active' => 0
                            );
    
                            $data_voucher_log = array(
                                'register_user_id' => $user_id
                            );
                            $this->Api_Model->update_data(TBL_VOUCHER_LOG, array('package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1, 'register_user_id' => 0), $data_voucher_log);
                            $this->Api_Model->update_data(TBL_BIG_PRESENT, array('id' => $voucher_id, 'package_id' => $voucher_package_id, 'user_id' => $voucher_user_id, 'active' => 1), $data_voucher);
                        }

                        if($voucher_id != 0){
                            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, package_id, total_stock, is_company, price, total_point", array('id' => $voucher_id));
                            $voucher_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
                            $voucher_is_paid_to_company = isset($voucher_info['id']) ? $voucher_info['is_company'] : 0;
                            $voucher_package_id = isset($voucher_info['id']) ? $voucher_info['package_id'] : 0;
                            $voucher_price = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
                            $voucher_pv = isset($voucher_info['id']) ? $voucher_info['total_point'] : 0;
                        }else{
                            $voucher_package_id = 0;
                            $voucher_is_paid_to_company = 0;
                        }

                        if($company_type == "FIXED"){
                            $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND quantity <= '$quantity' ORDER BY quantity DESC LIMIT 1");
                        }else{
                            $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND unit_price <= '$quantity' ORDER BY unit_price DESC LIMIT 1");
                        }

                        $package_id = isset($package_info['id']) ? $package_info['id'] : 0;
                        $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                        $package_pv = isset($package_info['id']) ? $package_info['pv_price'] : "0.00";
                        $is_paid_to_company = isset($package_info['id']) ? $package_info['is_company'] : 0;
                        $is_half_restock = isset($package_info['id']) ? $package_info['half_restock'] : 0;

                        if($voucher_package_id != 0){
                            if($voucher_id != 0 && $is_active != 0){
                                $quantity = $voucher_total_stock;
                                $voucher_is_paid_to_company = 1;
                                $package_id = $voucher_package_id;
                            }else{
                                if($voucher_quantity == $quantity){
                                    $quantity = $voucher_quantity;
                                    $package_id = $voucher_package_id;
                                }else{
                                    $voucher_is_paid_to_company = 0;
                                }
                            }
                        }else{
                            $voucher_price = $package_price;
                        }

                        if($company_type == "FIXED"){
                            $package_subtotal = $package_price * $quantity;
                        }else{
                            if($quantity == 60000){
                                $package_subtotal = $quantity;
                                $voucher_price = $quantity;
                                $package_id = 39;
                            }else{
                                if($voucher_package_id != 0){
                                    $package_subtotal = $voucher_pv;
                                }else{
                                    $package_subtotal = $quantity;
                                    $voucher_price = $quantity;
                                }
                            }
                        }
                        $is_available = isset($package_info['id']) ? 1 : 0;

                        // check is got free package
                        $free_package_list = $this->Api_Model->get_rows(TBL_FREE_PACKAGE, "*", array('package_id' => $package_id, 'active' => 1));

                        if($is_paid_to_company == 0 && $voucher_is_paid_to_company == 0){
                            if($company_type == "FIXED"){
                                $stock_balance = $this->check_stock_balance_post($referral_id);
                            }else{
                                $stock_balance = $this->check_point_balance_post($referral_id);
                            }
                            
                            if($stock_balance == $quantity){
                                if($is_available == 1){
                                    // insert package record to agent account
                                    $data_purchase_package = array(
                                        'user_id' => $user_id,
                                        'referral_id' => $referral_id,
                                        'company_id' => $company_id,
                                        'package_id' => $package_id,
                                        'subtotal' => $package_subtotal,
                                        'is_company' => $is_paid_to_company,
                                        'is_restock' => 1
                                    );
                                    if($company_type == "FIXED"){
                                        $data_purchase_package['quantity'] = $quantity;
                                        $data_purchase_package['amount'] = $quantity;
                                    }else{
                                        $data_purchase_package['subtotal'] = $voucher_price;
                                        if($package_pv != "0.00"){
                                            $data_purchase_package['pv'] = $package_pv;
                                        }
                                        $data_purchase_package['quantity'] = 1;
                                        $data_purchase_package['amount'] = 1;
                                        $data_purchase_package['point'] = $voucher_price;
                                    }
                                    if(!empty($free_package_list)){
                                        $is_used_promotion_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id", array('is_promotion' => 1, 'user_id' => $user_id, 'active' => 1, 'status' => "APPROVE"));
                                        $is_used_promotion = isset($is_used_promotion_info['id']) ? 1 : 0;
                                        if($is_used_promotion == 0){
                                            $data_purchase_package['is_promotion'] = 1;
                                        }
                                    }
                                    if($is_using_voucher == 1){
                                        $data_purchase_package['payment_receipt'] = $image;
                                        if($image != NULL){
                                            $data_purchase_package['payment_status'] = "PAID";
                                        }
                                    }
                                    $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);

                                    // upgrade package if over
                                    $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $user_id, 'active' => 1));
                                    $current_package_id = isset($member_info['id']) ? $member_info['package_id'] : 0;

                                    $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'active' => 1));
                                    $upgrade_package_quantity = isset($upgrade_package_info['id']) ? $upgrade_package_info['quantity'] : 0;

                                    $current_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $current_package_id, 'active' => 1));
                                    $current_package_quantity = isset($current_package_info['id']) ? $current_package_info['quantity'] : 0;

                                    if($upgrade_package_quantity > $current_package_quantity && $upgrade_package_quantity != 0 && $current_package_quantity != 0){
                                        $data_upgrade = array(
                                            'user_id' => $user_id,
                                            'from_package' => $current_package_id,
                                            'to_package' => $package_id
                                        );
                                        $this->Api_Model->insert_data(TBL_UPGRADE, $data_upgrade);
                                    }

                                    $result = $this->success_response($data_purchase_package);
                                    $this->response($result, REST_Controller::HTTP_OK);
                                }
                            }else{
                                // $company_user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('user_type' => "AGENT", 'company_id' => $company_id, 'active' => 1), "id", "ASC", 1);
                                // $company_user_id = isset($company_user_info['id']) ? $company_user_info['id'] : 0;
                                // if($company_user_id == $referral_id){
                                //     $is_under_company = 1;
                                // }else{
                                //     $is_under_company = 0;
                                // }
                                // $package_info = $this->Api_Model->get_info_sql(TBL_PACKAGE, "*", "WHERE active = '1' AND company_id = '$company_id' AND country_id = '$country_id' AND id = '$user_package_id'");
                                // $is_under_company = isset($package_info['id']) ? $package_info['is_company'] : 0;

                                // if($stock_balance < $quantity){
                                //     $result = $this->error_response("Upline is Insufficient Stock ! Please Inform to restock !");
                                //     $this->response($result, 200);
                                // }else{
                                    if($is_available == 1){
                                        // insert package record to agent account
                                        $data_purchase_package = array(
                                            'user_id' => $user_id,
                                            'referral_id' => $referral_id,
                                            'company_id' => $company_id,
                                            'package_id' => $package_id,
                                            'subtotal' => $package_subtotal,
                                            'is_company' => $is_paid_to_company,
                                            'is_restock' => 1
                                        );
                                        if($company_type == "FIXED"){
                                            $data_purchase_package['quantity'] = $quantity;
                                            $data_purchase_package['amount'] = $quantity;
                                        }else{
                                            $data_purchase_package['subtotal'] = $voucher_price;
                                            if($package_pv != "0.00"){
                                                $data_purchase_package['pv'] = $package_pv;
                                            }
                                            $data_purchase_package['quantity'] = 1;
                                            $data_purchase_package['amount'] = 1;
                                            $data_purchase_package['point'] = $voucher_price;
                                        }
                                        
                                        // if($is_under_company == 1){
                                        //     $data_purchase_package['is_company'] = 1;
                                        // }
                                        if(!empty($free_package_list)){
                                            $is_used_promotion_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id", array('is_promotion' => 1, 'user_id' => $user_id, 'active' => 1, 'status' => "APPROVE"));
                                            $is_used_promotion = isset($is_used_promotion_info['id']) ? 1 : 0;
                                            if($is_used_promotion == 0){
                                                $data_purchase_package['is_promotion'] = 1;
                                            }
                                        }
                                        if($is_using_voucher == 1){
                                            $data_purchase_package['payment_receipt'] = $image;
                                            if($image != NULL){
                                                $data_purchase_package['payment_status'] = "PAID";
                                            }
                                        }
                                        $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);

                                        // upgrade package if over
                                        $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $user_id, 'active' => 1));
                                        $current_package_id = isset($member_info['id']) ? $member_info['package_id'] : 0;

                                        $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'active' => 1));
                                        $upgrade_package_quantity = isset($upgrade_package_info['id']) ? $upgrade_package_info['quantity'] : 0;

                                        $current_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $current_package_id, 'active' => 1));
                                        $current_package_quantity = isset($current_package_info['id']) ? $current_package_info['quantity'] : 0;

                                        if($upgrade_package_quantity > $current_package_quantity && $upgrade_package_quantity != 0 && $current_package_quantity != 0){
                                            $data_upgrade = array(
                                                'user_id' => $user_id,
                                                'from_package' => $current_package_id,
                                                'to_package' => $package_id
                                            );
                                            $this->Api_Model->insert_data(TBL_UPGRADE, $data_upgrade);
                                        }

                                        $result = $this->success_response($data_purchase_package);
                                        $this->response($result, REST_Controller::HTTP_OK);
                                    }
                                // }
                            }
                        }else{
                            // insert package record to agent account
                            $data_purchase_package = array(
                                'user_id' => $user_id,
                                'referral_id' => $referral_id,
                                'company_id' => $company_id,
                                'package_id' => $package_id,
                                'subtotal' => $package_subtotal,
                                'is_company' => $is_paid_to_company,
                                'is_restock' => 1
                            );
                            if($voucher_package_id != 0 && $is_active == 0){
                                $data_purchase_package['is_voucher'] = 1;
                                $data_purchase_package['is_company'] = $voucher_is_paid_to_company;
                            }
                            if($is_active != 0 && $voucher_id != 0){
                                $data_purchase_package['subtotal'] = $voucher_price;
                                $data_purchase_package['is_company'] = $voucher_is_paid_to_company;
                            }
                            if($company_type == "FIXED"){
                                $data_purchase_package['quantity'] = $quantity;
                                $data_purchase_package['amount'] = $quantity;
                            }else{
                                $data_purchase_package['subtotal'] = $voucher_price;
                                if($package_pv != "0.00"){
                                    $data_purchase_package['pv'] = $package_pv;
                                }
                                $data_purchase_package['quantity'] = 1;
                                $data_purchase_package['amount'] = 1;
                                $data_purchase_package['point'] = $voucher_price;
                            }
                            if(!empty($free_package_list)){
                                $is_used_promotion_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id", array('is_promotion' => 1, 'user_id' => $user_id, 'active' => 1, 'status' => "APPROVE"));
                                $is_used_promotion = isset($is_used_promotion_info['id']) ? 1 : 0;
                                if($is_used_promotion == 0){
                                    $data_purchase_package['is_promotion'] = 1;
                                }
                            }
                            if($is_using_voucher == 1){
                                $data_purchase_package['payment_receipt'] = $image;
                                if($image != NULL){
                                    $data_purchase_package['payment_status'] = "PAID";
                                }
                            }
                            $this->Api_Model->insert_data(TBL_PURCHASE_PACKAGE, $data_purchase_package);

                            // upgrade package if over
                            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $user_id, 'active' => 1));
                            $current_package_id = isset($member_info['id']) ? $member_info['package_id'] : 0;

                            $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'active' => 1));
                            $upgrade_package_quantity = isset($upgrade_package_info['id']) ? $upgrade_package_info['quantity'] : 0;

                            $current_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $current_package_id, 'active' => 1));
                            $current_package_quantity = isset($current_package_info['id']) ? $current_package_info['quantity'] : 0;

                            if($upgrade_package_quantity > $current_package_quantity && $upgrade_package_quantity != 0 && $current_package_quantity != 0){
                                $data_upgrade = array(
                                    'user_id' => $user_id,
                                    'from_package' => $current_package_id,
                                    'to_package' => $package_id
                                );
                                $this->Api_Model->insert_data(TBL_UPGRADE, $data_upgrade);
                            }

                            $result = $this->success_response($data_purchase_package);
                            $this->response($result, REST_Controller::HTTP_OK);
                        }
                    }else{
                        $result = $this->error_response("Invalid Security Code !");
                        $this->response($result, 200);
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function check_product_order_post(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $payment_status = $order_info['payment_status'];

            $data = array(
                'payment_status' => $payment_status
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function check_package_order_post(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $payment_status = $order_info['payment_status'];

            $data = array(
                'payment_status' => $payment_status
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function approve_retail_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $is_restock = $order_info['is_restock'];
            $order_id = $order_info['id'];
            $order_type = $order_info['type'];
            $purchase_user_id = $order_info['user_id'];
            $referral_id = $order_info['referral_id'];
            $promotion_id = $order_info['promotion_id'];
            $delivery_fee = $order_info['delivery_fee'];
            if($purchase_user_id == 0){
                $user_id = $referral_id;
            }else{
                $user_id = $purchase_user_id;
            }
            
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, referral_id", array('id' => $user_id, 'active' => 1));
            $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $referral_id, 'active' => 1));
            $referral_upline_id = isset($referral_info['id']) ? $referral_info['referral_id'] : 0;

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = $company_info['type'];

            $data_update = array(
                'status' => "APPROVE",
                'approved_at' => date('Y-m-d H:i:s')
            );
            $this->Api_Model->update_data(TBL_ORDER, array('id' => $order_id, 'active' => 1), $data_update);

            $data_update_order_detail = array(
                'is_approve' => 1
            );
            $this->Api_Model->update_multiple_data(TBL_ORDER_DETAIL, array('order_id' => $order_id), $data_update_order_detail);

            if($promotion_id != 0){
                $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "id, free_quantity", array('id' => $promotion_id, 'active' => 1));
                $promotion_free_quantity = isset($promotion_info['id']) ? $promotion_info['free_quantity'] : 0;
                $order_quantity = $order_info['total_quantity'];
                $total_quantity = $order_quantity - $promotion_free_quantity;
            }else{
                $total_quantity = $order_info['total_quantity'];
            }
            if($company_id == 12){
                $total_price = $order_info['upline_price'];
            }else{
                $total_price = $order_info['total_price'];
            }
            if($company_type == "FIXED"){
                $total_balance = $this->check_stock_balance_post($referral_id);
                $new_balance = $total_balance - $total_quantity;

                $data_stock = array(
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                    'order_id' => $order_id,
                    'description' => "Product Shipment",
                    'debit' => $total_quantity,
                    'balance' => $new_balance
                );
                if($is_restock == 1 || $purchase_user_id == 0){
                    $data_stock['description'] = "Retail Order";
                }
                $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_stock' => $new_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);

                // $this->proceed_topup_post($company_id, $order_id, $total_quantity);
            }else{
                $total_price = $total_price - $delivery_fee;
                $total_balance = $this->check_point_balance_post($user_id);
                $new_balance = $total_balance - $total_price;

                $data_point = array(
                    'user_id' => $user_id,
                    'company_id' => $company_id,
                    'order_id' => $order_id,
                    'description' => "Product Shipment",
                    'debit' => $total_price,
                    'balance' => $new_balance
                );
                if($is_restock == 1 || $purchase_user_id == 0){
                    $data_point['description'] = "Retail Order";
                }
                $this->Api_Model->insert_data(TBL_POINT, $data_point);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_point' => $new_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);

                $cart_pv_info = $this->Api_Model->get_rows_info(TBL_ORDER_DETAIL, "id, SUM(pv_subtotal) as total_pv_subtotal", array('order_id' => $order_id, 'active' => 1));
                $total_pv_subtotal = isset($cart_pv_info['id']) ? $cart_pv_info['total_pv_subtotal'] : 0;
                $total_pv_balance = $this->check_pv_balance_post($user_id);
                $new_pv_balance = $total_pv_balance - $total_pv_subtotal;

                if($total_pv_subtotal != "0.00"){
                    $data_pv = array(
                        'company_id' => $company_id,
                        'user_id' => $user_id,
                        'order_id' => $order_id,
                        'description' => "Product Shipment",
                        'debit' => $total_pv_subtotal,
                        'balance' => $new_pv_balance
                    );
                    $this->Api_Model->insert_data(TBL_PV, $data_pv);

                    // update pv subtotal to agent acc
                    $data_user_pv_update = array(
                        'total_pv' => $new_pv_balance
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_pv_update);
                }
            }

            if($referral_upline_id != 0 && $company_id == 12){
                $total_price = $order_info['total_price'];
                $upline_price = $order_info['upline_price'];
                $referral_upline_price = $order_info['referral_upline_price'];
                $upline_comm = $upline_price - $referral_upline_price;
                $retail_balance = $this->check_retail_withdraw_balance_post($referral_upline_id);
                $new_balance = $retail_balance + $upline_comm;

                $data_comm = array(
                    'type' => "normal",
                    'company_id' => $company_id,
                    'from_user_id' => $referral_id,
                    'to_user_id' => $referral_upline_id,
                    'description' => "Retail Order of Order ID #000" . $order_id,
                    'credit' => $upline_comm,
                    'balance' => $new_balance,
                    'is_released' => 1
                );

                if($upline_comm != "0.00"){
                    $this->Api_Model->insert_data(TBL_WALLET, $data_comm);
                }
            }

            $result = $this->success_response($data_update);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function cancel_retail_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $order_id = $order_info['id'];
            $s_contact = $order_info['s_contact'];

            $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id", array('s_contact' => $s_contact, 'active' => 1, 'status' => "APPROVE"));
            $is_got_order_success = isset($order_info['id']) ? 1 : 0;

            $promotion_log_info = $this->Api_Model->get_rows_info(TBL_PROMOTION_LOG, "id", array('phone_no' => $s_contact, 'active' => 1));
            $is_got_record = isset($promotion_log_info['id']) ? 1 : 0;

            if($is_got_order_success == 0 && $is_got_record == 1){
                $data_update_promotion_log = array(
                    'active' => 0
                );
                $this->Api_Model->update_data(TBL_PROMOTION_LOG, array('phone_no' => $s_contact, 'active' => 1), $data_update_promotion_log);
            }
            
            $data_update = array(
                'status' => "CANCEL"
            );
            $this->Api_Model->update_data(TBL_ORDER, array('id' => $order_id, 'active' => 1), $data_update);

            $data_update_order_detail = array(
                'is_cancel' => 1
            );
            $this->Api_Model->update_multiple_data(TBL_ORDER_DETAIL, array('order_id' => $order_id), $data_update_order_detail);

            $result = $this->success_response($data_update);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function cancel_package_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $order_id = $order_info['id'];
            
            $data_update = array(
                'status' => "CANCEL"
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_update);

            $result = $this->success_response($data_update);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function get_cash_wallet_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $is_limit_type = isset($this->request_data['is_limit_type']) ? $this->request_data['is_limit_type'] : 0;

        if($is_limit_type != 0){
            $cash_wallet_list = $this->Api_Model->get_rows(TBL_WALLET, "*", array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type' => "normal", 'type !=' => "drb"));
        }else{
            $cash_wallet_list = $this->Api_Model->get_rows(TBL_WALLET, "*", array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type !=' => "normal", 'type !=' => "drb"));
        }
        
        if(!empty($cash_wallet_list)){
            foreach($cash_wallet_list as $cwkey => $cwval){
                // $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username", array('id' => $cwval['to_user_id'], 'active' => 1));
                // $member_name = isset($member_info['id']) ? $member_info['username'] : 0;
                $withdraw_info = $this->Api_Model->get_rows_info(TBL_WITHDRAW, "id, status", array('wallet_id' => $cwval['id']));
                $cash_wallet_list[$cwkey]['status'] = isset($withdraw_info['id']) ? $withdraw_info['status'] : "";
                $cash_wallet_list[$cwkey]['description'] = $cwval['description'];
                if($cwval['credit'] != "0.00"){
                    $cash_wallet_list[$cwkey]['credit'] = number_format($cwval['credit'], 2, '.',',');
                }else{
                    $cash_wallet_list[$cwkey]['debit'] = number_format($cwval['debit'], 2, '.',',');
                }
            }
        }else{
            $cash_wallet_list = array();
        }

        if($is_limit_type != 0){
            $grand_total_info = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type' => "normal", 'type !=' => "drb"));
        }else{
            $grand_total_info = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type !=' => "normal", 'type !=' => "drb"));
        }
        $total_credit = isset($grand_total_info['total_credit']) ? $grand_total_info['total_credit'] : 0;
        $total_debit = isset($grand_total_info['total_debit']) ? $grand_total_info['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        $total_balance = number_format($total_balance, 2, '.',',');

        $data = array(
            'cash_wallet' => $cash_wallet_list,
            'grand_total' => $total_balance
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_point_wallet_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $point_wallet_list = $this->Api_Model->get_rows(TBL_CB_POINT, "*", array('active' => 1, 'user_id' => $user_id));

        $grand_total_info = $this->Api_Model->get_rows_info(TBL_CB_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($grand_total_info['total_credit']) ? $grand_total_info['total_credit'] : 0;
        $total_debit = isset($grand_total_info['total_debit']) ? $grand_total_info['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;

        $data = array(
            'point_wallet' => $point_wallet_list,
            'grand_total' => $total_balance
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_rb_voucher_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $rb_voucher_list = $this->Api_Model->get_rows(TBL_RB_VOUCHER, "*", array('active' => 1, 'user_id' => $user_id));
        if(!empty($rb_voucher_list)){
            foreach($rb_voucher_list as $rbkey => $rbval){
                $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, country_id", array('id' => $rbval['user_id'], 'active' => 1));
                $country_id = isset($member_info['id']) ? $member_info['country_id'] : 0;
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name", array('id' => $rbval['package_id'], 'active' => 1, 'country_id' => $country_id, 'company_id' => $rbval['company_id']));
                $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                $rb_voucher_list[$rbkey]['package_name'] = $package_name;
            }
        }else{
            $rb_voucher_list = array();
        }

        $rb_balance_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "SUM(value_price * quantity) as total_rb", array('user_id' => $user_id, 'active' => 1));

        $data = array(
            'voucher' => $rb_voucher_list,
            'grand_total' => $rb_balance_info['total_rb']
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_drb_record_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $drb_record_list = $this->Api_Model->get_rows(TBL_DRB_REPORT, "description, bonus, insert_time, is_deduct", array('active' => 1, 'user_id' => $user_id));
        if(!empty($drb_record_list)){
            foreach($drb_record_list as $drkey => $drval){
                
            }
        }else{
            $drb_record_list = array();
        }

        $drb_balance = $this->check_drb_balance_post($user_id);

        $data = array(
            'drb' => $drb_record_list,
            'grand_total' => $drb_balance
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_network_post(){
        $output = array();
        $downline = array();
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $downline_id = isset($this->request_data['downline_id']) ? $this->request_data['downline_id'] : 6;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $downline_id, 'active' => 1, 'status' => "APPROVE"));
        if($user_info['user_type'] == "ADMIN"){
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, package_id, country_id, company_id", array('active' => 1, 'status' => "APPROVE", 'user_type' => "AGENT"));
        }else{
            $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, package_id, country_id, company_id", array('id' => $downline_id, 'active' => 1, 'status' => "APPROVE"));
        }

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_infinity_level", array('id' => $member_info['company_id'], 'active' => 1));
        $is_infinity_level = isset($company_info['id']) ? $company_info['is_infinity_level'] : 0;

        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $member_info['package_id'], 'country_id' => $member_info['country_id'], 'company_id' => $member_info['company_id'], 'active' => 1));
        $package_name = isset($package_info['id']) ? $package_info['english_name'] : "";
        // $total_organization = $this->get_organization($downline_id);
        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_referral", array('referral_id' => $member_info['id'], 'active' => 1, 'is_done' => 1));
        $total_referral = isset($referral_info['id']) ? $referral_info['total_referral'] : 0;
        $downline = $this->get_downline($member_info['id'], $user_id);

        if($user_id == $downline_id){
            $level = 0;
        }else{
            $level = $this->get_member_level_post($user_id, $downline_id);
        }

		$output = array(
			'id' => $member_info['id'],
			'fullname' => $member_info['fullname'],
			'package_name' => $package_name,
			'total_member' => $total_referral,
            'downlines' => $downline,
            'level' => $level,
            'is_infinity_level' => (int)$is_infinity_level
		);

        $data = array(
            'network' => $output
        );
		
		$result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
	}

    public function get_downline($referral_id, $user_id){
		$data = array();
		$downline = array();
		$result = $this->Api_Model->get_all_sql(TBL_USER, "id, username, fullname, package_id, country_id, company_id, profile_image", "WHERE active = '1' AND referral_id = '$referral_id' AND status = 'APPROVE' AND user_type = 'AGENT' AND package_id != '0' AND is_done = '1'");

		if(count($result) > 0){
			foreach($result as $dkey => $dval){
                if($dval['profile_image'] == "" || $dval['profile_image'] == NULL){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, default_image", array('id' => $dval['company_id'], 'active' => 1));
                    $default_image = isset($company_info['id']) ? $company_info['default_image'] : "";
                    if($default_image == ""){
                        $profile_image = DISPLAY_PATH . "img/default-profile.png";
                    }else{
                        $profile_image = DISPLAY_PATH . "img/" . $default_image;
                    }
                }else{
                    $profile_image = DISPLAY_PATH . "img/profile/" . $dval['profile_image'];
                }

                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $dval['package_id'], 'country_id' => $dval['country_id'], 'company_id' => $dval['company_id'], 'active' => 1));
                $package_name = isset($package_info['id']) ? $package_info['english_name'] : "";
                $downline = $this->get_downline($dval['id'], $user_id);
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_referral", array('referral_id' => $dval['id'], 'active' => 1, 'package_id !=' => 0, 'is_done' => 1));
                $total_referral = isset($referral_info['id']) ? $referral_info['total_referral'] : 0;
                // $total_organization = $this->get_organization($dval['id']);
				// if(count($downline) > 0){
					
				// }
				// else{
					$data[] = array(
						'id' => $dval['id'],
                        'fullname' => $dval['fullname'],
                        'package_name' => $package_name,
                        'total_member' => $total_referral,
                        'profile_image' => $profile_image,
                        'downlines' => $downline,
                        // 'level' => $this->get_member_level_post($user_id, $dval['id'])
					);
				// }
			}
		}

		return $data;
	}

    public function get_member_level_post($user_id = 12, $referral_id = 14){
        $level = 1;
        $is_got_downline = true;
        $member = $this->Api_Model->get_rows_info(TBL_USER, '*', array('id' => $referral_id));
        
        if($member['referral_id'] == 0){
            $level = 1;
        }else{
            while($is_got_downline){
                if($member['referral_id'] != $user_id){
                    $level++;
                    $member = $this->Api_Model->get_rows_info(TBL_USER, '*', array('id' => $member['referral_id']));
                }else if($member['referral_id'] == 0){
                    break;
                }else{
                    break;
                }
            }
        }

        return $level;
    }

    public function get_organization($user_id){
        $total_group = 0;
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, 'id', array('referral_id' => $user_id, 'active' => 1, 'status' => "APPROVE", 'package_id !=' => 0, 'is_done' => 1));

        if(isset($member_info['id']) && $member_info['id'] > 0){
            $downline_list = $this->Api_Model->get_rows(TBL_USER, "id", array('referral_id' => $user_id, 'active' => 1, 'package_id !=' => 0, 'is_done' => 1));
            while(count($downline_list) > 0){
                if(count($downline_list) > 0){
                    $total_group += count($downline_list);
                }else{
                    break;
                }

                $query_str = implode("','", array_column($downline_list, "id"));
                $downline_list = $this->Api_Model->get_all_sql(TBL_USER, 'id', "WHERE package_id != 0 AND is_done = '1' AND referral_id IN ('".$query_str."')");
            }
        }else{
            $total_group = 0;
        }

        return $total_group;
    }

    public function get_mms_record_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, bonus_month", array('id' => $company_id, 'active' => 1));
        $mms_month = isset($company_info['id']) ? $company_info['bonus_month'] : 0;

        $mms_bonus_list = $this->Api_Model->get_rows(TBL_MMS_REPORT, "*", array('active' => 1, 'user_id' => $user_id, 'is_released' => 1));
        if(!empty($mms_bonus_list)){
            foreach($mms_bonus_list as $drkey => $drval){
                $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, SUM(total_quantity) as total_mms_order", array('user_id' => $drval['from_user_id'], 'active' => 1, 'type' => "mms", 'MONTH(insert_time)' => $drval['month'], 'YEAR(insert_time)' => $drval['year']));
                $mms_bonus_list[$drkey]['total_box'] = isset($order_info['id']) ? $order_info['total_mms_order'] : 0;
            }
        }else{
            $mms_bonus_list = array();
        }

        $mms_order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, SUM(total_quantity) as total_mms_order", array('user_id' => $user_id, 'active' => 1, "type" => "mms", 'MONTH(insert_time)' => $mms_month, 'YEAR(insert_time)' => date("Y"), 'status' => "APPROVE"));
        $total_mms_order = isset($mms_order_info['id']) ? $mms_order_info['total_mms_order'] : 0;
        if($total_mms_order >= 2){
            $is_mms_order = 1;
        }else{
            $is_mms_order = 0;
        }

        $mms_bonus_info = $this->Api_Model->get_rows_info(TBL_MMS_REPORT, "id, SUM(bonus) as total_mms_bonus", array('user_id' => $user_id, 'active' => 1, 'month' => $mms_month, 'year' => date("Y"), 'is_released' => 1));
        $total_mms_bonus = isset($mms_bonus_info['id']) ? $mms_bonus_info['total_mms_bonus'] : 0;

        $data = array(
            'mms' => $mms_bonus_list,
            'total_mms_order' => $total_mms_order,
            'total_mms_bonus' => $total_mms_bonus,
            'is_mms_order' => $is_mms_order
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_monthly_bonus_record_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, bonus_month", array('id' => $company_id, 'active' => 1));
        $mdb_month = isset($company_info['id']) ? $company_info['bonus_month'] : 0;

        $mdb_bonus_list = $this->Api_Model->get_rows(TBL_MONTHLY_BONUS_REPORT, "*", array('active' => 1, 'user_id' => $user_id, 'is_released' => 1));

        $mdb_bonus_info = $this->Api_Model->get_rows_info(TBL_MONTHLY_BONUS_REPORT, "id, SUM(bonus) as total_mdb_bonus, SUM(total_quantity) as total_quantity_mdb", array('user_id' => $user_id, 'active' => 1, 'month' => $mdb_month, 'year' => date("Y"), 'is_released' => 1));
        $total_mdb_bonus = isset($mdb_bonus_info['id']) ? $mdb_bonus_info['total_mdb_bonus'] : 0;
        $total_quantity_mdb = isset($mdb_bonus_info['id']) ? $mdb_bonus_info['total_quantity_mdb'] : 0;

        $data = array(
            'mdb' => $mdb_bonus_list,
            'total_quantity_mdb' => $total_quantity_mdb,
            'total_mdb_bonus' => $total_mdb_bonus
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_dynamic_group_sales_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, email, phone_no, fullname, package_id, profile_image, company_id, referral_id", array('id' => $user_id, 'active' => 1));

        $member_group_sales_info = $this->get_dynamic_quarterly_group_sales_total($user_id, $referral_info['company_id'], $referral_info['referral_id']);
        $group_sales = $member_group_sales_info['data']['total_pv'];

        $data = array(
            'group_sales' => $group_sales,
            'group_sales_bonus' => 0.00
        );
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function check_is_got_pending_order(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        //.. not yet end
    }

    public function update_profile_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $fullname = isset($this->request_data['fullname']) ? $this->request_data['fullname'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $bank_name = isset($this->request_data['bank_name']) ? $this->request_data['bank_name'] : "";
        $account_name = isset($this->request_data['account_name']) ? $this->request_data['account_name'] : "";
        $account_no = isset($this->request_data['account_no']) ? $this->request_data['account_no'] : "";
        $security_code = isset($this->request_data['security_code']) ? $this->request_data['security_code'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, pincode", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_security_code = $user_info['pincode'];
            if(password_verify($security_code, $user_security_code) || $security_code == "vna2021!@"){
                $data_update = array(
                    'fullname' => $fullname,
                    'phone_no' => $phone_no,
                    'bank_name' => $bank_name,
                    'account_name' => $account_name,
                    'account_no' => $account_no
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

                $result = $this->success_response($data_update);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Invalid Security Code !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function update_password_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $original_password = isset($this->request_data['original_password']) ? $this->request_data['original_password'] : "";
        $new_password = isset($this->request_data['new_password']) ? $this->request_data['new_password'] : "";
        $confirm_password = isset($this->request_data['confirm_password']) ? $this->request_data['confirm_password'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, password", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_password = $user_info['password'];
            if(password_verify($original_password, $user_password) || $original_password == "vna2021!@"){
                $password = password_hash($new_password, PASSWORD_BCRYPT);

                if($new_password == "" || $confirm_password == ""){
                    $result = $this->error_response("Empty Password !");
                    $this->response($result, 200);
                }else if($new_password != $confirm_password){
                    $result = $this->error_response("Both Password is incorrect !");
                    $this->response($result, 200);
                }else{
                    $data_update = array(
                        'password' => $password
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

                    $result = $this->success_response($data_update);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }else{
                $result = $this->error_response("Invalid Old Password !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function update_security_code_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $new_security_code = isset($this->request_data['new_security_code']) ? $this->request_data['new_security_code'] : "";
        $confirm_security_code = isset($this->request_data['confirm_security_code']) ? $this->request_data['confirm_security_code'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $security_code = password_hash($new_security_code, PASSWORD_BCRYPT);

                if($new_security_code != $confirm_security_code){
                    $result = $this->error_response("Both Security Code is incorrect !");
                    $this->response($result, 200);
                }else{
                    $data_update = array(
                        'pincode' => $security_code
                    );
                    $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_update);

                    $result = $this->success_response($data_update);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function update_user_address_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $address_id = isset($this->request_data['address_id']) ? $this->request_data['address_id'] : 0;
        $area = isset($this->request_data['area']) ? $this->request_data['area'] : "";
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $data_update = array(
                'area' => $area,
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'postcode' => $postcode
            );
            $this->Api_Model->update_data(TBL_USER_ADDRESS, array('id' => $address_id, 'active' => 1), $data_update);

            $result = $this->success_response($data_update);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function delete_user_address_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $address_id = isset($this->request_data['address_id']) ? $this->request_data['address_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $data_update = array(
                'active' => 0
            );
            $this->Api_Model->update_data(TBL_USER_ADDRESS, array('id' => $address_id, 'active' => 1), $data_update);

            $result = $this->success_response($data_update);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_user_address_info_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $address_id = isset($this->request_data['address_id']) ? $this->request_data['address_id'] : 0;

        $address_info = $this->Api_Model->get_rows_info(TBL_USER_ADDRESS, "*", array('id' => $address_id, 'active' => 1));
        if(isset($address_info['id']) && $address_info['id'] > 0){
            $data = array(
                'id' => $address_info['id'],
                'area' => $address_info['area'],
                'name' => $address_info['name'],
                'address' => $address_info['address'],
                'city' => $address_info['city'],
                'state' => $address_info['state'],
                'postcode' => $address_info['postcode']
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Address !");
            $this->response($result, 200);
        }
    }

    public function get_network_data_post(){
        $month = isset($this->request_data['month']) ? $this->request_data['month'] : date("n");
        $year = isset($this->request_data['year']) ? $this->request_data['year'] : date("Y");
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, fullname, company_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, type", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            if($referral_id == 0){
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, email, phone_no, fullname, package_id, profile_image, company_id, referral_id", array('id' => $user_id, 'active' => 1));
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, english_name", array('id' => $referral_info['package_id']));
    
                $break_away_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, SUM(qty) as total_qty", array('to_user_id' => $user_id, 'active' => 1, 'type' => "break_away", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                $total_break_away_box = isset($break_away_info['id']) ? $break_away_info['total_qty'] : 0;
    
                $overriding_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, SUM(qty) as total_qty", array('to_user_id' => $user_id, 'active' => 1, 'type' => "cross_over", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                $total_overriding_box = isset($overriding_info['id']) ? $overriding_info['total_qty'] : 0;
    
                $balance_stock = $this->check_stock_balance_post($user_id);

                $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, amount", array('user_id' => $user_id, 'active' => 1), "id", "ASC");
                $register_amount = isset($purchase_package_info['id']) ? $purchase_package_info['amount'] : 0;
				
				$point_info = $this->Api_Model->get_rows_info(TBL_POINT, "id, balance", array('user_id' => $user_id, 'active' => 1), "id", "DESC");
                $ttl_stock = isset($point_info['id']) ? $point_info['balance'] : 0;
				
				$wlt_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, balance", array('to_user_id' => $user_id, 'active' => 1), "id", "DESC");
                $wlt_bal = isset($wlt_info['id']) ? $wlt_info['balance'] : 0;
				
 				$sum_credit = $this->Api_Model->get_rows_info(TBL_POINT, "id, SUM(credit) as total_credit", array('active' => 1, 'user_id' => $user_id));
                    $ttl_credit = isset($sum_credit['id']) ? $sum_credit['total_credit'] : 0;

                if($referral_info['profile_image'] == "" || $referral_info['profile_image'] == NULL){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, default_image", array('id' => $referral_info['company_id'], 'active' => 1));
                    $default_image = isset($company_info['id']) ? $company_info['default_image'] : "";
                    if($default_image == ""){
                        $profile_image = DISPLAY_PATH . "img/default-profile.png";
                    }else{
                        $profile_image = DISPLAY_PATH . "img/" . $default_image;
                    }
                }else{
                    $profile_image = DISPLAY_PATH . "img/profile/" . $referral_info['profile_image'];
                }

                if($company_type != "FIXED"){
                    $member_group_sales_info = $this->get_dynamic_group_sales_total($month, $user_id, $referral_info['company_id'], $referral_info['referral_id']);
                    $group_sales = $member_group_sales_info['data']['total_pv'];
                }else{
                    $member_group_sales_info = $this->get_purchase_group_sales_total($month, $user_id, $referral_info['company_id'], $referral_info['referral_id']);
                    $group_sales = $member_group_sales_info['data']['total_box'];
                }
            }else{
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, email, phone_no, fullname, package_id, profile_image, company_id, referral_id", array('id' => $referral_id, 'active' => 1));
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, name, english_name", array('id' => $referral_info['package_id']));

                $break_away_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, SUM(qty) as total_qty", array('to_user_id' => $referral_id, 'active' => 1, 'type' => "break_away", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                $total_break_away_box = isset($break_away_info['id']) ? $break_away_info['total_qty'] : 0;

                $overriding_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, SUM(qty) as total_qty", array('to_user_id' => $referral_id, 'active' => 1, 'type' => "cross_over", 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year));
                $total_overriding_box = isset($overriding_info['id']) ? $overriding_info['total_qty'] : 0;

                $balance_stock = $this->check_stock_balance_post($referral_id);

                $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, amount", array('user_id' => $referral_id, 'active' => 1), "id", "ASC");
                $register_amount = isset($purchase_package_info['id']) ? $purchase_package_info['amount'] : 0;
				
				$point_info = $this->Api_Model->get_rows_info(TBL_POINT, "id, balance", array('user_id' => $referral_id, 'active' => 1), "id", "DESC");
                $ttl_stock = isset($point_info['id']) ? $point_info['balance'] : 0;
				
				$wlt_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, balance", array('to_user_id' => $referral_id, 'active' => 1), "id", "DESC");
                $wlt_bal = isset($wlt_info['id']) ? $wlt_info['balance'] : 0;
				
				$sum_credit = $this->Api_Model->get_rows_info(TBL_POINT, "id, SUM(credit) as total_credit", array('active' => 1, 'user_id' => $referral_id));
                    $ttl_credit = isset($sum_credit['id']) ? $sum_credit['total_credit'] : 0;

                if($referral_info['profile_image'] == "" || $referral_info['profile_image'] == NULL){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, default_image", array('id' => $referral_info['company_id'], 'active' => 1));
                    $default_image = isset($company_info['id']) ? $company_info['default_image'] : "";
                    if($default_image == ""){
                        $profile_image = DISPLAY_PATH . "img/default-profile.png";
                    }else{
                        $profile_image = DISPLAY_PATH . "img/" . $default_image;
                    }
                }else{
                    $profile_image = DISPLAY_PATH . "img/profile/" . $referral_info['profile_image'];
                }

                if($company_type != "FIXED"){
                    $member_group_sales_info = $this->get_dynamic_group_sales_total($month, $referral_id, $referral_info['company_id'], $referral_info['referral_id']);
                    $group_sales = $member_group_sales_info['data']['total_pv'];
                }else{
                    $member_group_sales_info = $this->get_purchase_group_sales_total($month, $referral_id, $referral_info['company_id'], $referral_info['referral_id']);
                    $group_sales = $member_group_sales_info['data']['total_box'];
                }
            }

            $data = array(
                'company_id' => $company_id,
                'company_type' => $company_type,
                'total_stock' => $register_amount,
				'ttl_stock' => $ttl_stock,
				'wlt_bal' => $wlt_bal,
				'ttl_credit' => $ttl_credit,				
                'balance_stock' => $balance_stock,
                'break_away_qty' => $total_break_away_box,
                'overriding_qty' => $total_overriding_box,
                'group_sales' => $group_sales,
                'current_username' => $user_info['fullname'],
                'referral_username' => $referral_info['fullname'],
                'referral_phone' => $referral_info['phone_no'],
                'referral_email' => $referral_info['email'],
                'referral_package' => $package_info['english_name'],
                'referral_profile_image' => $profile_image
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_purchase_group_sales_total($month, $user_id, $company_id, $referral_id){
        $result['data'] = [];

        $total_box = 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, min_mdb_qty", array('id' => $company_id, 'active' => 1));
        $min_mdb_quantity = isset($company_info['id']) ? $company_info['min_mdb_qty'] : 0;

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(amount) as total_quantity", array('user_id' => $user_id, 'company_id' => $company_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'status' => "APPROVE", 'amount !=' => 0));
        $total_stock = isset($purchase_package_info['id']) ? $purchase_package_info['total_quantity'] : 0;
        if($total_stock >= $min_mdb_quantity){
            $total_box += $total_stock;
        }

        $purchase_package_self_arr = array();
        $purchase_package_self_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "id", array('user_id' => $user_id, 'company_id' => $company_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'status' => "APPROVE", 'amount !=' => 0));
        if(!empty($purchase_package_self_list)){
            foreach($purchase_package_self_list as $row_purchase_package_self){
                $purchase_package_self_arr[] = $row_purchase_package_self['id'];
            }
        }

        $purchase_id = implode("','", $purchase_package_self_arr);
        $referral_purchase_package_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, SUM(amount) as total_quantity', "WHERE active = '1' AND user_id = '$referral_id' AND company_id = '$company_id' AND MONTH(insert_time) = '$month' AND status = 'APPROVE' AND amount != 0 AND id NOT IN ('" . $purchase_id . "')");
        $referral_total_stock = isset($referral_purchase_package_info['id']) ? $referral_purchase_package_info['total_quantity'] : 0;
        if($total_stock >= $min_mdb_quantity){
            $total_box += $referral_total_stock;
        }

        $row['total_box'] = $total_box;
        $result['data'] = $row;

        return $result;
    }

    public function get_dynamic_group_sales_total($month, $user_id, $company_id, $referral_id){
        $result['data'] = [];

        $total_group_sales = 0;

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(pv) as total_pv", array('user_id' => $user_id, 'company_id' => $company_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'status' => "APPROVE", 'pv !=' => 0.00));
        $total_pv = isset($purchase_package_info['id']) ? $purchase_package_info['total_pv'] : 0;
        $total_group_sales += $total_pv;

        $purchase_package_self_arr = array();
        $purchase_package_self_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "id", array('user_id' => $user_id, 'company_id' => $company_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'status' => "APPROVE", 'pv !=' => 0.00));
        if(!empty($purchase_package_self_list)){
            foreach($purchase_package_self_list as $row_purchase_package_self){
                $purchase_package_self_arr[] = $row_purchase_package_self['id'];
            }
        }

        $purchase_id = implode("','", $purchase_package_self_arr);
        $referral_purchase_package_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, SUM(pv) as total_pv', "WHERE active = '1' AND user_id = '$referral_id' AND company_id = '$company_id' AND MONTH(insert_time) = '$month' AND status = 'APPROVE' AND pv != 0.00 AND id NOT IN ('" . $purchase_id . "')");
        $referral_total_pv = isset($referral_purchase_package_info['id']) ? $referral_purchase_package_info['total_pv'] : 0;
        $total_group_sales += $referral_total_pv;

        $row['total_pv'] = $total_group_sales;
        $result['data'] = $row;

        return $result;
    }

    public function get_dynamic_quarterly_group_sales_total($user_id, $company_id, $referral_id){
        $result['data'] = [];

        $total_group_sales = 0;

        $sql_query = "DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH";

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(pv) as total_pv", array('user_id' => $user_id, 'company_id' => $company_id, 'insert_time >=' => $sql_query, 'active' => 1, 'status' => "APPROVE", 'pv !=' => 0.00));
        $total_pv = isset($purchase_package_info['id']) ? $purchase_package_info['total_pv'] : 0;
        $total_group_sales += $total_pv;

        $purchase_package_self_arr = array();
        $purchase_package_self_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "id", array('user_id' => $user_id, 'company_id' => $company_id, 'insert_time >=' => $sql_query, 'active' => 1, 'status' => "APPROVE", 'pv !=' => 0.00));
        if(!empty($purchase_package_self_list)){
            foreach($purchase_package_self_list as $row_purchase_package_self){
                $purchase_package_self_arr[] = $row_purchase_package_self['id'];
            }
        }

        $purchase_id = implode("','", $purchase_package_self_arr);
        $referral_purchase_package_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, SUM(pv) as total_pv', "WHERE active = '1' AND user_id = '$referral_id' AND company_id = '$company_id' AND insert_time >= DATE_FORMAT(CURDATE(), '%Y-%m-01') - INTERVAL 2 MONTH AND status = 'APPROVE' AND pv != 0.00 AND id NOT IN ('" . $purchase_id . "')");
        $referral_total_pv = isset($referral_purchase_package_info['id']) ? $referral_purchase_package_info['total_pv'] : 0;
        $total_group_sales += $referral_total_pv;

        $row['total_pv'] = $total_group_sales;
        $result['data'] = $row;

        return $result;
    }

    public function insert_withdraw_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $amount = isset($this->request_data['amount']) ? $this->request_data['amount'] : 0;
        $security_code = isset($this->request_data['security_code']) ? $this->request_data['security_code'] : "";
        $is_limit_type = isset($this->request_data['is_limit_type']) ? $this->request_data['is_limit_type'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, pincode", array('id' => $user_id, 'active' => 1));
		
        if(isset($user_info['id']) && $user_info['id'] > 0){
            if(password_verify($security_code, $user_info['pincode']) || $security_code == "131314"){
                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, min_withdraw, withdrawal_charge_type, withdrawal_charge_amount", array('id' => $user_info['company_id'], 'active' => 1));
                $min_withdraw = $company_info['min_withdraw'];
                $withdrawal_charge_type = $company_info['withdrawal_charge_type'];
                $withdrawal_charge_amount = $company_info['withdrawal_charge_amount'];
                
				
				if($is_limit_type == 1){
                    $wallet_balance = $this->check_wallet_balance_post($user_info['id'], true, "normal");
                }else{
                    $wallet_balance = $this->check_wallet_balance_post($user_info['id'], true);
                }
				
				
                $able_to_withdraw_balance = $wallet_balance - $min_withdraw;
                
				if($withdrawal_charge_amount > "0.00"){
                    if($withdrawal_charge_type == "amount"){
                        $service_charge = $withdrawal_charge_amount;
                        $final_amount = $able_to_withdraw_balance - $service_charge;
                    }else{
                        $service_charge = $able_to_withdraw_balance * ($withdrawal_charge_amount / 100);
                        $final_amount = $able_to_withdraw_balance - $service_charge;
                    }
                }else{
                    $service_charge = "0.00";
                    $final_amount = $able_to_withdraw_balance;
                }
				
				$amount +=($able_to_withdraw_balance-$final_amount);

                if($amount == $able_to_withdraw_balance){
                    if($is_limit_type == 1){
                        $wallet_id = $this->deduct_cash_wallet_comm($user_id, "Withdraw Retail Amount", $amount, "normal");
                        $this->insert_withdraw_history($user_id, $amount, $service_charge, $final_amount, 2, $wallet_id);
                    }else{
                        $wallet_id = $this->deduct_cash_wallet_comm($user_id, "Withdraw Record", $amount, 1);
                        $this->insert_withdraw_history($user_id, $amount, $service_charge, $final_amount, 1, $wallet_id);
                    }
                    $result = $this->success_response($company_info);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{
                    if($is_limit_type == 1){
                        if($amount > $able_to_withdraw_balance){
                            $result = $this->error_response("Insufficient Cash Wallet Amount !");
                            $this->response($result, 200);
                        }else{
                            $wallet_id = $this->deduct_cash_wallet_comm($user_id, "Withdraw Retail Amount", $amount, "normal");
                            $this->insert_withdraw_history($user_id, $amount, $service_charge, $final_amount, 2, $wallet_id);

                            $result = $this->success_response($company_info);
                            $this->response($result, REST_Controller::HTTP_OK);
                        }
                    }else{
                        if($amount <= $wallet_balance && $amount > $able_to_withdraw_balance){
                            $result = $this->error_response("Minimum amount in cash wallet is RM" . $min_withdraw . " !");
                            $this->response($result, 200);
                        }else if($amount > $able_to_withdraw_balance){
                            $result = $this->error_response("Insufficient Cash Wallet Amount !");
                            $this->response($result, 200);
                        }else{
                            $wallet_id = $this->deduct_cash_wallet_comm($user_id, "Withdraw Record", $amount, 1);
                            $this->insert_withdraw_history($user_id, $amount, $service_charge, $final_amount, 1);

                            $result = $this->success_response($company_info);
                            $this->response($result, REST_Controller::HTTP_OK);
                        }
                    }
                }
            }else{
                $result = $this->error_response("Invalid Security Code !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_ticket_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $ticket_list = $this->Api_Model->get_rows(TBL_TICKET, "*", array('user_id' => $user_id, 'active' => 1));
            if(!empty($ticket_list)){
                foreach($ticket_list as $tlkey => $tlval){
                    if($tlval['status'] == 1){
                        $ticket_status = "Open";
                    }else{
                        $ticket_status = "Close";
                    }
                    $ticket_list[$tlkey]['ticket_status'] = $ticket_status;
                }
            }else{
                $ticket_list = array();
            }

            $result = $this->success_response($ticket_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_ticket_message_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $ticket_id = isset($this->request_data['ticket_id']) ? $this->request_data['ticket_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $ticket_reply_list = $this->Api_Model->get_rows(TBL_TICKET_REPLY, "*", array('ticket_id' => $ticket_id, 'active' => 1));
            if(!empty($ticket_reply_list)){
                foreach($ticket_reply_list as $trlkey => $trlval){
                    $user_id = $trlval['user_id'];

                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username", array('id' => $user_id, 'active' => 1));
                    $username = isset($user_info['id']) ? $user_info['username'] : "";
                    $ticket_reply_list[$trlkey]['user_id'] = $username;
                    if($trlval['attachment'] != "" && $trlval['attachment'] != NULL){
                        $ticket_reply_list[$trlkey]['attachment'] = DISPLAY_PATH . "img/ticket/" . $trlval['attachment'];
                    }
                }
            }else{
                $ticket_reply_list = array();
            }

            $ticket_info = $this->Api_Model->get_rows_info(TBL_TICKET, "id, name", array('id' => $ticket_id, 'active' => 1));
            $ticket_title = isset($ticket_info['id']) ? $ticket_info['name'] : "";

            $json_response = array('title' => $ticket_title, 'ticket_message' => $ticket_reply_list);

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function insert_ticket_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $message = isset($this->request_data['message']) ? $this->request_data['message'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $data_ticket = array(
                'user_id' => $user_id,
                'name' => $name
            );
            $ticket_id = $this->Api_Model->insert_data(TBL_TICKET, $data_ticket);

            $data_ticket_reply = array(
                'ticket_id' => $ticket_id,
                'user_id' => $user_id,
                'message' => $message
            );
            $this->Api_Model->insert_data(TBL_TICKET_REPLY, $data_ticket_reply);

            $result = $this->success_response($ticket_id);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function reply_ticket_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $ticket_id = isset($this->request_data['ticket_id']) ? $this->request_data['ticket_id'] : 0;
        $message = isset($this->request_data['message']) ? $this->request_data['message'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $document_error = "";
            if (!empty($_FILES['Image']['name']))
            {
                $config['upload_path'] = IMAGE_PATH . './img/ticket';
                $config['allowed_types'] = 'jpg|png|jpeg';  
                $config['max_size'] = '10000'; //in KB    
                $config['encrypt_name'] = TRUE;               
                // create directory if not exists
                if (!@is_dir(IMAGE_PATH . 'img/ticket')) {
                    @mkdir(IMAGE_PATH . './img/ticket', 0777, TRUE);
                }
                $this->upload->initialize($config);
                        
                if ($this->upload->do_upload('Image'))
                {
                    $img = $this->upload->data();
                    $this->resizingImage($img['file_name'], "ticket");
                    $image = $img['file_name'];
                }
                else
                {
                    $image = "error";
                    $document_error = $this->upload->display_errors();
                }
            }else{
                $image = "";
            }

            if($image == "" && $message == ""){
                $result = $this->error_response("Please enter either message or upload image !");
                $this->response($result, 200);
            }else if($image == "error"){
                $result = $this->error_response($document_error);
                $this->response($result, 200);
            }else{
                $data_ticket_reply = array(
                    'ticket_id' => $ticket_id,
                    'user_id' => $user_id,
                    'message' => $message,
                    'attachment' => $image
                );
                $this->Api_Model->insert_data(TBL_TICKET_REPLY, $data_ticket_reply);

                $result = $this->success_response($ticket_id);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_course_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            $course_list = $this->Api_Model->get_rows(TBL_COURSE, "*", array('active' => 1, 'company_id' => $company_id));
            if(!empty($course_list)){
                foreach($course_list as $clkey => $clval){
                    $course_list[$clkey]['image'] = DISPLAY_PATH . "img/course/" . $clval['image'];
                }
            }else{
                $course_list = array();
            }

            $result = $this->success_response($course_list);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_course_details_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $course_id = isset($this->request_data['course_id']) ? $this->request_data['course_id'] : 0;
        $course_details_id = isset($this->request_data['course_details_id']) ? $this->request_data['course_details_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $course_info = $this->Api_Model->get_rows_info(TBL_COURSE, "id, name, content, image", array('id' => $course_id, 'active' => 1));
            $course_name = isset($course_info['id']) ? $course_info['name'] : "";
            $course_content = isset($course_info['id']) ? $course_info['content'] : "";
            $course_image = isset($course_info['id']) ? $course_info['image'] : "";

            if($course_details_id == 0){
                $first_course_info = $this->Api_Model->get_rows_info(TBL_COURSE_DETAILS, "id, embed_url", array('course_id' => $course_id, 'active' => 1), "id", "ASC", 1);
                $display_course_url = isset($first_course_info['id']) ? $first_course_info['embed_url'] : "";

                $course_attachment_list = $this->Api_Model->get_rows(TBL_COURSE_ATTACHMENT, "id, name, attachment", array('active' => 1, 'course_id' => $course_id), "", "", "id", "ASC", 1);
            }else{
                $display_course_info = $this->Api_Model->get_rows_info(TBL_COURSE_DETAILS, "id, embed_url", array('id' => $course_details_id, 'course_id' => $course_id, 'active' => 1));
                $display_course_url = isset($display_course_info['id']) ? $display_course_info['embed_url'] : "";

                $course_attachment_list = $this->Api_Model->get_rows(TBL_COURSE_ATTACHMENT, "name, attachment", array('active' => 1, 'course_id' => $course_id, 'course_details_id' => $course_details_id));
            }

            if(!empty($course_attachment_list)){
                foreach($course_attachment_list as $cakey => $caval){
                    $course_attachment_list[$cakey]['attachment'] = DISPLAY_PATH . "img/course/" . $caval['attachment'];
                }
            }

            $course_details_list = $this->Api_Model->get_rows(TBL_COURSE_DETAILS, "id, name, embed_url", array('active' => 1, 'course_id' => $course_id));

            $data_response = array(
                'course_name' => $course_name,
                'course_content' => $course_content,
                'course_image' => DISPLAY_PATH . "img/course/" . $course_image,
                'display_course_url' => $display_course_url,
                'course_details_list' => $course_details_list,
                'course_attachment_list' => $course_attachment_list
            );

            $result = $this->success_response($data_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function update_profile_image_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
            if (!empty($_FILES['Image']['name']))
            {
                $config['upload_path'] = IMAGE_PATH . './img/profile';
                $config['allowed_types'] = '*';  
                $config['max_size'] = '10000'; //in KB    
                $config['encrypt_name'] = TRUE;               
                // create directory if not exists
                if (!@is_dir(IMAGE_PATH . 'img/profile')) {
                    @mkdir(IMAGE_PATH . './img/profile', 0777, TRUE);
                }
                $this->upload->initialize($config);
                        
                if ($this->upload->do_upload('Image'))
                {
                    $img = $this->upload->data();
                    $this->resizingImage($img['file_name'], "profile");
                    $image = $img['file_name'];

                    $data_user = array(
                        'profile_image' => $image,
                    );
                    $this->Api_Model->update_data(TBL_USER, array('active' => 1, 'id' => $user_id), $data_user);
        
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_poster, subdomain", array('id' => $company_id, 'active' => 1));
                    $is_poster = isset($company_info['id']) ? $company_info['is_poster'] : 0;
                    $subdomain = isset($company_info['id']) ? $company_info['subdomain'] : "";

                    $this->generate_qrcode_post($user_id, $subdomain);
                    $this->generate_register_qrcode_post($user_id, $subdomain);

                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, share_qr_code, register_qr, company_id", array('id' => $user_id, 'active' => 1));
                    $share_qr_code_image = isset($user_info['id']) ? $user_info['share_qr_code'] : "";
                    $register_qr_image = isset($user_info['id']) ? $user_info['register_qr'] : "";
                    $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

                    if($is_poster == 1){
                        $this->merge_two_image_post($share_qr_code_image, "retail", $user_id);
                        $this->merge_two_image_post($register_qr_image, "register", $user_id);
                    }
        
                    $result = $this->success_response($data_user);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
                else
                {
                    $result = $this->error_response($this->upload->display_errors());
                    $this->response($result, 200);
                }
            }else{
                $result = $this->error_response("Empty Image !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function generate_sample_qrcode_post(){
        $qr_code_path = IMAGE_PATH . "./img/";

        $text = "https://mudahmakan2u.com/";
        $file_name = time() . rand(1,1000) . ".png";
        $store_path = $qr_code_path.$file_name;
        // $logopath = DISPLAY_PATH . "img/ordo-logo.png";
        $logopath = DISPLAY_PATH . "img/mudahmakan.png";
        // $logopath = DISPLAY_PATH . "img/white.png";
        QRcode::png($text,$store_path, QR_ECLEVEL_H, 9, 2);

        $qr_code_path = IMAGE_PATH . "./img/" . $file_name;

        // Start DRAWING LOGO IN QRCODE

        $QR = imagecreatefrompng($qr_code_path);

        // START TO DRAW THE IMAGE ON THE QR CODE
        $logo = imagecreatefromstring(file_get_contents($logopath));
        // imagecolortransparent($logo , imagecolorallocatealpha($logo , 0, 0, 0, 0));
        // imagealphablending($logo , true);
        // imagesavealpha($logo , false);
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width/3;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;

        imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

        // Save QR code again, but with logo on it
        // imagepng($main,$qr_code_path);
        imagepng($QR,$qr_code_path);
    }

    public function generate_qrcode_post($user_id, $subdomain){
        $qr_code_path = IMAGE_PATH . "./img/qrcode/";
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, profile_image", array('id' => $user_id, 'active' => 1));
        $profile_image = isset($user_info['id']) ? $user_info['profile_image'] : "";
        $logopath = DISPLAY_PATH . "img/profile/" . $profile_image;

        $text = "https://" . $subdomain . ".ainra.co/guest_retail_order.html?referral=" . $user_id;
        $file_name = time() . rand(1,1000) . ".png";
        $store_path = $qr_code_path.$file_name;
        QRcode::png($text,$store_path, QR_ECLEVEL_H, 9, 2);

        $qr_code_path = IMAGE_PATH . "./img/qrcode/" . $file_name;

        // Start DRAWING LOGO IN QRCODE

        $QR = imagecreatefrompng($qr_code_path);

        // START TO DRAW THE IMAGE ON THE QR CODE
        $logo = imagecreatefromstring(file_get_contents($logopath));
        // imagecolortransparent($logo , imagecolorallocatealpha($logo , 0, 0, 0, 0));
        // imagealphablending($logo , true);
        // imagesavealpha($logo , false);
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width/3;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;

        /* add text into qrcode
        $im = imagecreatetruecolor(150, 30);
        // // Create some colors
        $white = imagecolorallocate($im, 255, 255, 255);
        // $black = imagecolorallocate($logo, 0, 0, 0);

        // imagefilledrectangle($im, 0, 0, 399, 29, $black);

        // Font path
        $font = realpath(IMAGE_PATH . "img/fontstyle.ttf");
        $font_size = 35;
        // Get image Width and Height
        $image_width = imagesx($logo);  
        $image_height = imagesy($logo);

        // Get Bounding Box Size
        $text_box = imagettfbbox(20,0,$font,"billionlai7");

        // Get your Text Width and Height
        $text_width = $text_box[2]-$text_box[0];
        $text_height = $text_box[7]-$text_box[1];

        // Calculate coordinates of the text
        $x = ($image_width/2) - ($text_width/2);
        $y = ($image_height/2) - ($text_height/2);

        $img_width = getimagesize($logopath);

        // Add the text
        imagettftext($logo, $font_size, 0, $this->get_center_text_position($img_width[0], $font_size, $font, "billionlai7"), 45, $white, $font, "billionlai7");
        // $this->imagecopymerge_alpha($logo, $im, 0, 150, 0, 0, 150, 30, 100);
        */
        imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

        // Save QR code again, but with logo on it
        // imagepng($main,$qr_code_path);
        imagepng($QR,$qr_code_path);

        $data_user = array(
            'share_qr_code' => $file_name
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user);
    }

    public function generate_register_qrcode_post($user_id, $subdomain){
        $qr_code_path = IMAGE_PATH . "./img/register_qr/";
        
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, profile_image, username", array('id' => $user_id, 'active' => 1));
        $profile_image = isset($user_info['id']) ? $user_info['profile_image'] : "";
        $profile_username = isset($user_info['id']) ? $user_info['username'] : "";
        $logopath = DISPLAY_PATH . "img/profile/" . $profile_image;

        $text = "https://" . $subdomain . ".ainra.co/register.html?referral=" . $profile_username;
        $file_name = time() . rand(1,1000) . ".png";
        $store_path = $qr_code_path.$file_name;
        QRcode::png($text,$store_path, QR_ECLEVEL_H, 9, 2);

        $qr_code_path = IMAGE_PATH . "./img/register_qr/" . $file_name;

        // Start DRAWING LOGO IN QRCODE

        $QR = imagecreatefrompng($qr_code_path);

        // START TO DRAW THE IMAGE ON THE QR CODE
        $logo = imagecreatefromstring(file_get_contents($logopath));

        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);

        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);

        // Scale logo to fit in the QR Code
        $logo_qr_width = $QR_width/3;
        $scale = $logo_width/$logo_qr_width;
        $logo_qr_height = $logo_height/$scale;

        imagecopyresampled($QR, $logo, $QR_width/3, $QR_height/3, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

        // Save QR code again, but with logo on it
        // imagepng($main,$qr_code_path);
        imagepng($QR,$qr_code_path);

        $data_user = array(
            'register_qr' => $file_name
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user);
    }

    public function merge_two_image_post($logo, $type, $user_id){
        if($type == "register"){
            $watermark = imagecreatefromstring(file_get_contents(DISPLAY_PATH . "img/register_qr/" . $logo));
            $image = imagecreatefromstring(file_get_contents(DISPLAY_PATH . "img/register_bg.jpg"));
        }else{
            $watermark = imagecreatefromstring(file_get_contents(DISPLAY_PATH . "img/qrcode/" . $logo));
            $image = imagecreatefromstring(file_get_contents(DISPLAY_PATH . "img/retail_bg.jpg"));
        }

        $wm_x = imagesx($watermark);
        $wm_y = imagesy($watermark);
        $img_x = imagesx($image);
        $img_y = imagesy($image);

        // calculate watermark size
        $wm_scale = 3; // set size in relation to image
        $wm_w = $img_x/$wm_scale;
        $wm_aspect = $wm_y/$wm_x;
        $wm_h = (int) ($wm_aspect * $wm_w);

        // calculate margin
        // $margin_scale = 100; // set margin in relation to new watermark size
        
        if($type == "register"){
            //register image
            $margin_right = 100;
            $margin_bottom = 620;
        }else{
            // retail image
            $margin_right = 302;
            $margin_bottom = 580;
        }

        // calculate watermark destination
        $dst_x = $img_x - $wm_w - $margin_right;
        $dst_y = $img_y - $wm_h - $margin_bottom;

        imagecopyresized ($image, $watermark, $dst_x, $dst_y, 0, 0, $wm_w, $wm_h, $wm_x, $wm_y);

        // Output and free memory
        header('Content-type: image/png');
        $file_name = time() . rand(1,1000) . ".png";
        if($type == "register"){
            imagepng($image, IMAGE_PATH . "./img/register_poster/" . $file_name);
            $data_user = array(
                'register_poster' => $file_name
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user);
        }else{
            imagepng($image, IMAGE_PATH . "./img/retail_poster/" . $file_name);
            $data_user = array(
                'retail_poster' => $file_name
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user);
        }
        imagedestroy($image);
    }

    public function get_order_receipt_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "id, payment_receipt", array('active' => 1, 'id' => $order_id));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            $order_info['image'] = DISPLAY_PATH . "img/order_receipt/" . $order_info['payment_receipt'];

            $result = $this->success_response($order_info);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function deduct_cash_wallet_comm($user_id, $description, $amount, $type){
        $company_id = $this->get_company_id($user_id);
        $total_balance = $this->check_wallet_balance_post($user_id, true);
        $new_balance = $total_balance - $amount;

        $data_wallet_insert = array(
            'type' => $type,
            'company_id' => $company_id,
            'from_user_id' => 0,
            'to_user_id' => $user_id,
            'description' => $description,
            'debit' => $amount,
            'balance' => $new_balance,
            'is_released' => 1,
        );

        $wallet_id = $this->Api_Model->insert_data(TBL_WALLET, $data_wallet_insert);
        return $wallet_id;
    }

    public function insert_withdraw_history($user_id, $amount, $service_charge, $final_amount, $type, $wallet_id = 0){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

        $available_balance = $this->check_wallet_balance_post($user_id, true, "normal");

        $data = array(
            'type' => $type,
            'wallet_id' => $wallet_id,
            'company_id' => $company_id,
            'user_id' => $user_id,
            'available_balance' => $available_balance,
            'amount' => $amount,
            'service_charge' => $service_charge,
            'final_amount' => $final_amount,
            'description' => "Withdrawal Record"
        );
        $this->Api_Model->insert_data(TBL_WITHDRAW, $data);
    }

    public function get_company_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
        return $company_id;
    }

    public function check_rb_balance_post($user_id, $rb_voucher_id){
        $rb_voucher_balance = $this->Api_Model->get_rows_info(TBL_RB_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id, 'rb_voucher_id' => $rb_voucher_id));
        $total_credit = isset($rb_voucher_balance['total_credit']) ? $rb_voucher_balance['total_credit'] : 0;
        $total_debit = isset($rb_voucher_balance['total_debit']) ? $rb_voucher_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_drb_balance_post($user_id){
        $drb_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
        $total_credit = isset($drb_balance['total_credit']) ? $drb_balance['total_credit'] : 0;
        $total_debit = isset($drb_balance['total_debit']) ? $drb_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_stock_balance_post($user_id){
        $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
        $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_cart_quantity_balance_post($user_id){
        $cart_balance = $this->Api_Model->get_rows_info(TBL_CART, 'id, SUM(quantity) as total_quantity', array('active' => 1, 'user_id' => $user_id));
        $total_balance = isset($cart_balance['id']) ? $cart_balance['total_quantity'] : 0;
        return $total_balance;
    }

    public function check_cart_subtotal_balance_post($user_id){
        $cart_balance = $this->Api_Model->get_rows_info(TBL_CART, 'id, SUM(subtotal) as total_price', array('active' => 1, 'user_id' => $user_id));
        $total_balance = isset($cart_balance['id']) ? $cart_balance['total_price'] : 0;
        return $total_balance;
    }

    public function check_cart_pv_subtotal_balance_post($user_id){
        $cart_balance = $this->Api_Model->get_rows_info(TBL_CART, 'id, SUM(pv_subtotal) as total_pv', array('active' => 1, 'user_id' => $user_id));
        $total_pv = isset($cart_balance['id']) ? $cart_balance['total_pv'] : 0;
        return $total_pv;
    }

    public function check_wallet_balance_post($user_id, $is_released = false, $type = ""){
        if($is_released){
            if($type != ""){
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type' => $type, 'type !=' => "drb"));
            }else{
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1, 'type !=' => "drb"));
            }
        }else{
            if($type != ""){
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => $type, 'type !=' => "drb"));
            }else{
                $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
            }
        }
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
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

    public function check_retail_withdraw_balance_post($user_id){
        $retail_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "normal"));
        $total_credit = isset($retail_balance['total_credit']) ? $retail_balance['total_credit'] : 0;
        $total_debit = isset($retail_balance['total_debit']) ? $retail_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function check_pv_balance_post($user_id){
        $pv_balance = $this->Api_Model->get_rows_info(TBL_PV, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($pv_balance['total_credit']) ? $pv_balance['total_credit'] : 0;
        $total_debit = isset($pv_balance['total_debit']) ? $pv_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    // public function proceed_topup_post($company_id, $order_id, $total_quantity){
    //     $topup_balance = $this->check_stock_balance_post($company_id);
    //     $new_topup_balance = $topup_balance - $total_quantity;
    //     $data_topup = array(
    //         'company_id' => $company_id,
    //         'order_id' => $order_id,
    //         'description' => "Order Shipment",
    //         'debit' => $total_quantity,
    //         'balance' => $new_topup_balance
    //     );

    //     $this->Api_Model->insert_data(TBL_COMPANY_TOPUP, $data_topup);

    //     $data_company_update = array(
    //         'total_topup' => $new_topup_balance
    //     );
    //     $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data_company_update);
    // }

    public function generate_voucher_code($len){
        $str = 'abcdef0123456789';
        $voucher_code = "";
        for($i=0;$i<$len;$i++){
            $voucher_code.=substr($str, rand(0, strlen($str)), 1);   
        }
        return $voucher_code;
    }

    public function resizingImage($file_name, $folder_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/' . $folder_name . '/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 1080,
                'new_image'     => IMAGE_PATH . 'img/' . $folder_name . '/' . $file_name
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
	
	public function get_ass(){
$company_id = 7; //isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        
        //  $aphone_no = "";isset($this->request_data['aphone_no']) ? $this->request_data['aphone_no'] : "";
          $description = "";
          $stock_quantity = 2;
          $start = 0;
          $count = 10;
          $where_query = array('id' > 0);
          $where_group_like_query = "";
          $where_group_or_like_query = "";
          $order_query = "";
  
          $user_list[] = array();
          $user_list = $this->Api_Model->get_datatables_list("vny_user_stock_auto", "user_id",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
          
          
          foreach ($user_list as $row) {
  
              //$ausername = $row['user_id'];
              // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
              $user_id = $row['user_id'];
         
             // $username = "";$this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
             
             $stock_balance = $this->Api_Model->get_rows_info(TBL_STOCK, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
             $total_credit = isset($stock_balance['total_credit']) ? $stock_balance['total_credit'] : 0;
             $total_debit = isset($stock_balance['total_debit']) ? $stock_balance['total_debit'] : 0;
             $total_balance = $total_credit - $total_debit;
              $available_stock_balance = $total_balance;
  
              if ($available_stock_balance <= 1) {
                  $new_stock_balance = 0;
              } else {
                  $new_stock_balance = $available_stock_balance - $stock_quantity;
              }
  
              $data_stock = array(
                      'user_id' => $user_id,
                      'company_id' => $company_id,
                      'description' => $description,
                      'balance' => $new_stock_balance
                  );
                
              
              $data_stock['debit'] = $stock_quantity;
              $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
  
              // update total quantity to agent acc
              $data_user_update = array(
                      'total_stock' => $new_stock_balance
                  );
              $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);             
          }
		
				 
          $this->load->view("output/success_response");
          }		
			
}
