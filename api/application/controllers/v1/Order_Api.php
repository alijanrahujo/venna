<?php
require_once APPPATH.'libraries/mpdf/autoload.php';

class Order_Api extends Base_Controller {
    public function __construct(){
        parent::__construct();
    }

    public function index_get(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function index_post(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function approve_package_order_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $package_order_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "*", array('id' => $order_id, 'active' => 1));
        if(isset($package_order_info['id']) && $package_order_info['id'] > 0){
            $purchase_id = $package_order_info['id'];
            $referral_id = $package_order_info['referral_id'];
            $user_id = $package_order_info['user_id'];
            $company_id = $package_order_info['company_id'];
            $package_id = $package_order_info['package_id'];
            $is_voucher = $package_order_info['is_voucher'];
            $is_restock = $package_order_info['is_restock'];
            $restock_quantity = $package_order_info['quantity'];
            $restock_point = $package_order_info['point'];
            $is_paid_to_company = $package_order_info['is_company'];
            $purchase_package_pv = $package_order_info['pv'];
            $restock_subtotal = $package_order_info['subtotal'];

            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, voucher_id, is_old, country_id", array('id' => $user_id, 'active' => 1));
            $user_voucher_id = isset($user_info['id']) ? $user_info['voucher_id'] : 0;
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "id, type", array('id' => $user_voucher_id));
            $voucher_type = isset($voucher_info['id']) ? $voucher_info['type'] : "";
            $is_old = isset($user_info['id']) ? $user_info['is_old'] : 0;
            $country_id = isset($user_info['id']) ? $user_info['country_id'] : 0;

            // if($user_id == 942){
            //     echo $is_paid_to_company . "-" . $voucher_type . "-" . $is_restock . "-" . $is_voucher; die;
            // }

            if(($is_paid_to_company == 1 && $voucher_type == "BIG_PRESENT") || ($is_paid_to_company == 1 && $is_restock == 1) || ($is_paid_to_company == 1 && $is_voucher == 0)){
                $result = $this->error_response("Invalid Permission to approve !");
                $this->response($result, 200);
            }else{
                if($is_voucher == 1){
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, voucher_id", array('id' => $user_id, 'active' => 1));
                    $voucher_id = isset($user_info['id']) ? $user_info['voucher_id'] : 0;
                }else{
                    $voucher_id = 0;
                }

                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                $company_type = $company_info['type'];
                
                // stock to rb voucher percentage
                $rb_voucher_percentage = $company_info['rb_voucher_qty'];

                // per rb voucher value
                $rb_voucher_value = $company_info['rb_voucher_value'];

                // is pv
                $is_pv = $company_info['is_pv'];

                // check is rb is active
                if($rb_voucher_percentage != 0){
                    $is_active_rb = true;
                }else{
                    $is_active_rb = false;
                }

                // check is tuo li feature is active
                if($company_info['break_away_bonus'] != 0.00){
                    $is_active_break_away = true;
                }else{
                    $is_active_break_away = false;
                }

                // check is yue ji feature is active
                if($company_info['cross_over_bonus'] != 0.00){
                    // if restock, yue ji will be disabled
                    if($is_restock == 1){
                        $is_active_cross_over = false;
                    }else{
                        $is_active_cross_over = true;
                    }
                }else{
                    $is_active_cross_over = false;
                }

                if($company_info['smart_partner_bonus'] != "0.00"){
                    $is_active_smart_partner = true;
                }else{
                    $is_active_smart_partner = false;
                }

                if($is_pv == 1){
                    $is_active_pv = true;
                }else{
                    $is_active_pv = false;
                }

                if($company_info['cb_rate'] != "0.00"){
                    $is_active_cb = true;
                    $cb_point_rate = $company_info['cb_rate'];
                }else{
                    $is_active_cb = false;
                }

                // get package details
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "*", array('id' => $package_id, 'active' => 1));
                $package_name = isset($package_info['id']) ? $package_info['name'] : "";
                $package_price = isset($package_info['id']) ? $package_info['unit_price'] : "0.00";
                $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
                $package_free_quantity = isset($package_info['id']) ? $package_info['free_quantity'] : 0;
                $package_grand_total = isset($package_info['id']) ? $package_info['grand_total'] : 0;
                $package_pv = isset($package_info['id']) ? $package_info['pv_price'] : 0;
                if($purchase_package_pv != 0.00){
                    $package_pv = $purchase_package_pv;
                }
                $is_free_voucher = isset($package_info['id']) ? $package_info['is_free_voucher'] : 0;
                $limit_free_voucher = isset($package_info['id']) ? $package_info['limit_free_voucher'] : 0;

                $insert_voucher_info = $this->Api_Model->get_rows_info(TBL_ANNOUNCEMENT, "id, SUM(active) as total_voucher", array('user_id' => $user_id, 'active' => 1, 'code !=' => ""));
                $total_voucher = isset($insert_voucher_info['id']) ? $insert_voucher_info['total_voucher'] : 0;

                $is_continue_giving_voucher = false;
                if($limit_free_voucher != 0){
                    if($total_voucher < $limit_free_voucher){
                        $is_continue_giving_voucher = true;
                    }
                }

                // insert free voucher to agent account
                if($is_continue_giving_voucher){
                // if($is_paid_to_company == 0 && $is_continue_giving_voucher){
                    $free_voucher_list = $this->Api_Model->get_rows(TBL_FREE_VOUCHER, "*", array('company_id' => $company_id, 'package_id' => $package_id, 'active' => 1));
                    if(!empty($free_voucher_list)){
                        foreach($free_voucher_list as $row_free_voucher){
                            $voucher_code = $this->generate_voucher_code(8) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(4) . "-" . $this->generate_voucher_code(10);

                            $data_voucher = array(
                                'type' => 2,
                                'user_id' => $user_id,
                                'country_id' => $country_id,
                                'company_id' => $company_id,
                                'package_id' => $package_id,
                                'code' => $voucher_code,
                                'quantity' => 1,
                                'balance_quantity' => 1,
                                'price' => $row_free_voucher['price'],
                                'total_stock' => $row_free_voucher['quantity'],
                                'status' => "APPROVE"
                            );
                            $insert_id = $this->Api_Model->insert_data(TBL_BIG_PRESENT, $data_voucher);
                        }

                        $data_voucher_log = array(
                            'user_id' => $user_id,
                            'country_id' => $country_id,
                            'package_id' => $package_id
                        );
                        $data_voucher_log['voucher_id'] = $insert_id;
                        $this->Api_Model->insert_data(TBL_VOUCHER_LOG, $data_voucher_log);

                        $content = "You get a free voucher, voucher code is " . $voucher_code;

                        $data = array(
                            'company_id' => $company_id,
                            'user_id' => $user_id,
                            'code' => $voucher_code,
                            'title' => "Free Voucher",
                            'content' => $content
                        );
                        $this->Api_Model->insert_data(TBL_ANNOUNCEMENT, $data);
                    }
                }

                // if($user_id == 897){
                //     echo "here"; die;
                // }

                // insert voucher into agent account
                if($is_restock == 0){
                    $package_voucher_list = $this->Api_Model->get_rows(TBL_PACKAGE_VOUCHER, "*", array('package_id' => $package_id, 'active' => 1));
                    if(!empty($package_voucher_list)){
                        foreach($package_voucher_list as $row_package_voucher){
                            $data_user_voucher = array(
                                'user_id' => $user_id,
                                'product_id' => $row_package_voucher['product_id'],
                                'quantity' => $row_package_voucher['quantity'],
                                'price' => $row_package_voucher['price']
                            );
                            $this->Api_Model->insert_data(TBL_USER_VOUCHER, $data_user_voucher);
                        }
                    }
                }else{
                    $package_voucher_list = $this->Api_Model->get_rows(TBL_RESTOCK_VOUCHER, "*", array('company_id' => $company_id, 'active' => 1));
                    if(!empty($package_voucher_list)){
                        foreach($package_voucher_list as $row_package_voucher){
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

                // insert free voucher into agent account
                $free_package_list = $this->Api_Model->get_rows(TBL_FREE_PACKAGE, "*", array('package_id' => $package_id, 'active' => 1));
                $is_used_promotion_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id", array('is_promotion' => 1, 'user_id' => $user_id, 'active' => 1, 'status' => "APPROVE"));
                $is_used_promotion = isset($is_used_promotion_info['id']) ? 1 : 0;
                
                if(!empty($free_package_list) && $is_used_promotion == 0){
                    foreach($free_package_list as $row_free_package){
                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, half_quantity, quantity", array('id' => $row_free_package['free_package_id'], 'active' => 1));
                        $is_half_quantity = isset($package_info['id']) ? $package_info['half_quantity'] : 0;
                        $free_package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
                        if($is_half_quantity == 1){
                            $total_stock = $free_package_quantity / 2;
                        }else{
                            $total_stock = $free_package_quantity;
                        }
                        $data_big_present_user = array(
                            'user_id' => $user_id,
                            'package_id' => $row_free_package['free_package_id'],
                            'quantity' => $row_free_package['quantity'],
                            'total_stock' => $total_stock,
                            'is_promotion' => 1
                        );
                        $this->Api_Model->insert_data(TBL_USER_BIG_PRESENT_FREE, $data_big_present_user);
                    }
                }

                // if($user_id == 186){
                //     echo "here"; die;
                // }

                $big_present_log_info = $this->Api_Model->get_info_sql(TBL_BIG_PRESENT_LOG, "*", "WHERE user_id = '$user_id' AND active = '1' ORDER BY id DESC");
                $voucher_log_info = $this->Api_Model->get_rows_info(TBL_VOUCHER_LOG, "*", array('package_id' => $package_id, 'voucher_id' => $voucher_id, 'active' => 1, 'register_user_id' => $user_id));
                if($is_free_voucher == 1){
                    if($is_restock == 1){
                        $total_quantity = $restock_quantity;
                        $grand_total = $restock_point;
                    }else{
                        $total_quantity = $package_quantity;
                        $grand_total = $package_grand_total;
                    }
                }else{
                    if(isset($big_present_log_info['id']) && $big_present_log_info['id'] > 0){
                        $big_present_id = $big_present_log_info['big_present_id'];
                        $big_present_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $big_present_id, 'active' => 1));
                        
                        $total_quantity = $big_present_info['total_stock'];
                        $grand_total = $big_present_info['price'];

                        if($is_restock == 1){
                            $total_quantity = $restock_quantity;
                            $grand_total = $restock_point;
                        }
                    }else if(isset($voucher_log_info['id']) && $voucher_log_info['id'] > 0){
                        $voucher_id = $voucher_log_info['voucher_id'];
                        $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                        
                        $total_quantity = $voucher_info['total_stock'];
                        $grand_total = $voucher_info['price'];

                        if($is_restock == 1){
                            $total_quantity = $restock_quantity;
                            $grand_total = $restock_point;
                        }
                    }else{
                        if($is_restock == 1){
                            $total_quantity = $restock_quantity;
                            $grand_total = $restock_point;
                        }else{
                            if($restock_quantity != 0){
                                $total_quantity = $restock_quantity;
                            }else{
                                $total_quantity = $package_quantity;
                            }
                            $grand_total = $package_grand_total;
                        }
                    }
                }

                // if($is_paid_to_company == 1){
                //     $available_stock_balance = $this->check_stock_balance_post($user_id);
                // }else{
                    $available_stock_balance = $this->check_stock_balance_post($referral_id);
                    $available_point_balance = $this->check_point_balance_post($referral_id);
                // }

                if($company_type == "FIXED"){
                    if($is_voucher == 0){
                        if($available_stock_balance == $total_quantity){
                            $is_continue_approved = true;
                        }else{
                            if($available_stock_balance < $total_quantity){
                                $is_continue_approved = false;
                            }else{
                                $is_continue_approved = true;
                            }
                        }
                    }else{
                        $is_continue_approved = true;
                    }
                }else{
                    if($available_point_balance == $grand_total){
                        $is_continue_approved = true;
                    }else{
                        if($available_point_balance < $grand_total){
                            $is_continue_approved = false;
                        }else{
                            $is_continue_approved = true;
                        }
                    }
                }
                
                if($is_continue_approved){
                    $data_update = array(
                        'status' => "APPROVE",
                        'approved_at' => date('Y-m-d H:i:s')
                    );
                    if(!empty($free_package_list)){
                        $data_update['is_promotion'] = 1;
                    }
                    $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_update);

                    $this->generate_invoice($order_id);
                    $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), array('invoice_name' => "invoice" . $order_id . ".pdf"));

                    // approve upgrade package
                    $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id", array('id' => $user_id, 'active' => 1));
                    $current_package_id = isset($member_info['id']) ? $member_info['package_id'] : 0;
                    $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'active' => 1));
                    $upgrade_package_quantity = isset($upgrade_package_info['id']) ? $upgrade_package_info['quantity'] : 0;
                    $upgrade_package_grand_total = $restock_subtotal;

                    $current_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, grand_total", array('id' => $current_package_id, 'active' => 1));
                    $current_package_quantity = isset($current_package_info['id']) ? $current_package_info['quantity'] : 0;
                    $current_package_grand_total = isset($current_package_info['id']) ? $current_package_info['grand_total'] : 0;

                    if(($upgrade_package_quantity > $current_package_quantity && $upgrade_package_quantity != 0 && $current_package_quantity != 0) || $upgrade_package_grand_total > $current_package_grand_total && $upgrade_package_grand_total != 0 && $current_package_grand_total != 0){
                        $upgrade_package_info = $this->Api_Model->get_rows_info(TBL_UPGRADE, "*", array('user_id' => $user_id, 'status' => "PENDING", 'active' => 1));
                        $data_upgrade = array('status' => "APPROVE");
                        $this->Api_Model->update_data(TBL_UPGRADE, array('user_id' => $user_id, 'status' => "PENDING", 'active' => 1), $data_upgrade);
                        $data_user_upgrade = array('package_id' => $package_id);
                        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_upgrade);
                    }

                    $total_quantity_add_to_agent = $total_quantity + $package_free_quantity;

                    if($company_type == "FIXED"){
                        if($is_paid_to_company == 1){
                            $available_stock_balance = $this->check_stock_balance_post($user_id);

                            $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity_add_to_agent, "credit");
                            $this->update_member_stock_post($available_stock_balance, $total_quantity_add_to_agent, $user_id, "credit");
                        }else{
                            $available_stock_balance = $this->check_stock_balance_post($referral_id);
                        
                            if($available_stock_balance == $total_quantity){
                                // deduct stock from member acc
                                $this->proceed_stock_post($referral_id, $company_id, $purchase_id, $total_quantity, "debit");

                                // insert stock into current acc
                                $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity_add_to_agent, "credit");

                                // update total stock into referral acc
                                $this->update_member_stock_post($available_stock_balance, $total_quantity, $referral_id, "debit");

                                // update total stock into current acc
                                $available_stock_balance = $this->check_stock_balance_post($user_id);
                                $this->update_member_stock_post($available_stock_balance, $total_quantity_add_to_agent, $user_id, "credit", true);
                            }else{
                                if($available_stock_balance < $total_quantity){
                                    $result = $this->error_response("Insufficient Stock ! Please Inform to restock !");
                                    $this->response($result, 200);
                                }else{
                                    $this->proceed_stock_post($referral_id, $company_id, $purchase_id, $total_quantity, "debit");
                                    $this->proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity_add_to_agent, "credit");
                                    $this->update_member_stock_post($available_stock_balance, $total_quantity, $referral_id, "debit");
                                    $available_stock_balance = $this->check_stock_balance_post($user_id);
                                    $this->update_member_stock_post($available_stock_balance, $total_quantity_add_to_agent, $user_id, "credit", true);
                                }
                            }
                        }

                        if($is_active_smart_partner){
                            // check is available for smart partner and update become smart partner
                            $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
                            if(in_array($package_id, $package_id_arr)){
                                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'is_smart_partner' => 1));
                                $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                $is_available_smart_partner = $this->check_is_smart_partner_available($user_id);
                                $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $user_id);
                            }
                        }

                        // give cb point
                        if($is_active_cb){
                            $cb_point = $total_quantity * $cb_point_rate;
                            $this->give_cb_point($user_id, $package_name, $cb_point, $purchase_id);
                        }

                        // if active rb, rb voucher process here
                        if($is_active_rb){
                            $this->proceed_rb_voucher_post($rb_voucher_percentage, $rb_voucher_value, $total_quantity, $package_price, $user_id, $company_id, $package_id, $package_name);
                        }

                        // check is current package member got upline or not
                        $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                        $referral_id = $member_info['referral_id'];
                        $user_package = $member_info['package_id'];

                        // get upline info
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id", array('id' => $referral_id, 'active' => 1));
                        $referral_package = isset($referral_info['id']) ? $referral_info['package_id'] : 0;

                        $yue_ji_log_info = $this->Api_Model->get_rows_info(TBL_YUE_JI_LOG, "id", array('active' => 1, 'referral_id' => $referral_id, 'user_id' => $user_id)); 
                        $referral_is_cross_bonus = isset($yue_ji_log_info['id']) ? 1 : 0;

                        if($referral_id != 0){
                            $current_month = date("m");
                            $current_year = date("Y");
                            
                            $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
                            if($is_restock == 1 && $is_max_package){
                                $is_got_referral_two_max_package = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away", false, true);

                                if($is_max_package || $is_got_referral_two_max_package){
                                    // restock break away process here
                                    $this->check_break_away_restock_requirement("package", $purchase_id, $user_id);
                                }
                            }else{
                                if($referral_package < $user_package){
                                    // check if the referral got find 2 max package
                                    $is_got_referral_two_max_package = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away");

                                    // check is the referral got referral 1 max package and group sales is max package quantity
                                    $personal_group_total_conditions = $this->check_break_away_team_total_box($user_id, $current_month, $current_year);

                                    if($is_got_referral_two_max_package || $personal_group_total_conditions['data']['status']){
                                        // break away process here
                                        if($is_active_break_away){
                                            $this->check_break_away_requirement("package", $purchase_id, $user_id);
                                        }
                                    }else{
                                        // cross over process here
                                        if($is_active_cross_over){
                                            if($referral_is_cross_bonus == 0){
                                                $this->check_cross_away_requirement("package", $purchase_id, $user_id);
                                            }else{
                                                $data_purchase_package_update = array('is_paid' => 1);
                                                $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                            }
                                        }else{
                                            $data_purchase_package_update = array('is_paid' => 1);
                                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                        }
                                    }
                                }else if($is_active_break_away){
                                    $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
                                    $personal_group_total_conditions = $this->check_break_away_team_total_box($member_info['id'], $current_month, $current_year, "", true);

                                    if($is_max_package){
                                        // break away process here
                                        $this->check_break_away_requirement("package", $purchase_id, $user_id);
                                    }else if($personal_group_total_conditions['data']['status']){
                                        $this->check_break_away_requirement("package", $purchase_id, $user_id, true);
                                    }
                                }
                            }
                        }
                    }else{
                        if($is_paid_to_company == 1){
                            $available_point_balance = $this->check_point_balance_post($user_id);
        
                            $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");
                            $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit");
        
                            $available_pv_balance = $this->check_pv_balance_post($user_id);
        
                            $this->proceed_pv_post($user_id, $company_id, $purchase_id, $package_pv, "credit");
                            $this->update_member_pv_post($available_pv_balance, $package_pv, $user_id, "credit");
                        }else{
                            $available_point_balance = $this->check_point_balance_post($referral_id);
                            $available_pv_balance = $this->check_pv_balance_post($referral_id);
                        
                            if($available_point_balance == $grand_total){
                                // deduct stock from member acc
                                $this->proceed_point_post($referral_id, $company_id, $purchase_id, $grand_total, "debit");
                                $this->proceed_pv_post($referral_id, $company_id, $purchase_id, $package_pv, "debit");
        
                                // insert stock into current acc
                                $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");
                                $this->proceed_pv_post($user_id, $company_id, $purchase_id, $package_pv, "credit");
                                
                                // update total stock into referral acc
                                $this->update_member_point_post($available_point_balance, $grand_total, $referral_id, "debit");
                                $this->update_member_pv_post($available_pv_balance, $package_pv, $referral_id, "debit");
        
                                // update total stock into current acc
                                $available_point_balance = $this->check_point_balance_post($user_id);
                                $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit", true);
        
                                $available_pv_balance = $this->check_pv_balance_post($user_id);
                                $this->update_member_pv_post($available_pv_balance, $package_pv, $user_id, "credit", true);
                            }else{
                                if($available_point_balance < $grand_total){
                                    $data['message'] = "Insufficient Point ! Please Inform to restock !";
                                    $this->load->view("output/error_response", $data);
                                }else{
                                    $this->proceed_point_post($referral_id, $company_id, $purchase_id, $grand_total, "debit");
                                    $this->proceed_point_post($user_id, $company_id, $purchase_id, $grand_total, "credit");
                                    $this->proceed_pv_post($referral_id, $company_id, $purchase_id, $package_pv, "debit");
                                    $this->proceed_pv_post($user_id, $company_id, $purchase_id, $package_pv, "credit");
                                    $this->update_member_point_post($available_point_balance, $grand_total, $referral_id, "debit");
                                    $this->update_member_pv_post($available_pv_balance, $package_pv, $referral_id, "debit");
                                    $available_point_balance = $this->check_point_balance_post($user_id);
                                    $this->update_member_point_post($available_point_balance, $grand_total, $user_id, "credit", true);
                                    $available_pv_balance = $this->check_pv_balance_post($user_id);
                                    $this->update_member_pv_post($available_pv_balance, $package_pv, $user_id, "credit", true);
                                }
                            }
                        }
        
                        // check is current package member got upline or not
                        $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                        $referral_id = $member_info['referral_id'];
                        $user_package = $member_info['package_id'];
        
                        // get upline info
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id, package_id", array('id' => $referral_id, 'active' => 1));
                        $referral_package = isset($referral_info['id']) ? $referral_info['package_id'] : 0;
        
                        $yue_ji_log_info = $this->Api_Model->get_rows_info(TBL_YUE_JI_LOG, "id", array('active' => 1, 'referral_id' => $referral_id, 'user_id' => $user_id)); 
                        $referral_is_cross_bonus = isset($yue_ji_log_info['id']) ? 1 : 0;
        
                        if($referral_id != 0){
                            $current_month = date("m");
                            $current_year = date("Y");
                            
                            $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
                            if($is_restock == 1 && $is_max_package){
                                if($is_max_package){
                                    // restock break away process here
                                    $this->check_dynamic_break_away_restock_requirement("package", $purchase_id, $user_id);
                                }
                            }else{
                                if($user_package < $referral_package){
                                    // cross over process here
                                    if($is_active_cross_over){
                                        if($referral_is_cross_bonus == 0){
                                            $this->check_dynamic_cross_away_requirement("package", $purchase_id, $user_id);
                                        }else{
                                            $data_purchase_package_update = array('is_paid' => 1);
                                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                        }
                                    }else{
                                        $data_purchase_package_update = array('is_paid' => 1);
                                        $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                                    }
                                }else if($is_active_break_away){
                                    $is_max_package = $this->check_is_package_max_package($member_info['package_id'], $member_info['company_id'], $member_info['country_id'], "break_away");
        
                                    if($is_max_package){
                                        // break away process here
                                        $this->check_dynamic_break_away_requirement("package", $purchase_id, $user_id);
                                    }
                                }
                            }
                        }
                    }

                    $result = $this->success_response($data_update);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{
                    if($company_type == "FIXED"){
                        $result = $this->error_response("Insufficient Stock ! Please Inform to restock !");
                        $this->response($result, 200);
                    }else{
                        $result = $this->error_response("Insufficient Point ! Please Inform to restock !");
                        $this->response($result, 200);
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid Package Order !");
            $this->response($result, 200);
        }
    }

    public function generate_invoice($order_id)
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $this->page_data['package_info'] = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, "*", "WHERE id = '$order_id'");
        $html = $this->load->view('package_invoice',$this->page_data,true);
        $mpdf->WriteHTML($html);
        $mpdf->Output("../img/package_invoice/invoice" . $order_id . '.pdf','F');
    }

    public function generate_voucher_code($len){
        $str = 'abcdef0123456789';
        $voucher_code = "";
        for($i=0;$i<$len;$i++){
            $voucher_code.=substr($str, rand(0, strlen($str)), 1);   
        }
        return $voucher_code;
    }

    public function check_cross_away_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        // type => package/order
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id, is_voucher, voucher_id', array('id' => $user_id, 'active' => 1));
        $member_package_id = isset($member['id']) ? $member['package_id'] : 0;
        $company_id = isset($member['id']) ? $member['company_id'] : 0;
        $country_id = isset($member['id']) ? $member['country_id'] : 0;
        $referral_id = isset($member['id']) ? $member['referral_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, cross_over_bonus, any_cross_over_bonus", array('id' => $company_id, 'active' => 1));
        $cross_over_bonus_rate = isset($company_info['id']) ? $company_info['cross_over_bonus'] : "0.00";
        $any_cross_over_bonus = isset($company_info['id']) ? $company_info['any_cross_over_bonus'] : "0.00";

        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1), "id", "DESC", 1);
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        if($any_cross_over_bonus != "0.00"){
            $this->proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, 1);
        }else{
            if($cross_over_bonus_rate != "0.00"){
                if($member_package_id == $package_id){
                    $this->proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id);
                }
            }
        }
    }

    public function proceed_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, $is_any_cross_over = 0){
        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, unit", array('id' => $member_package_id, 'active' => 1));
        $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
        $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

        $insert_name = $this->get_purchase_display_data($user_id);
        if($type == "package"){
            $total_stock = $this->get_team_break_away_total_box($member['is_voucher'], $member['voucher_id'], $member['package_id'], $member['company_id'], $member['country_id']);

            // whenever which package is greater than current referral package
            if($is_any_cross_over == 1){
                $cross_over_bonus = $total_stock * $any_cross_over_bonus;
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $any_cross_over_bonus . " per " . $package_unit . "";
            }else{
                // only allow small referral max package
                $cross_over_bonus = $total_stock * $cross_over_bonus_rate;
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $cross_over_bonus_rate . " per " . $package_unit . "";
            }
            
            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "cross_over", $cross_over_remark, $cross_over_bonus, 0, $total_stock);
            $data_purchase_package_update = array(
                // 'is_paid' => 1,
                'wallet_id' => $wallet_id
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

            $data_yue_ji_log = array(
                'referral_id' => $referral_id,
                'user_id' => $user_id
            );
            $this->Api_Model->insert_data(TBL_YUE_JI_LOG, $data_yue_ji_log);
        }
    }

    public function check_dynamic_cross_away_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        // type => package/order
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id, is_voucher, voucher_id', array('id' => $user_id, 'active' => 1));
        $member_package_id = isset($member['id']) ? $member['package_id'] : 0;
        $company_id = isset($member['id']) ? $member['company_id'] : 0;
        $country_id = isset($member['id']) ? $member['country_id'] : 0;
        $referral_id = isset($member['id']) ? $member['referral_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, cross_over_bonus, any_cross_over_bonus", array('id' => $company_id, 'active' => 1));
        $cross_over_bonus_rate = isset($company_info['id']) ? $company_info['cross_over_bonus'] : "0.00";
        $any_cross_over_bonus = isset($company_info['id']) ? $company_info['any_cross_over_bonus'] : "0.00";

        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1), "id", "DESC", 1);
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        if($any_cross_over_bonus != "0.00"){
            $this->proceed_dynamic_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, 1);
        }else{
            if($cross_over_bonus_rate != "0.00"){
                $this->proceed_dynamic_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id);
            }
        }
    }

    public function proceed_dynamic_cross_over_bonus_post($type, $month, $member_package_id, $user_id, $member, $cross_over_bonus_rate, $any_cross_over_bonus, $referral_id, $order_id, $is_any_cross_over = 0){
        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity, unit", array('id' => $member_package_id, 'active' => 1));
        $package_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
        $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

        $company_id = $this->get_company_id($referral_id);

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_break_and_over_percentage", array('id' => $company_id, 'active' => 1));
        $is_break_and_over_percentage = isset($company_info['id']) ? $company_info['is_break_and_over_percentage'] : 0;

        $insert_name = $this->get_purchase_display_data($user_id);
        if($type == "package"){
            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, pv', array('referral_id' => $member['referral_id'], 'active' => 1), "id", "ASC");
            $total_pv = isset($purchase_package_info['id']) ? $purchase_package_info['pv'] : "0.00";

            // whenever which package is greater than current referral package
            if($is_any_cross_over == 1){
                if($is_break_and_over_percentage == 1){
                    $cross_over_bonus = $total_pv * ($any_cross_over_bonus / 100);
                }else{
                    $cross_over_bonus = $total_pv * $any_cross_over_bonus;
                }
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_pv . " " . $package_unit . " @ " . $any_cross_over_bonus . "% per " . $package_unit . "";
            }else{
                // only allow small referral max package
                if($is_break_and_over_percentage == 1){
                    $cross_over_bonus = $total_pv * ($cross_over_bonus_rate / 100);
                }else{
                    $cross_over_bonus = $total_pv * $cross_over_bonus_rate;
                }
                $cross_over_remark = "Overriding (" . strtoupper($month) . ") | Writing by " . $insert_name . " | " . $total_pv . " " . $package_unit . " @ " . $cross_over_bonus_rate . "% per " . $package_unit . "";
            }
            
            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "cross_over", $cross_over_remark, $cross_over_bonus, 0, $total_pv);
            $data_purchase_package_update = array(
                // 'is_paid' => 1,
                'wallet_id' => $wallet_id
            );
            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

            $data_yue_ji_log = array(
                'referral_id' => $referral_id,
                'user_id' => $user_id
            );
            $this->Api_Model->insert_data(TBL_YUE_JI_LOG, $data_yue_ji_log);
        }
    }

    public function check_break_away_restock_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");
        
        $two_max_package_conditions = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away", false, true);

        if($two_max_package_conditions){
            $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $user_id, 'active' => 1));
            $company_id = isset($member['id']) ? $member['company_id'] : 0;
            $country_id = isset($member['id']) ? $member['country_id'] : 0;
            $referral_id = isset($member['id']) ? $member['referral_id'] : 0;
            $member_package_id = isset($member['id']) ? $member['package_id'] : 0;

            $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
            $package_id = implode("','", $package_id_arr);
            $purchase_package_list = $this->Api_Model->get_all_sql(TBL_PURCHASE_PACKAGE, 'id, user_id, wallet_id', "WHERE active = '1' AND referral_id = '" . $member['id'] . "' AND MONTH(insert_time) = '$current_month' AND YEAR(insert_time) = '$current_year' AND package_id IN ('" . $package_id . "') ORDER BY id ASC");

            // if got find under referral is 2 max package
            if(count($purchase_package_list) > 1){
                if($type == "package"){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $company_id, 'active' => 1));
                    $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                    //  check if self got fulfil the requirement for max package
                    $is_self_max_package = $this->check_is_package_max_package($member_package_id, $company_id, $country_id, "break_away");

                    if($is_self_max_package && $break_away_bonus_rate != "0.00"){
                        foreach($purchase_package_list as $row_purchase_package){
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, wallet_id, user_id', array('referral_id' => $member['id'], 'active' => 1, 'id' => $row_purchase_package['id']), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $order_wallet_id = $purchase_package_info['wallet_id'];

                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                            $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                            $data_wallet_update = array(
                                'active' => 0
                            );
                            $this->Api_Model->update_data(TBL_WALLET, array('id' => $order_wallet_id, 'active' => 1), $data_wallet_update);

                            $insert_name = $this->get_purchase_display_data($user_id);

                            $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            // remove cross over comm if have take before
                            $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                            if($check_is_got_cross_over_comm['data']['status']){
                                $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                                
                                $data_wallet_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                                $data_yue_ji_log_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                            }

                            // give break away bonus to referral that one time restock become max package
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                            $wallet_id = $this->give_cash_wallet_comm($member['id'], $order_user_user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                            $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                            $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                            $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                        }
                    }
                }
            }
        }
    }
    
    // break away dynamic bonus
    public function check_dynamic_break_away_restock_requirement($type = "", $order_id, $user_id){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");

        $member = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, company_id, country_id, referral_id", array('id' => $user_id, 'active' => 1));
        $is_max_package = $this->check_is_package_max_package($member['package_id'], $member['company_id'], $member['country_id'], "break_away");

        if($is_max_package){
            $company_id = isset($member['id']) ? $member['company_id'] : 0;
            $country_id = isset($member['id']) ? $member['country_id'] : 0;
            $referral_id = isset($member['id']) ? $member['referral_id'] : 0;
            $member_package_id = isset($member['id']) ? $member['package_id'] : 0;
            $member_id = isset($member['id']) ? $member['id'] : 0;

                if($type == "package"){
                    $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus, is_break_and_over_percentage", array('id' => $company_id, 'active' => 1));
                    $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";
                    $is_break_and_over_percentage = isset($company_info['id']) ? $company_info['is_break_and_over_percentage'] : 0;

                    //  check if self got fulfil the requirement for max package
                    $is_self_max_package = $this->check_is_package_max_package($member_package_id, $company_id, $country_id, "break_away");

                    if($is_self_max_package && $break_away_bonus_rate != "0.00" && $is_break_and_over_percentage){
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, wallet_id, user_id, pv', array('user_id' => $member_id, 'active' => 1), "id", "ASC");
                            if(isset($purchase_package_info['id']) && $purchase_package_info['id'] > 0){
                                $order_package_id = $purchase_package_info['id'];
                                $order_user_package_id = $purchase_package_info['package_id'];
                                $order_user_user_id = $purchase_package_info['user_id'];
                                $order_wallet_id = $purchase_package_info['wallet_id'];
                                $order_pv = $purchase_package_info['pv'];

                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                                $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                                $data_wallet_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_WALLET, array('id' => $order_wallet_id, 'active' => 1), $data_wallet_update);

                                $insert_name = $this->get_purchase_display_data($user_id);

                                if($is_break_and_over_percentage == 1){
                                    $break_away_bonus = $order_pv * ($break_away_bonus_rate / 100);
                                }else{
                                    $break_away_bonus = $order_pv * $break_away_bonus_rate;
                                }

                                // remove cross over comm if have take before
                                $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                                if($check_is_got_cross_over_comm['data']['status']){
                                    $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                                    
                                    $data_wallet_update = array(
                                        'active' => 0
                                    );
                                    $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                                    $data_yue_ji_log_update = array(
                                        'active' => 0
                                    );
                                    $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                                }

                                // give break away bonus to referral that one time restock become max package
                                $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $order_pv . " " . $package_unit . " @ " . $break_away_bonus_rate . "% per " . $package_unit . "";
                                $wallet_id = $this->give_cash_wallet_comm($referral_id, $order_user_user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $order_pv);
                                $data_purchase_package_update = array(
                                    'is_paid' => 1,
                                    'wallet_id' => $wallet_id
                                );
                                $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                                // check is available for smart partner and update become smart partner

                                // to be continue...
                            }
                    }
                }
        }
    }

    public function check_dynamic_break_away_requirement($type = "", $order_id, $user_id, $is_personal_group_sales = false){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");
        // type => package/order
        if($is_personal_group_sales){
            $break_away_conditions = false;
        }else{
            $break_away_conditions = $this->check_upline_is_max_package($user_id);
        }

        if($break_away_conditions && $type != ""){
            $company_id = $this->get_company_id($user_id);
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus, smart_partner_bonus, is_break_away_pass, is_break_and_over_percentage", array('id' => $company_id, 'active' => 1));
            $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";
            $smart_partner_bonus = isset($company_info['id']) ? $company_info['smart_partner_bonus'] : "0.00";
            $is_break_away_pass = isset($company_info['id']) ? $company_info['is_break_away_pass'] : 0;
            $is_break_and_over_percentage = isset($company_info['id']) ? $company_info['is_break_and_over_percentage'] : 0;
            // first conditions, self must is max package, and referral is max package also
            if($break_away_conditions){
                $fulfil_break_away_upline_id = $this->get_fulfil_upline_post($user_id, "break_away");
                if($fulfil_break_away_upline_id != 0){
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                    $referral_id = $this->get_referral_id($user_id);

                    // check is referral got more than 3 organization then pass 1 to upline
                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
                    $package_id_arr = $this->get_conditions_package_id_array($referral_info['company_id'], "break_away");
                    $package_id = implode("','", $package_id_arr);
                    $total_referral_info = $this->Api_Model->get_info_sql(TBL_USER, "id, COUNT(*) as total_referral", "WHERE active = '1' AND referral_id = '$referral_id' AND package_id IN ('" . $package_id . "')");
                    $total_referral = $total_referral_info['total_referral'];
                    
                    $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, COUNT(*) as total_wallet_record", array('to_user_id' => $referral_id, 'MONTH(insert_time)' => $current_month));

                    $insert_name = $this->get_purchase_display_data($user_id);
                    if($type == "package"){
                        $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id, pv", array('id' => $order_id));
                        $total_pv = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['pv'] : "0.00";
                        $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                        $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                        $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                        if($is_break_and_over_percentage == 1){
                            $break_away_bonus = $total_pv * ($break_away_bonus_rate / 100);
                        }else{
                            $break_away_bonus = $total_pv * $break_away_bonus_rate;
                        }
                        $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_pv . " " . $package_unit . " @ " . $break_away_bonus_rate . "% per " . $package_unit . "";

                        if($break_away_bonus != "0.00"){
                            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_pv);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);
                        }
                    }
                }
            }
        }
    }

    public function check_break_away_requirement($type = "", $order_id, $user_id, $is_personal_group_sales = false){
        $month = date("M");
        $current_month = date("m");
        $current_year = date("Y");
        // type => package/order
        if($is_personal_group_sales){
            $break_away_conditions = false;
        }else{
            $break_away_conditions = $this->check_upline_is_max_package($user_id);
        }
        $two_max_package_conditions = $this->check_upline_is_referral_two_max_package($user_id, $current_month, $current_year, "break_away");
        $personal_group_total_conditions = $this->check_break_away_team_total_box($user_id, $current_month, $current_year);

        if(($break_away_conditions || $two_max_package_conditions || $personal_group_total_conditions['data']['status']) && $type != ""){
            $company_id = $this->get_company_id($user_id);
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus, smart_partner_bonus, is_break_away_pass", array('id' => $company_id, 'active' => 1));
            $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";
            $smart_partner_bonus = isset($company_info['id']) ? $company_info['smart_partner_bonus'] : "0.00";
            $is_break_away_pass = isset($company_info['id']) ? $company_info['is_break_away_pass'] : 0;
            // first conditions, self must is max package, and referral is max package also
            if($break_away_conditions){
                $fulfil_break_away_upline_id = $this->get_fulfil_upline_post($user_id, "break_away");
                if($fulfil_break_away_upline_id != 0){
                    $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $user_id, 'active' => 1));
                    $referral_id = $this->get_referral_id($user_id);

                    // check is referral got more than 3 organization then pass 1 to upline
                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
                    $package_id_arr = $this->get_conditions_package_id_array($referral_info['company_id'], "break_away");
                    $package_id = implode("','", $package_id_arr);
                    $total_referral_info = $this->Api_Model->get_info_sql(TBL_USER, "id, COUNT(*) as total_referral", "WHERE active = '1' AND referral_id = '$referral_id' AND package_id IN ('" . $package_id . "')");
                    $total_referral = $total_referral_info['total_referral'];
                    
                    $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id, COUNT(*) as total_wallet_record", array('to_user_id' => $referral_id, 'MONTH(insert_time)' => $current_month));

                    if($is_break_away_pass == 1){
                        if($total_referral > 2){
                            $allow_to_take_bonus = $total_referral - 1;
                            $total_wallet_record = $wallet_info['total_wallet_record'];
                            if($total_wallet_record == $allow_to_take_bonus){
                                $referral_upline_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $referral_id, 'active' => 1));
                                $upline_id = $referral_upline_info['referral_id'];

                                $upline_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, company_id, country_id, package_id', array('id' => $upline_id, 'active' => 1));
                                $is_max_package = $this->check_is_package_max_package($upline_info['package_id'], $upline_info['company_id'], $upline_info['country_id'], "break_away");
                                if($is_max_package){
                                    $referral_upline_id = $upline_id;
                                }else{
                                    $referral_upline_id = $this->get_fulfil_upline_post($upline_id, "break_away");
                                }

                                $insert_name = $this->get_purchase_display_data($user_id);
                                if($type == "package"){
                                    $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                                    $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_id));
                                    $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                                    $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                                    $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                                    $break_away_bonus = $total_stock * $break_away_bonus_rate;
                                    $break_away_remark = "Break Away Pension (" . strtoupper($month) . ") | : Pension by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                                    if($break_away_bonus != "0.00"){
                                        $wallet_id = $this->give_cash_wallet_comm($referral_upline_id, $user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                                        $data_purchase_package_update = array(
                                            'is_paid' => 1,
                                            'wallet_id' => $wallet_id
                                        );
                                        $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

                                        // check is available for smart partner and update become smart partner
                                        if($smart_partner_bonus != "0.00"){
                                            $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_upline_id, 'is_smart_partner' => 1));
                                            $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                            $is_available_smart_partner = $this->check_is_smart_partner_available($referral_upline_id);
                                            $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_upline_id);
                                        }
                                    }
                                }
                            }
                        }else{
                            $insert_name = $this->get_purchase_display_data($user_id);
                            if($type == "package"){
                                $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                                $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_id));
                                $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                                $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                                $break_away_bonus = $total_stock * $break_away_bonus_rate;
                                $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                                if($break_away_bonus != "0.00"){
                                    $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                                    $data_purchase_package_update = array(
                                        'is_paid' => 1,
                                        'wallet_id' => $wallet_id
                                    );
                                    $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

                                    // check is available for smart partner and update become smart partner
                                    if($smart_partner_bonus != "0.00"){
                                        $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                                        $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                        $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                                        $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                                    }
                                }
                            }
                        }
                    }else{
                        $insert_name = $this->get_purchase_display_data($user_id);
                        if($type == "package"){
                            $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                            $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_id));
                            $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                            $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                            $break_away_bonus = $total_stock * $break_away_bonus_rate;
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                            if($break_away_bonus != "0.00"){
                                $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                                $data_purchase_package_update = array(
                                    'is_paid' => 1,
                                    'wallet_id' => $wallet_id
                                );
                                $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_id, 'active' => 1), $data_purchase_package_update);

                                // check is available for smart partner and update become smart partner
                                if($smart_partner_bonus != "0.00"){
                                    $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                                    $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                    $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                                    $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                                }
                            }
                        }
                    }
                }
            }else if($two_max_package_conditions){
                $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $user_id, 'active' => 1));
                $company_id = isset($member['id']) ? $member['company_id'] : 0;
                $country_id = isset($member['id']) ? $member['country_id'] : 0;
                $referral_id = isset($member['id']) ? $member['referral_id'] : 0;
                $package_id = isset($member['id']) ? $member['package_id'] : 0;

                $package_id_arr = $this->get_conditions_package_id_array($company_id, "break_away");
                $package_id = implode("','", $package_id_arr);
                $purchase_package_list = $this->Api_Model->get_all_sql(TBL_PURCHASE_PACKAGE, 'id, user_id', "WHERE active = '1' AND is_paid = '0' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$current_month' AND YEAR(insert_time) = '$current_year' AND package_id IN ('" . $package_id . "') ORDER BY id ASC");
                
                // if got find under referral is 2 max package
                if(count($purchase_package_list) > 1){
                    if($type == "package"){
                        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $company_id, 'active' => 1));
                        $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                        //  check if self got fulfil the requirement for max package
                        $is_self_max_package = $this->check_is_package_max_package($package_id, $company_id, $country_id, "break_away");

                        if(!$is_self_max_package && $break_away_bonus_rate != "0.00"){
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, user_id, SUM(amount) as total_qty_amount', array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $total_stock = $purchase_package_info['total_qty_amount'] / 2;
                            $insert_name = $this->get_purchase_display_data($user_id);

                            // $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            // remove cross over comm if have take before
                            $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                            if($check_is_got_cross_over_comm['data']['status']){
                                $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                                
                                $data_wallet_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                                $data_yue_ji_log_update = array(
                                    'active' => 0
                                );
                                $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                            }

                            $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_package_id));
                            $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                            $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                            // give break away bonus to referral that no fulfil the requirement of max package boxes
                            $break_away_remark = "Break Away (" . strtoupper($month) . ") | : Order ID: " . $order_id . " by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                            $wallet_id = $this->give_cash_wallet_comm($referral_id, $user_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            if($smart_partner_bonus != "0.00"){
                                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                                $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                                $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                            }

                            // give another break away bonus to their upline of the current referral
                            $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, 'id, package_id, user_id', array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0), "id", "ASC");
                            $order_package_id = $purchase_package_info['id'];
                            $order_user_package_id = $purchase_package_info['package_id'];
                            $order_user_user_id = $purchase_package_info['user_id'];
                            $insert_name = $this->get_purchase_display_data($referral_id);

                            $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $order_user_user_id, 'active' => 1));
                            $referral_upline_id = $member['referral_id'];

                            // get until the upline is max package
                            $upline_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, company_id, country_id, package_id', array('id' => $referral_upline_id, 'active' => 1));
                            $is_max_package = $this->check_is_package_max_package($upline_info['package_id'], $upline_info['company_id'], $upline_info['country_id'], "break_away");
                            if($is_max_package){
                                $referral_upline_id = $referral_upline_id;
                            }else{
                                $referral_upline_id = $this->get_fulfil_upline_post($referral_upline_id, "break_away");
                            }

                            // $total_stock = $this->get_conditions_package_quantity_array($order_user_user_id, "break_away");
                            $break_away_bonus = $total_stock * $break_away_bonus_rate;

                            $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_package_id));
                            $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                            $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                            $break_away_remark = "Break Away Pension (" . strtoupper($month) . ") | : Pension by " . $insert_name . " | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                            $wallet_id = $this->give_cash_wallet_comm($referral_upline_id, $referral_id, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                            $data_purchase_package_update = array(
                                'is_paid' => 1,
                                'wallet_id' => $wallet_id
                            );
                            $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $order_package_id, 'active' => 1), $data_purchase_package_update);

                            // check is available for smart partner and update become smart partner
                            if($smart_partner_bonus != "0.00"){
                                $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_upline_id, 'is_smart_partner' => 1));
                                $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                                $is_available_smart_partner = $this->check_is_smart_partner_available($referral_upline_id);
                                $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_upline_id);
                            }
                        }
                    }
                }
            }else if($personal_group_total_conditions['data']['status']){
                $purchase_id_arr = $personal_group_total_conditions['data']['purchase_id'];
                $referral_id = $personal_group_total_conditions['data']['referral_id'];
                $total_quantity = $personal_group_total_conditions['data']['total_quantity'];
                $referral_total_stock = $this->get_balance_purchase_quantity_total($current_month, $referral_id, 1);
                $total_quantity += $referral_total_stock;

                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id, package_id, is_voucher, voucher_id", array('id' => $referral_id, 'active' => 1));
                $total_stock = $total_quantity;
                // $total_stock = $this->get_team_break_away_total_box($user_info['is_voucher'], $user_info['voucher_id'], $user_info['package_id'], $user_info['company_id'], $user_info['country_id']);

                // remove cross over comm if have take before
                $check_is_got_cross_over_comm = $this->check_is_got_cross_over_comm($referral_id);
                if($check_is_got_cross_over_comm['data']['status']){
                    $cross_over_comm_wallet_id = $check_is_got_cross_over_comm['data']['wallet_id'];
                    
                    $data_wallet_update = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_data(TBL_WALLET, array('id' => $cross_over_comm_wallet_id), $data_wallet_update);

                    $data_yue_ji_log_update = array(
                        'active' => 0
                    );
                    $this->Api_Model->update_data(TBL_YUE_JI_LOG, array('referral_id' => $referral_id, 'active' => 1), $data_yue_ji_log_update);
                }

                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, break_away_bonus", array('id' => $user_info['company_id'], 'active' => 1));
                $break_away_bonus_rate = isset($company_info['id']) ? $company_info['break_away_bonus'] : "0.00";

                $break_away_bonus = $total_stock * $break_away_bonus_rate;

                $order_purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, package_id", array('id' => $order_id));
                $order_user_package_id = isset($order_purchase_package_info['id']) ? $order_purchase_package_info['package_id'] : 0;
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, unit", array('id' => $order_user_package_id, 'active' => 1));
                $package_unit = isset($package_info['id']) ? $package_info['unit'] : "";

                $break_away_remark = "Break Away (" . strtoupper($month) . ") | Personal Group Sales | " . $total_stock . " " . $package_unit . " @ RM" . $break_away_bonus_rate . " per " . $package_unit . "";
                if($break_away_bonus != "0.00"){
                    $wallet_id = $this->give_cash_wallet_comm($referral_id, 0, "break_away", $break_away_remark, $break_away_bonus, 0, $total_stock);
                    foreach($purchase_id_arr as $purchase_id){
                        $data_purchase_package_update = array(
                            'is_paid' => 1,
                            'wallet_id' => $wallet_id
                        );
                        $this->Api_Model->update_data(TBL_PURCHASE_PACKAGE, array('id' => $purchase_id, 'active' => 1), $data_purchase_package_update);
                    }

                    // check is available for smart partner and update become smart partner
                    if($smart_partner_bonus != "0.00"){
                        $smart_partner_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $referral_id, 'is_smart_partner' => 1));
                        $is_already_become_smart_partner = isset($smart_partner_info['id']) ? true : false;
                        $is_available_smart_partner = $this->check_is_smart_partner_available($referral_id);
                        $this->update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $referral_id);
                    }
                }
            }
        }
    }

    public function proceed_rb_voucher_post($rb_voucher_percentage, $rb_voucher_value, $package_quantity, $package_price, $user_id, $company_id, $package_id, $package_name){
        // insert rb voucher if have
        $rb_voucher_convert_qty = $rb_voucher_percentage / 100;
        $rb_voucher_convert_value = $rb_voucher_value / 100;
        $rb_quantity = $package_quantity * $rb_voucher_convert_qty;
        $rb_value_price = $package_price * $rb_voucher_convert_value;
        $rb_actual_price = $package_price - $rb_value_price;
        // check if the rb is < 1rb
        $is_insert_rb = ($rb_quantity < 1) ? 0 : 1;

        $rb_voucher_info = $this->Api_Model->get_rows_info(TBL_RB_VOUCHER, "*", array('user_id' => $user_id, 'company_id' => $company_id, 'package_id' => $package_id));
        $rb_current_quantity = isset($rb_voucher_info['quantity']) ? $rb_voucher_info['quantity'] : 0;
        $rb_voucher_id = isset($rb_voucher_info['id']) ? $rb_voucher_info['id'] : 0;
        $rb_new_quantity = $rb_current_quantity + $rb_quantity;

        $data_rb_voucher = array(
            'company_id' => $company_id,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'cost_price' => $package_price,
            'quantity' => $rb_quantity,
            'value_price' => $rb_value_price,
            'actual_price' => $rb_actual_price
        );

        if($is_insert_rb == 1){
            if($rb_current_quantity == 0){
                $rb_voucher_id = $this->Api_Model->insert_data(TBL_RB_VOUCHER, $data_rb_voucher);
            }else{
                $data_exist_voucher = array(
                    'quantity' => $rb_new_quantity
                );
                $this->Api_Model->update_data(TBL_RB_VOUCHER, array('id' => $rb_voucher_id, 'active' => 1), $data_exist_voucher);
            }
            
            $rb_voucher_balance = $this->check_rb_balance_post($rb_voucher_id, $user_id);
            $new_voucher_balance = $rb_voucher_balance + $rb_quantity;

            $data_rb_wallet = array(
                'rb_voucher_id' => $rb_voucher_id,
                'user_id' => $user_id,
                'description' => $package_name,
                'credit' => $rb_quantity,
                'balance' => $new_voucher_balance
            );
            $this->Api_Model->insert_data(TBL_RB_WALLET, $data_rb_wallet);
        }
    }
    

    // check is upline is got referral 2 max package
    public function check_upline_is_referral_two_max_package($insert_id = 0, $month, $year, $condition, $is_smart_partner_check = false, $is_restock = false, $debug = false){
        $break_away_package_list = array();
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id', array('id' => $insert_id, 'active' => 1));
        $company_id = isset($member['id']) ? $member['company_id'] : 0;

        $package_id_arr = $this->get_conditions_package_id_array($company_id, $condition);
        $package_id = implode("','", $package_id_arr);

        if($is_smart_partner_check){
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE active = '1' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }else if($is_restock){
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE active = '1' AND referral_id = '" . $member['id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }else{
            $referral_info = $this->Api_Model->get_info_sql(TBL_PURCHASE_PACKAGE, 'id, COUNT(*) as total_referral', "WHERE is_paid = '0' AND active = '1' AND referral_id = '" . $member['referral_id'] . "' AND MONTH(insert_time) = '$month' AND YEAR(insert_time) = '$year' AND package_id IN ('".$package_id."')");
        }
        
        $total_referral_max_package = isset($referral_info['id']) ? $referral_info['total_referral'] : 0;

        if($total_referral_max_package > 1){
            return true;
        }else{
            return false;
        }
    }

    // check is upline team total got fulfil the break away box requirement
    public function check_break_away_team_total_box($insert_id = 0, $month, $year, $is_smart_partner_check = false, $debug = false){
        $result['data'] = [];
        $purchase_id = [];
    
        $total_quantity = 0;
        $member_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, is_voucher, voucher_id, package_id, company_id, country_id', array('id' => $insert_id, 'active' => 1));
    
        if(isset($member_info['id']) && $member_info['id'] > 0){
            $company_type = $this->get_company_type($member_info['company_id']);
            $referral_id = $member_info['referral_id'];
    
            if($company_type == "FIXED"){
                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
                $referral_stock = $this->get_balance_purchase_quantity_total($month, $referral_info['id']);
                $referral_package_id = $referral_info['package_id'];
                $referral_company_id = $referral_info['company_id'];
                $package_id_arr = $this->get_conditions_package_id_array($referral_company_id, "break_away");
                
                if(in_array($referral_package_id, $package_id_arr)){
                    $total_quantity += $referral_stock;
                }

                $referral_total_stock = $this->get_balance_purchase_quantity_total($month, $referral_info['id'], 1);

                if($is_smart_partner_check){
                    $team_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('referral_id' => $referral_id, 'active' => 1, 'package_id' => 5, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'status' => "APPROVE"));
                }else{
                    $team_list = $this->Api_Model->get_rows(TBL_PURCHASE_PACKAGE, "*", array('referral_id' => $referral_id, 'active' => 1, 'is_paid' => 0, 'MONTH(insert_time)' => $month, 'YEAR(insert_time)' => $year, 'status' => "APPROVE"));
                }
                
                if(!empty($team_list)){
                    foreach($team_list as $row_team){
                        $purchase_id[] = $row_team['id'];
                        $member = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('id' => $row_team['user_id'], 'active' => 1));
                        $total_stock = $this->get_team_break_away_total_box($member['is_voucher'], $member['voucher_id'], $member['package_id'], $member['company_id'], $member['country_id']);
                        $max_package_quantity = $this->get_conditions_package_quantity_array($member['id'], "break_away");
                        
                        if($total_stock != $max_package_quantity){
                            $total_quantity += $total_stock;
                        }
                    }
                }

                // must sponsor at least 1 direct max package
                // $is_got_sponsor_at_least_one_max_package = false;
                // $package_id_arr = $this->get_conditions_package_id_array($referral_info['company_id'], "break_away");
                // $package_id = implode("','", $package_id_arr);
                // $member_list = $this->Api_Model->get_all_sql(TBL_USER, "id", "WHERE active = '1' AND referral_id = '$referral_id' AND package_id IN ('" . $package_id . "')");
                
                // if(count($member_list) > 0){
                //     $is_got_sponsor_at_least_one_max_package = true;
                // }
            }
        }

        if($total_quantity >= $referral_total_stock && $total_quantity != 0 && $referral_total_stock != 0){
            $status = true;
        }else{
            $status = false;
        }

        $row['status'] = $status;
        $row['purchase_id'] = $purchase_id;
        $row['referral_id'] = $referral_id;
        $row['total_quantity'] = $total_quantity;
    
        $result['data'] = $row;
    
        return $result;
    }

    // get the max package quantity
    public function get_conditions_package_quantity_array($user_id, $type, $debug = false){
        $total_quantity = 0;

        $member_info = $this->Api_Model->get_rows_info(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", array('active' => 1, 'id' => $user_id));
        $is_voucher = $member_info['is_voucher'];
        $voucher_id = $member_info['voucher_id'];
        $package_id = $member_info['package_id'];
        $company_id = $member_info['company_id'];
        $country_id = $member_info['country_id'];

        $package_id_arr = array();

        $break_away_package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "id", array('company_id' => $company_id, 'active' => 1, $type => 1));
        if(!empty($break_away_package_list)){
            foreach($break_away_package_list as $row_break_away_package){
                $package_id_arr[] = $row_break_away_package['id'];
            }
        }

        if(in_array($package_id, $package_id_arr)){
            $valid = true;
        }else{
            $valid = false;
        }

        if($valid){
            if($is_voucher == 1){
                $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
                $total_quantity = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
            }else{
                $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
                $total_quantity = isset($package_info['id']) ? $package_info['quantity'] : 0;
            }
        }else{
            $total_quantity = 0;
        }

        return $total_quantity;
    }

    public function get_balance_purchase_quantity_total($month, $user_id, $is_paid = 0){
        $total_box = 0;

        $purchase_package_info = $this->Api_Model->get_rows_info(TBL_PURCHASE_PACKAGE, "id, SUM(amount) as total_quantity", array('user_id' => $user_id, 'MONTH(insert_time)' => $month, 'active' => 1, 'is_paid' => $is_paid));
        $total_stock = isset($purchase_package_info['id']) ? $purchase_package_info['total_quantity'] : 0;
        $total_box = $total_stock;

        return $total_box;
    }

    public function check_is_package_max_package($member_package_id, $company_id, $country_id, $type){
        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1, $type => 1));
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        if($member_package_id == $package_id){
            return true;
        }else{
            return false;
        }
    }

    public function get_team_break_away_total_box($is_voucher, $voucher_id, $package_id, $company_id, $country_id, $debug = false){
        $total_box = 0;
        if($is_voucher == 1){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
            $total_stock = isset($voucher_info['id']) ? $voucher_info['total_stock'] : 0;
            $total_box = $total_stock;
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, quantity", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
            $total_stock = isset($package_info['id']) ? $package_info['quantity'] : 0;
            $total_box = $total_stock;
        }

        return $total_box;
    }

    // check until the upline fulfil the requirement
    public function get_fulfil_upline_post($insert_id = 0, $type, $debug = false){
        $user_id = 0;
        $is_fulfil = false;
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $insert_id, 'active' => 1));
        while($is_fulfil === false){
            $company_id = $member['company_id'];
            $country_id = $member['country_id'];
            $package_id = $member['package_id'];

            $package_id_arr = $this->get_conditions_package_id_array($company_id, $type);

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, package_id', array('id' => $member['referral_id'], 'active' => 1));
            $referral_id = isset($referral_info['id']) ? $referral_info['id'] : 0;
            $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;

            if(in_array($referral_package_id, $package_id_arr)){
                $user_id = $referral_id;
                break;
            }else if($referral_id == 0){
                $user_id = 0;
                break;
            }else{
                $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $member['referral_id']));
                $is_fulfil = false;
            }
        }

        return $user_id;
    }

    public function check_is_got_cross_over_comm($referral_id){
        $result['data'] = array();
        $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id", array('to_user_id' => $referral_id, 'type' => "cross_over", 'active' => 1));
        if(isset($wallet_info['id']) && $wallet_info['id'] > 0){
            $status = true;
            $wallet_id = $wallet_info['id'];
        }else{
            $status = false;
            $wallet_id = 0;
        }

        $row['status'] = $status;
        $row['wallet_id'] = $wallet_id;

        $result['data'] = $row;

        return $result;
    }

    // check is upline is max package or not
    public function check_upline_is_max_package($insert_id = 0){
        $member = $this->Api_Model->get_rows_info(TBL_USER, 'id, referral_id, company_id, country_id, package_id', array('id' => $insert_id, 'active' => 1));
        $company_id = isset($member['id']) ? $member['company_id'] : 0;
        $country_id = isset($member['id']) ? $member['country_id'] : 0;

        $max_package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1), "id", "DESC", 1);
        $package_id = isset($max_package_info['id']) ? $max_package_info['id'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, 'id, package_id', array('id' => $member['referral_id'], 'active' => 1));
        $referral_package_id = isset($referral_info['id']) ? $referral_info['package_id'] : 0;
            
        if($referral_package_id == $package_id && $referral_package_id != 0 && $package_id != 0){
            return true;
        }else{
            return false;
        }
    }

    public function get_purchase_display_data($referral_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, email", array('id' => $referral_id, 'active' => 1));
        $data = isset($user_info['id']) ? $user_info['username'] : $user_info['email'];
        return $data;
    }

    public function get_referral_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, referral_id", array('id' => $user_id, 'active' => 1));
        $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;
        return $referral_id;
    }

    public function get_company_type($company_id){
        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, type", array('id' => $company_id, 'active' => 1));
        $company_type = isset($company_info['id']) ? $company_info['type'] : "";
        return $company_type;
    }

    public function get_company_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
        return $company_id;
    }

    public function give_cash_wallet_comm($referral_id, $user_id, $type, $description, $amount, $is_drb = 0, $qty = 0){
        $company_id = $this->get_company_id($referral_id);
        $total_balance = $this->check_wallet_balance_post($type, $referral_id, $is_drb);
        $new_balance = $total_balance + $amount;

        $data_wallet_insert = array(
            'type' => $type,
            'qty' => $qty,
            'company_id' => $company_id,
            'from_user_id' => $user_id,
            'to_user_id' => $referral_id,
            'description' => $description,
            'credit' => $amount,
            'balance' => $new_balance,
        );

        $wallet_id = $this->Api_Model->insert_data(TBL_WALLET, $data_wallet_insert);
        return $wallet_id;
    }

    public function give_cb_point($user_id, $description, $amount, $package_id = 0, $order_id = 0){
        $company_id = $this->get_company_id($user_id);
        $total_balance = $this->check_cb_point_balance_post($user_id);
        $new_balance = $total_balance + $amount;

        $data_point_insert = array(
            'company_id' => $company_id,
            'user_id' => $user_id,
            'package_id' => $package_id,
            'order_id' => $order_id,
            'description' => $description,
            'credit' => $amount,
            'balance' => $new_balance,
        );

        $this->Api_Model->insert_data(TBL_CB_POINT, $data_point_insert);
    }

    public function check_cb_point_balance_post($user_id){
        $point_balance = $this->Api_Model->get_rows_info(TBL_CB_POINT, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($point_balance['total_credit']) ? $point_balance['total_credit'] : 0;
        $total_debit = isset($point_balance['total_debit']) ? $point_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }

    public function update_become_smart_partner($is_available_smart_partner, $is_already_become_smart_partner, $user_id){
        if($is_available_smart_partner && $is_already_become_smart_partner === false){
            $data_active_smart_partner_update = array(
                'is_smart_partner' => 1
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_active_smart_partner_update);
        }
    }

    public function check_is_smart_partner_available($referral_id, $debug = false){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, COUNT(*) as total_smart_partner", array('is_smart_partner' => 1, 'active' => 1));
        $total_smart_partner = isset($user_info['id']) ? $user_info['total_smart_partner'] : 0;

        $user_company_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $referral_id, 'active' => 1));
        $company_id = isset($user_company_info['id']) ? $user_company_info['company_id'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
        $first_smart_partner = $company_info['first_smart_partner'];

        if($total_smart_partner == $first_smart_partner || $total_smart_partner > $first_smart_partner){
            return false;
        }else{
            return true;
        }
    }

    // get the max package id
    public function get_conditions_package_id_array($company_id, $type){
        $package_id = array();

        $break_away_package_list = $this->Api_Model->get_rows(TBL_PACKAGE, "id", array('company_id' => $company_id, 'active' => 1, $type => 1));
        if(!empty($break_away_package_list)){
            foreach($break_away_package_list as $row_break_away_package){
                $package_id[] = $row_break_away_package['id'];
            }
        }

        return $package_id;
    }

    public function proceed_pv_post($user_id, $company_id, $purchase_id, $total_pv, $type, $debug = false){
        if($total_pv != "0.00"){
            $pv_balance = $this->check_pv_balance_post($user_id);
            if($type == "debit"){
                $new_balance = $pv_balance - $total_pv;
            }else{
                $new_balance = $pv_balance + $total_pv;
            }

            $data_pv = array(
                'user_id' => $user_id,
                'company_id' => $company_id,
                'package_id' => $purchase_id,
                'description' => "Repeat Order/Upgrade",
                'balance' => $new_balance
            );

            if($type == "debit"){
                $data_pv['debit'] = $total_pv;
            }else{
                $data_pv['credit'] = $total_pv;
            }
            $this->Api_Model->insert_data(TBL_PV, $data_pv);
        }
    }

    public function update_member_pv_post($available_pv_balance, $total_pv, $user_id, $type, $is_self = false){
        if($total_pv != "0.00"){
            if(!$is_self){
                if($type == "debit"){
                    $new_pv_balance = $available_pv_balance - $total_pv;
                }else{
                    $new_pv_balance = $available_pv_balance + $total_pv;
                }
            }else{
                $new_pv_balance = $available_pv_balance;
            }

            $data_user_update = array(
                'total_pv' => $new_pv_balance
            );
            $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
        }
    }

    public function proceed_stock_post($user_id, $company_id, $purchase_id, $total_quantity, $type, $debug = false){
        $stock_balance = $this->check_stock_balance_post($user_id);
        if($type == "debit"){
            $new_balance = $stock_balance - $total_quantity;
        }else{
            $new_balance = $stock_balance + $total_quantity;
        }

        $data_stock = array(
            'user_id' => $user_id,
            'company_id' => $company_id,
            'package_id' => $purchase_id,
            'description' => "Repeat Order/Upgrade",
            'balance' => $new_balance
        );

        if($type == "debit"){
            $data_stock['debit'] = $total_quantity;
        }else{
            $data_stock['credit'] = $total_quantity;
        }
        $this->Api_Model->insert_data(TBL_STOCK, $data_stock);
    }

    public function update_member_stock_post($available_stock_balance, $total_quantity, $user_id, $type, $is_self = false){
        $company_id = $this->get_company_id($user_id);

        if(!$is_self){
            if($type == "debit"){
                $new_stock_balance = $available_stock_balance - $total_quantity;
            }else{
                $new_stock_balance = $available_stock_balance + $total_quantity;
            }
        }else{
            $new_stock_balance = $available_stock_balance;
        }

        $data_user_update = array(
            'total_stock' => $new_stock_balance
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
    }

    public function proceed_topup_post($company_id, $order_id, $total_quantity){
        $topup_balance = $this->check_stock_balance_post($company_id);
        $new_topup_balance = $topup_balance - $total_quantity;
        $data_topup = array(
            'company_id' => $company_id,
            'order_id' => $order_id,
            'description' => "Order Shipment",
            'debit' => $total_quantity,
            'balance' => $new_topup_balance
        );

        $this->Api_Model->insert_data(TBL_COMPANY_TOPUP, $data_topup);

        $data_company_update = array(
            'total_topup' => $new_topup_balance
        );
        $this->Api_Model->update_data(TBL_COMPANY, array('id' => $company_id, 'active' => 1), $data_company_update);
    }

    public function proceed_point_post($user_id, $company_id, $purchase_id, $total_point, $type, $debug = false){
        $point_balance = $this->check_point_balance_post($user_id);
        if($type == "debit"){
            $new_balance = $point_balance - $total_point;
        }else{
            $new_balance = $point_balance + $total_point;
        }

        $data_point = array(
            'user_id' => $user_id,
            'company_id' => $company_id,
            'package_id' => $purchase_id,
            'description' => "Repeat Order/Upgrade",
            'balance' => $new_balance
        );

        if($type == "debit"){
            $data_point['debit'] = $total_point;
        }else{
            $data_point['credit'] = $total_point;
        }
        $this->Api_Model->insert_data(TBL_POINT, $data_point);
    }

    public function update_member_point_post($available_point_balance, $total_point, $user_id, $type, $is_self = false){
        if(!$is_self){
            if($type == "debit"){
                $new_point_balance = $available_point_balance - $total_point;
            }else{
                $new_point_balance = $available_point_balance + $total_point;
            }
        }else{
            $new_point_balance = $available_point_balance;
        }

        $data_user_update = array(
            'total_point' => $new_point_balance
        );
        $this->Api_Model->update_data(TBL_USER, array('id' => $user_id, 'active' => 1), $data_user_update);
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

    public function check_wallet_balance_post($user_id, $is_released = false){
        if($is_released){
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'is_released' => 1));
        }else{
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id));
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

    public function check_pv_balance_post($user_id){
        $pv_balance = $this->Api_Model->get_rows_info(TBL_PV, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'user_id' => $user_id));
        $total_credit = isset($pv_balance['total_credit']) ? $pv_balance['total_credit'] : 0;
        $total_debit = isset($pv_balance['total_debit']) ? $pv_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
    }
}
