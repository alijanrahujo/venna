<?php
require_once APPPATH.'vendor/autoload.php';
use RevenueMonster\SDK\RevenueMonster;
use RevenueMonster\SDK\Exceptions\ApiException;
use RevenueMonster\SDK\Exceptions\ValidationException;
use RevenueMonster\SDK\Request\WebPayment;
use RevenueMonster\SDK\Request\QRPay;
use RevenueMonster\SDK\Request\QuickPay;

class Normal_Api extends Base_Controller {
    public $_api_code = '3068';
    public $_exclude_api_code = '3838';

    protected $_sms_api_key = "6fe02ca8bfa206c7455bd265092ae543";
    protected $_sms_api_email = "scanpay4u@gmail.com";

    public function __construct(){
        parent::__construct();
    }

    public function index_get(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function index_post(){
        $this->response(array("status" => $this->lang->line("text_rest_invalid_api_key")), 200);
    }

    public function get_slider_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $referral_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        $slider_list = $this->Api_Model->get_rows(TBL_SLIDER, "*", array('type' => "HOME", 'active' => 1, 'company_id' => $company_id), "", "", "sequence", "ASC");

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
    }

    public function get_category_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $referral_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_id = $user_info['company_id'];
        }else{
            $company_id = 0;
        }

        $category_list = $this->Api_Model->get_rows(TBL_CATEGORY, "*", array('active' => 1, 'company_id' => $company_id));

        $result = $this->success_response($category_list);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_company_info_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, bank_name, account_name, account_no", array('id' => $referral_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, private_key_file", array('id' => $user_info['company_id'], 'active' => 1));
            $payment_gateway_file = isset($company_info['id']) ? $company_info['private_key_file'] : "";

            $referral_bank_name = isset($user_info['bank_name']) ? $user_info['bank_name'] : "";
            $referral_account_name = isset($user_info['account_name']) ? $user_info['account_name'] : "";
            $referral_account_no = isset($user_info['account_no']) ? $user_info['account_no'] : "";

            if($payment_gateway_file == "" || $payment_gateway_file == NULL){
                $is_active_payment_gateway = 0;
            }else{
                $is_active_payment_gateway = 1;
            }

            $data = array(
                'is_active_payment_gateway' => $is_active_payment_gateway,
                'bank_name' => $referral_bank_name,
                'account_name' => $referral_account_name,
                'account_no' => $referral_account_no
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Data !");
            $this->response($result, 200);
        }
    }

    public function get_color_theme_post(){
        $subdomain = isset($this->request_data['subdomain']) ? $this->request_data['subdomain'] : 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id", array('subdomain' => $subdomain, 'active' => 1));
        if(isset($company_info['id']) && $company_info['id'] > 0){
            $company_id = isset($company_info['id']) ? $company_info['id'] : 0;

            $color_theme_info = $this->Api_Model->get_rows_info(TBL_COLOR_THEME, "*", array('company_id' => $company_id));
            if(isset($color_theme_info['id']) && $color_theme_info['id'] > 0){
                $color_theme_info['brand_logo'] = DISPLAY_PATH . "img/theme/" . $company_id . "/" . $color_theme_info['brand_logo'];
                $color_theme_info['login_bg'] = DISPLAY_PATH . "img/theme/" . $company_id . "/" . $color_theme_info['login_bg'];
                $color_theme_info['main_bg'] = DISPLAY_PATH . "img/theme/" . $company_id . "/" . $color_theme_info['main_bg'];
                $color_theme_info['header_image'] = DISPLAY_PATH . "img/theme/" . $company_id . "/" . $color_theme_info['header_image'];
        
                $json_response = array(
                    'setting' => $color_theme_info
                );
        
                $result = $this->success_response($json_response);
                $this->response($result, REST_Controller::HTTP_OK);
            }else{
                $result = $this->error_response("Invalid Color Theme !");
                $this->response($result, 200);
            }
        }else{
            $result = $this->error_response("Invalid Theme !");
            $this->response($result, 200);
        }
    }

    public function payment_post($amount, $phone, $name, $email, $company_id, $is_sandbox){
        $title = "Sangri-La";
        $details = "Paying to Sangri-La";
        $amount = isset($amount) ? $amount : 1;
        $order_id = date("Ymdhis") . rand(10,1000);
        $phone = isset($this->request_data['phone']) ? $this->request_data['phone'] : "";
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $email = isset($this->request_data['email']) ? $this->request_data['email'] : "";
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "live";
        $revenue_amount = intval($amount * 100);
        if($revenue_amount == 204){
            $revenue_amount = 205;
        }

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, store_id, client_id, client_secret, private_key_file", array('id' => $company_id, 'active' => 1));
        $store_id = isset($company_info['id']) ? $company_info['store_id'] : "1622005674872298025";
        $client_id = isset($company_info['id']) ? $company_info['client_id'] : "1621942353650703102";
        $client_secret = isset($company_info['id']) ? $company_info['client_secret'] : "fMjXTRaxogEfVNuZkdihtUahwYUXnSjs";
        $private_key_file = isset($company_info['id']) ? $company_info['private_key_file'] : "venna_private_key.pem";
        if($is_sandbox === true){
            $isSandbox = true;
        }else{
            $isSandbox = false;
        }

        // $store_id = "1622610523615394976";
        // $client_id = "1622610698544378585";
        // $client_secret = "PPUyKpVosRXUXmHBcJqJnoyawMCNutxb";
        // $private_key_file = "ordo_private_key.pem";
        
        $callback_url = DISPLAY_PATH . "api/v1/Normal_Api/response_payment";
        $redirect_url = DISPLAY_PATH . "api/v1/Normal_Api/response_payment";

        // Initialise sdk instance
        $rm = new RevenueMonster([
            'clientId' => $client_id,
            'clientSecret' => $client_secret,
            'privateKey' => file_get_contents(APPPATH.'vendor/revenuemonster/sdk/tests/' . $private_key_file),
            'version' => 'stable',
            // 'isSandbox' => true,
            'isSandbox' => $isSandbox,
        ]);

        //create Web payment
        try {
            $wp = new WebPayment;
            $wp->order->id = $order_id;
            $wp->order->title = $title;
            $wp->order->currencyType = 'MYR';
            $wp->order->amount = $revenue_amount;
            $wp->order->detail = $details;
            $wp->order->additionalData = '';
            $wp->storeId = $store_id;
            $wp->redirectUrl = $redirect_url;
            $wp->notifyUrl = $callback_url;
            $wp->layoutVersion = 'v3';
          
            $response = $rm->payment->createWebPayment($wp);
            // echo $response->checkoutId;
            $json_response = array(
                'order_id' => $order_id,
                'return_url' => $response->url
            );

            $data_callback = array(
                'order_id' => $order_id,
                'transaction_id' => $response->checkoutId,
                'transaction_total' => $amount,
                'type' => $type
            );
    
            $this->Api_Model->insert_data(TBL_TRANSACTION_LOG, $data_callback);

            $result = $this->success_response($json_response);
            $this->response($result, REST_Controller::HTTP_OK);
        } catch(ApiException $e) {
            $result = $this->error_response("statusCode : {$e->getCode()}, errorCode : {$e->getErrorCode()}, errorMessage : {$e->getMessage()}");
            $this->response($result, 401);
        } catch(ValidationException $e) {
            $result = $this->error_response($e->getMessage());
            $this->response($result, 401);
        } catch(Exception $e) {
            $result = $this->error_response($e->getMessage());
            $this->response($result, 401);
        }
    }

    public function check_payment_get(){
        if(isset($_GET['status']) || isset($_GET['orderId'])){
            $transaction_log_info = $this->Api_Model->get_rows_info(TBL_TRANSACTION_LOG, "*", array('order_id' => $_GET['orderId']));
            if(isset($transaction_log_info['id']) && $transaction_log_info['id'] > 0){
                if($transaction_log_info['transaction_status'] == 1){
                    $data = array(
                        'status' => "Success"
                    );
                }else if($transaction_log_info['transaction_status'] == 2){
                    $data = array(
                        'status' => "Failed"
                    );
                }else{
                    $data = array(
                        'status' => "Pending"
                    );
                }
            }else{
                $data = array(
                    'status' => "Failed"
                );
            }
        }else{
            $data = array(
                'status' => "Failed"
            );
        }
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function response_payment_get(){
        if(isset($_GET['status']) || isset($_GET['orderId'])){
            $transaction_log_info = $this->Api_Model->get_rows_info(TBL_TRANSACTION_LOG, "*", array('order_id' => $_GET['orderId']));
            if(isset($transaction_log_info['id']) && $transaction_log_info['id'] > 0){
                if($_GET['status'] == "CANCELLED"){
                    $data_update = array(
                        'transaction_status' => 2
                    );
                    $this->Api_Model->update_data(TBL_TRANSACTION_LOG, array('order_id' => $_GET['orderId']), $data_update);

                    $data = array(
                        'status' => "Failed"
                    );
                }else{
                    $data_update = array(
                        'transaction_status' => 1
                    );
                    $this->Api_Model->update_data(TBL_TRANSACTION_LOG, array('order_id' => $_GET['orderId']), $data_update);

                    $transaction_log_info = $this->Api_Model->get_rows_info(TBL_TRANSACTION_LOG, "*", array('order_id' => $_GET['orderId']));
                    if($transaction_log_info['transaction_status'] == 1){
                        $data = array(
                            'status' => "Success"
                        );
                    }else if($transaction_log_info['transaction_status'] == 2){
                        $data = array(
                            'status' => "Failed"
                        );
                    }else{
                        $data = array(
                            'status' => "Pending"
                        );
                    }
                }
            }else{
                $data = array(
                    'status' => "Failed"
                );
            }
        }else{
            $data = array(
                'status' => "Failed"
            );
        }
        
        $result = $this->success_response($data);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function get_product_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, country_id", array('id' => $referral_id, 'active' => 1));
        if(isset($referral_info['id']) && $referral_info['id'] > 0){
            $package_id = $referral_info['package_id'];
            $company_id = $referral_info['company_id'];
            $country_id = $referral_info['country_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $product_list = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $company_id, 'active' => 1, 'is_active' => 1), "", "", "is_new", "DESC");
            if(!empty($product_list)){
                foreach($product_list as $plkey => $plval){
                    $product_list[$plkey]['image'] = DISPLAY_PATH . "img/product/" . $plval['image'];
                    if($company_type != "FIXED"){
                        $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('company_id' => $company_id, 'product_id' => $plval['id'], 'currency_id' => $country_id, 'active' => 1));
                        $global_price = isset($global_price_info['id']) ? $global_price_info['price'] : "0.00";
                        $product_list[$plkey]['price'] = $global_price;
                    }else{
                        $product_list[$plkey]['price'] = $plval['price'];
                    }

                    $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "id, code", array('id' => $country_id, 'active' => 1));
                    $currency_name = isset($country_info['id']) ? $country_info['code'] : "";
                    $product_list[$plkey]['currency_name'] = $currency_name;

                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('product_id' => $plval['id'], 'tmp_user_id' => $tmp_user_id, 'active' => 1));
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
            $result = $this->error_response("No agent detected. Contact agent to resend order link.");
            $this->response($result, 200);
        }
    }

    public function select_product_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, country_id", array('id' => $referral_id, 'active' => 1));
        if(isset($referral_info['id']) && $referral_info['id'] > 0){
            $country_id = $referral_info['country_id'];
            $company_id = $referral_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $is_minimum_purchase = isset($company_info['id']) ? $company_info['is_minimum_purchase'] : 0;

            if($company_type == "FIXED"){
                $account_balance = $this->check_stock_balance_post($referral_id);
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
                $account_balance = $this->check_point_balance_post($referral_id);
                $error_message = "Insufficient Point !";
                if($account_balance == 0 || $account_balance == "0.00"){
                    $is_able_to_continue = false;
                }else if($account_balance == $quantity){
                    $is_able_to_continue = true;
                }else if($account_balance < $quantity){
                    $is_able_to_continue = false;
                }else{
                    $is_able_to_continue = true;
                }
            }
            
            if(!$is_able_to_continue){
                $result = $this->error_response($error_message);
                $this->response($result, 200);
            }else{
                // check is first item
                $cart_remind_info = $this->Api_Model->get_rows_info(TBL_CART_REMIND, "id, COUNT(*) as total_data", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
                $total_data = isset($cart_remind_info['id']) ? $cart_remind_info['total_data'] : 0;

                if($total_data == 0 && $is_minimum_purchase != 0){
                    $data_cart_remind = array('tmp_user_id' => $tmp_user_id);
                    $this->Api_Model->insert_data(TBL_CART_REMIND, $data_cart_remind);

                    $result = $this->error_response("Minimum order quantity is 10 !");
                    $this->response($result, 200);
                }else{
                    $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $product_id, 'active' => 1));
                    if(isset($product_info['id']) && $product_info['id'] > 0){
                        if($company_type != "FIXED"){
                            $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('company_id' => $company_id, 'currency_id' => $country_id, 'product_id' => $product_info['id'], 'active' => 1));
                            if(isset($global_price_info['id']) && $global_price_info['id'] > 0){
                                $product_price = $global_price_info['price'];
                                $product_pv_price = isset($global_price_info['id']) ? $global_price_info['pv_price'] : "0.00";
                            }else{
                                $product_price = "0.00";
                                $product_pv_price = "0.00";
                            }
                        }else{
                            $product_price = $product_info['price'];
                        }
                        $product_info['price'] = $product_price;
                        $product_subtotal = $product_price * $quantity;

                        // check available product in cart
                        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'product_id' => $product_info['id'], 'active' => 1, 'is_clear' => 0));
                        if(isset($cart_info['id']) && $cart_info['id'] > 0){
                            $cart_id = $cart_info['id'];
                            $cart_quantity = $cart_info['quantity'];
                            $new_quantity = $cart_quantity + $quantity;
                            $product_subtotal = $product_price * $new_quantity;
                            $data_cart_update = array(
                                'referral_id' => $referral_id,
                                // 'active' => 0,
                                'price' => $product_price,
                                'quantity' => $new_quantity
                            );
                            if($company_type != "FIXED"){
                                $data_cart_update['pv_price'] = $product_pv_price;
                                $data_cart_update['subtotal'] = $product_price;
                                $data_cart_update['pv_subtotal'] = $product_pv_price;
                            }else{
                                $data_cart_update['subtotal'] = $product_subtotal;
                            }
                            $this->Api_Model->update_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1), $data_cart_update);
                            // $this->Api_Model->update_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0), $data_cart_update);
                        }else{
                            $exist_cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'product_id' => $product_info['id'], 'active' => 0, 'is_clear' => 0));
                            if(isset($exist_cart_info['id']) && $exist_cart_info['id'] > 0){
                                $cart_id = $exist_cart_info['id'];
                                $data_cart_update = array(
                                    'referral_id' => $referral_id,
                                    'active' => 1,
                                    'price' => $product_price,
                                    'quantity' => $quantity
                                );
                                if($company_type != "FIXED"){
                                    $data_cart_update['pv_price'] = $product_pv_price;
                                    $data_cart_update['subtotal'] = $product_price;
                                    $data_cart_update['pv_subtotal'] = $product_pv_price;
                                }else{
                                    $data_cart_update['subtotal'] = $product_subtotal;
                                }
                                $this->Api_Model->update_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 0, 'is_clear' => 0), $data_cart_update);
                            }else{
                                $data_cart = array(
                                    'rndcode' => $product_info['rndcode'],
                                    'tmp_user_id' => $tmp_user_id,
                                    'referral_id' => $referral_id,
                                    'product_id' => $product_info['id'],
                                    'product_name' => $product_info['name'],
                                    'price' => $product_price,
                                    'quantity' => $quantity,
                                    'image' => $product_info['image']
                                );
                                if($company_type != "FIXED"){
                                    $data_cart['pv_price'] = $product_pv_price;
                                    $data_cart['subtotal'] = $product_price;
                                    $data_cart['pv_subtotal'] = $product_pv_price;
                                }else{
                                    $data_cart['subtotal'] = $product_subtotal;
                                }
                                $this->Api_Model->insert_data(TBL_CART, $data_cart);
                            }
                        }

                        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('product_id' => $product_id, 'tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
                        $is_add_to_cart = isset($cart_info['id']) ? 1 : 0;
                        $cart_id = isset($cart_info['id']) ? $cart_info['id'] : 0;

                        // get cart item
                        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
                        if(!empty($cart_list)){
                            foreach($cart_list as $clkey => $clval){
                                $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
                            }
                        }else{
                            $cart_list = array();
                        }

                        $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
                        $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                        $data = array(
                            'id' => $cart_id,
                            'product_id' => $product_id,
                            'is_add_to_cart' => $is_add_to_cart,
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
            $result = $this->error_response("No agent detected. Contact agent to resend order link.");
            $this->response($result, 200);
        }
    }

    public function get_cart_post(){
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;

        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
        if(!empty($cart_list)){
            foreach($cart_list as $clkey => $clval){
                $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
            }
        }else{
            $cart_list = array();
        }

        $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
        $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
 
        $result = $this->success_response($cart_list);
        $this->response($result, REST_Controller::HTTP_OK);
    }

    public function update_cart_quantity_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $cart_id = isset($this->request_data['cart_id']) ? $this->request_data['cart_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $is_minus = isset($this->request_data['is_minus']) ? $this->request_data['is_minus'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id", array('id' => $referral_id, 'active' => 1));
        if(isset($referral_info['id']) && $referral_info['id'] > 0){
            $package_id = $referral_info['package_id'];
            $company_id = $referral_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            if($quantity == 0){
                $result = $this->error_response("Empty Quantity !");
                $this->response($result, 200);
            }else{
                $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'company_id' => $company_id));
                $purchase_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                $free_quantity = isset($promotion_info['id']) ? $promotion_info['free_quantity'] : 0;
                $total_purchase_quantity = $purchase_quantity + $free_quantity;
                $new_quantity = $purchase_quantity - $free_quantity;

                if($quantity > $purchase_quantity && $quantity < $total_purchase_quantity){
                    $check_quantity = $new_quantity;
                }else if($quantity == $total_purchase_quantity){
                    $check_quantity = $purchase_quantity;
                }else{
                    $check_quantity = $quantity;
                }

                $stock_balance = $this->check_stock_balance_post($referral_id);
                $point_balance = $this->check_point_balance_post($referral_id);

                if($company_type == "FIXED"){
                    if($check_quantity > $stock_balance){
                        $result = $this->error_response("Insufficient Stock !");
                        $this->response($result, 200);
                    }else{
                        $this->proceed_update_cart_post($tmp_user_id, $cart_id, $quantity, $company_type, 0);
                    }
                }else{
                    $cart_balance = $this->check_cart_subtotal_balance_post($referral_id);

                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0));
                    $product_id = isset($cart_info['id']) ? $cart_info['product_id'] : 0;

                    $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('company_id' => $company_id, 'product_id' => $product_id, 'active' => 1));
                    $product_price = isset($global_price_info['id']) ? $global_price_info['price'] : "0.00";

                    if($is_minus == 1){
                        $cart_balance = $cart_balance - $product_price;
                    }else{
                        $cart_balance = $cart_balance + $product_price;
                    }

                    if($cart_balance == $point_balance){
                        $this->proceed_update_cart_post($tmp_user_id, $cart_id, $quantity, $company_type, $is_minus);
                    }else{
                        if($cart_balance > $point_balance){
                            $result = $this->error_response("Insufficient Point !");
                            $this->response($result, 200);
                        }else{
                            $this->proceed_update_cart_post($tmp_user_id, $cart_id, $quantity, $company_type, $is_minus);
                        }
                    }

                    // $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "price", array('id' => $cart_id, 'tmp_user_id' => $tmp_user_id, 'active' => 1));
                    // $subtotal_cart_price = $cart_info['price'] * $quantity;
                    // if($subtotal_cart_price > $point_balance){
                    //     $result = $this->error_response("Insufficient Point !");
                    //     $this->response($result, 200);
                    // }else{
                    //     $this->proceed_update_cart_post($tmp_user_id, $cart_id, $quantity, $company_type);
                    // }
                }
            }
        }else{
            $result = $this->error_response("No agent detected. Contact agent to resend order link.");
            $this->response($result, 200);
        }
    }

    public function proceed_update_cart_post($tmp_user_id, $cart_id, $quantity, $company_type = "", $is_minus = 0){
        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0));
        if(isset($cart_info['id']) && $cart_info['id'] > 0){
            $referral_id = $cart_info['referral_id'];
            if($company_type != "FIXED"){
                $product_id = $cart_info['product_id'];

                $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('active' => 1, 'id' => $referral_id));
                $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

                $global_price_info = $this->Api_Model->get_rows_info(TBL_GLOBAL_PRICE, "*", array('company_id' => $company_id, 'product_id' => $product_id, 'active' => 1));
                $product_price = isset($global_price_info['id']) ? $global_price_info['price'] : "0.00";
                $pv_price = isset($global_price_info['id']) ? $global_price_info['pv_price'] : "0.00";
                $pv_subtotal = $pv_price * $quantity;
            }

            $is_restock = $cart_info['is_restock'];
            $product_price = $cart_info['price'];
            $subtotal = $product_price * $quantity;

            $data_cart_update = array(
                'quantity' => $quantity
            );

            if($company_type == "FIXED"){
                $data_cart_update['subtotal'] = $subtotal;
            }

            if($company_type != "FIXED"){
                $data_cart_update['subtotal'] = $subtotal;
                $data_cart_update['pv_price'] = $pv_price;
                $data_cart_update['pv_subtotal'] = $pv_subtotal;
            }

            $this->Api_Model->update_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0), $data_cart_update);

            // get cart item
            $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'id' => $cart_id));
            $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
            $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

            $data = array(
                'id' => $cart_id,
                'subtotal' => $cart_info['subtotal'],
                'quantity' => $cart_info['quantity'],
                'cart_quantity' => $total_cart_quantity
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Cart !");
            $this->response($result, 200);
        }
    }

    public function delete_cart_post(){
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $cart_id = isset($this->request_data['cart_id']) ? $this->request_data['cart_id'] : 0;

        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, product_id", array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1));
        if(isset($cart_info['id']) && $cart_info['id'] > 0){
            $product_id = $cart_info['product_id'];
            $data_cart_update = array(
                'active' => 0
            );
            $this->Api_Model->update_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'id' => $cart_id, 'active' => 1), $data_cart_update);

            // get cart item
            $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
            if(!empty($cart_list)){
                foreach($cart_list as $clkey => $clval){
                    $cart_list[$clkey]['image'] = DISPLAY_PATH . "img/product/" . $clval['image'];
                }
            }else{
                $cart_list = array();
            }

            $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
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

    public function get_shipping_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $is_got_price = isset($this->request_data['is_got_price']) ? $this->request_data['is_got_price'] : 0;

        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, country_id", array('id' => $referral_id, 'active' => 1));
        if(isset($referral_info['id']) && $referral_info['id'] > 0){
            $country_id = $referral_info['country_id'];
            $company_id = $referral_info['company_id'];

            if($company_id != 2 && $company_id != 11 && $company_id != 12){
                $delivery_fee_list = $this->Api_Model->get_rows(TBL_DELIVERY_FEE, "*", array('company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
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
            $result = $this->error_response("No agent detected. Contact agent to resend order link.");
            $this->response($result, 200);
        }
    }

    public function get_order_subtotal_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $shipping_id = isset($this->request_data['shipping_id']) ? $this->request_data['shipping_id'] : 0;
        $payment_type = isset($this->request_data['payment_type']) ? $this->request_data['payment_type'] : "";
        $promo_code = isset($this->request_data['promo_code']) ? $this->request_data['promo_code'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";

        if($phone_no == "" && $promo_code != ""){
            $result = $this->error_response("Recipent Phone is empty !");
            $this->response($result, 200);
        }else{
            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $referral_id, 'active' => 1));
            if(isset($referral_info['id']) && $referral_info['id'] > 0){
                $company_id = $referral_info['company_id'];
                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                $is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;

                $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(subtotal) as total_price, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
                $total_price = isset($cart_info['id']) ? $cart_info['total_price'] : "0.00";
                $total_quantity = isset($cart_info['id']) ? $cart_info['total_quantity'] : 0;

                $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'company_id' => $company_id));
                // $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'name' => $promo_code), "id", "DESC", 1);
                $promotion_id = isset($promotion_info['id']) ? $promotion_info['id'] : 0;
                $unit_price = isset($promotion_info['id']) ? $promotion_info['unit_price'] : "0.00";
                $promotion_price = isset($promotion_info['id']) ? $promotion_info['promotion_price'] : "0.00";
                $purchase_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                $free_quantity = isset($promotion_info['id']) ? $promotion_info['free_quantity'] : 0;

                $promotion_log_info = $this->Api_Model->get_info_sql(TBL_PROMOTION_LOG, "id", "WHERE promotion_id = '$promotion_id' AND active = '1' AND phone_no LIKE '%".$phone_no."%'");

                if((isset($promotion_log_info['id']) && $promotion_log_info['id'] > 0) && $phone_no != ""){
                    $result = $this->error_response("Promotion already been used !");
                    $this->response($result, 200);
                }else{
                    if($promotion_id != 0 && $total_quantity >= $purchase_quantity){
                        $grand_free_quantity = $total_quantity / $purchase_quantity;
                        $total_sum_quantity = $purchase_quantity + $free_quantity;
                        if($grand_free_quantity >= 1 && $total_quantity >= $total_sum_quantity){
                            $grand_free_quantity = 1;

                            $promotion_balance_quantity = $total_quantity - $free_quantity;
                            $balance_quantity_price = $promotion_balance_quantity * $unit_price;
                            $total_price = $balance_quantity_price;
                        }
                    }

                    $shipping_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price, end, is_multiply", array('id' => $shipping_id, 'active' => 1));
                    $end_amount = isset($shipping_info['id']) ? $shipping_info['end'] : "0.00";
                    $is_multiply = isset($shipping_info['id']) ? $shipping_info['is_multiply'] : 0;
                    if($is_delivery_fee == 1 && $is_multiply == 1){
                        $selected_shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                        $shipping_fee = $selected_shipping_fee * $total_quantity;
                    }else{
                        $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                    }

                    if($company_id == 12 && $total_price < 160){
                        $final_price = $total_price + 6.50;
                        $shipping_fee = 6.50;
                    }else{
                        $final_price = $total_price + $shipping_fee;
                    }
                    if($payment_type == "BANK_IN"){
                        $service_fee = "0.00";
                        $grand_total_price = $final_price;
                    }else{
                        $service_fee = $final_price * 0.02;
                        $grand_total_price = $final_price + ($final_price * 0.02);
                    }

                    $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'is_clear' => 0));
                    $total_cart_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;

                    $data = array(
                        'grand_total' => number_format($total_price, 2),
                        'delivery_fee' => number_format($shipping_fee, 2),
                        'service_fee' => number_format($service_fee, 2),
                        'subtotal' => number_format($grand_total_price, 2),
                        'cart_quantity' => $total_cart_quantity
                    );

                    $result = $this->success_response($data);
                    $this->response($result, REST_Controller::HTTP_OK);
                }
            }else{
                $result = $this->error_response("No agent detected. Contact agent to resend order link.");
                $this->response($result, 200);
            }
        }
    }

    public function place_order_post(){
        $referral_id = isset($this->request_data['referral_id']) ? $this->request_data['referral_id'] : 0;
        $tmp_user_id = isset($this->request_data['tmp_user_id']) ? $this->request_data['tmp_user_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $phone_no = isset($this->request_data['phone_no']) ? $this->request_data['phone_no'] : "";
        $address = isset($this->request_data['address']) ? $this->request_data['address'] : "";
        $city = isset($this->request_data['city']) ? $this->request_data['city'] : "";
        $state = isset($this->request_data['state']) ? $this->request_data['state'] : "";
        $postcode = isset($this->request_data['postcode']) ? $this->request_data['postcode'] : "";
        $shipping_id = isset($this->request_data['shipping_id']) ? $this->request_data['shipping_id'] : 0;
        $remark = isset($this->request_data['remark']) ? $this->request_data['remark'] : "";
        $is_payment_check = isset($this->request_data['is_payment_check']) ? $this->request_data['is_payment_check'] : 0;
        $tracking_password = isset($this->request_data['tracking_password']) ? $this->request_data['tracking_password'] : "";
        $payment_type = isset($this->request_data['payment_type']) ? $this->request_data['payment_type'] : "0";
        $promo_code = isset($this->request_data['promo_code']) ? $this->request_data['promo_code'] : "";
        $is_sandbox = isset($this->request_data['is_sandbox']) ? $this->request_data['is_sandbox'] : false;

        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
        if(!empty($cart_list)){
            if($name == ""){
                $result = $this->error_response("Shipping Name is empty !");
                $this->response($result, 200);
            }else if($phone_no == ""){
                $result = $this->error_response("Shipping Contact is empty !");
                $this->response($result, 200);
            }else if($address == ""){
                $result = $this->error_response("Shipping Address is empty !");
                $this->response($result, 200);
            }else if($city == ""){
                $result = $this->error_response("Shipping City is empty !");
                $this->response($result, 200);
            }else if($state == ""){
                $result = $this->error_response("Shipping State is empty !");
                $this->response($result, 200);
            }else if($postcode == ""){
                $result = $this->error_response("Shipping Postcode is empty !");
                $this->response($result, 200);
            }else if($shipping_id == 0){
                $result = $this->error_response("Invalid Shipping Method !");
                $this->response($result, 200);
            }else if($shipping_id == 3){
                $result = $this->error_response("Sangri-La is temporarily suspending international delivery service to Singapore. Sangri-La 暂时停止对新加坡的国际送货服务。");
                $this->response($result, 200);
            }else{
                if($payment_type == "" || $payment_type == "0"){
                    $result = $this->error_response("Invalid Payment Method !");
                    $this->response($result, 200);
                }else{
                    if($payment_type == "BANK_IN"){
                        if (!empty($_FILES['Image']['name']))
                        {
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

                                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, country_id, company_id", array('id' => $referral_id, 'active' => 1));
                                $country_id = isset($referral_info['id']) ? $referral_info['country_id'] : 0;
                                $company_id = isset($referral_info['id']) ? $referral_info['company_id'] : 0;

                                $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $country_id, 'active' => 1));
                                $country_name = isset($country_info['id']) ? $country_info['name'] : "";

                                $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                                $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                                $is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;
                                $is_minimum_purchase = isset($company_info['id']) ? $company_info['is_minimum_purchase'] : 0;

                                $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity, SUM(subtotal) as total_price", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
                                $total_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
                                $total_price = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_price'] : 0;

                                // $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'name' => $promo_code), "id", "DESC", 1);
                                $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'company_id' => $company_id));
                                $promotion_id = isset($promotion_info['id']) ? $promotion_info['id'] : 0;
                                $unit_price = isset($promotion_info['id']) ? $promotion_info['unit_price'] : "0.00";
                                $promotion_price = isset($promotion_info['id']) ? $promotion_info['promotion_price'] : "0.00";
                                $purchase_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                                $free_quantity = isset($promotion_info['id']) ? $promotion_info['free_quantity'] : 0;

                                $total_purchase_quantity = $purchase_quantity + $free_quantity;
                                $new_quantity = $purchase_quantity - $free_quantity;

                                /* Customized Free Quantity
                                if($promotion_id != 0 && $total_quantity >= $purchase_quantity){
                                    $grand_free_quantity = $total_quantity / $purchase_quantity;
                                    if($grand_free_quantity >= 1){
                                        $total_free_quantity = bcdiv($grand_free_quantity, 1, 0) * $free_quantity;
                                        $grand_total_quantity = ($purchase_quantity * bcdiv($grand_free_quantity, 1, 0)) + ($free_quantity * bcdiv($grand_free_quantity, 1, 0));

                                        if($total_quantity == $grand_total_quantity){
                                            $total_price = $promotion_price * bcdiv($grand_free_quantity, 1, 0);
                                        }else{
                                            // 10*1 + 2*1 = 12
                                            $combine_free_quantity = ($purchase_quantity * bcdiv($grand_free_quantity, 1, 0)) + ($free_quantity * bcdiv($grand_free_quantity, 1, 0));
                                            if($total_quantity < $combine_free_quantity){
                                                $available_free_quantity = bcdiv($grand_free_quantity - 1, 1, 0);
                                                $combine_free_quantity = ($purchase_quantity * $available_free_quantity) + ($free_quantity * $available_free_quantity);
                                                $promotion_balance_quantity = $total_quantity - $combine_free_quantity;

                                                $promotion_price = $available_free_quantity * $promotion_price;
                                            }else{
                                                $promotion_balance_quantity = $total_quantity - $combine_free_quantity;
                                                $promotion_price = bcdiv($grand_free_quantity, 1, 0) * $promotion_price;
                                            }
                                            
                                            // 2*14.90=29.80
                                            $balance_quantity_price = $promotion_balance_quantity * $unit_price;
                                            $total_price = $balance_quantity_price + $promotion_price;
                                        }
                                    }
                                }
                                */
                                $is_promotion = 0;
                                $promotion_log_info = $this->Api_Model->get_info_sql(TBL_PROMOTION_LOG, "id", "WHERE promotion_id = '$promotion_id' AND active = '1' AND phone_no LIKE '%".$phone_no."%'");
                                $is_phone_no_exist = isset($promotion_log_info['id']) ? 1 : 0;
                                if($is_phone_no_exist == 1){
                                    $is_promotion = 0;
                                }else{
                                    $is_promotion = 1;
                                }
                                    
                                if($purchase_quantity != 0 && $is_promotion != 0 && $total_quantity >= ($purchase_quantity + $free_quantity)){
                                    $grand_free_quantity = $total_quantity / $purchase_quantity;
                                    $total_sum_quantity = $purchase_quantity + $free_quantity;
                                    if($grand_free_quantity >= 1 && $total_quantity >= $total_sum_quantity){
                                        $grand_free_quantity = 1;
                    
                                        $promotion_balance_quantity = $total_quantity - $free_quantity;
                                        $balance_quantity_price = $promotion_balance_quantity * $unit_price;
                                        $total_price = $balance_quantity_price;
                                        $is_promotion = 1;
                                    }
                                }

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

                                if($is_continue){
                                    if($total_quantity < 10 && $is_minimum_purchase != 0){
                                        $result = $this->error_response("Minimum order quantity is " . $is_minimum_purchase . " !");
                                        $this->response($result, 200);
                                    }else{
                                        if($is_payment_check == 1){
                                            $this->payment_post($actual_price, $phone_no, $name, "", $company_id, $is_sandbox);
                                        }else{
                                            $stock_balance = $this->check_stock_balance_post($referral_id);
                                            $point_balance = $this->check_point_balance_post($referral_id);

                                            if($total_quantity > $purchase_quantity && $total_quantity < $total_purchase_quantity){
                                                $check_quantity = $new_quantity;
                                            }else if($total_quantity == $total_purchase_quantity){
                                                $check_quantity = $purchase_quantity;
                                            }else{
                                                $check_quantity = $total_quantity;
                                            }

                                            if(($check_quantity > $stock_balance) && $company_type == "FIXED"){
                                                $result = $this->error_response("Insufficient Stock !");
                                                $this->response($result, 200);
                                            }else if(($actual_price > $point_balance) && $company_type == "FLAT"){
                                                $result = $this->error_response("Insufficient Point !");
                                                $this->response($result, 200);
                                            }else{
                                                if($check_quantity == $stock_balance || $actual_price == $point_balance){
                                                    $this->proceed_order_post($referral_id, $tmp_user_id, $name, $phone_no, $address, $city, $state, $postcode, $country_name, $remark, $total_quantity, $total_price, $company_id, $shipping_id, $tracking_password, $payment_image, $promotion_id, $is_promotion, $payment_type);
                                                }else{
                                                    if(($actual_price < $point_balance  && $company_type == "FLAT") || ($total_quantity < $stock_balance  && $company_type == "FIXED")){
                                                        $this->proceed_order_post($referral_id, $tmp_user_id, $name, $phone_no, $address, $city, $state, $postcode, $country_name, $remark, $total_quantity, $total_price, $company_id, $shipping_id, $tracking_password, $payment_image, $promotion_id, $is_promotion, $payment_type);
                                                    }else{
                                                        $result = $this->error_response("Invalid Process !");
                                                        $this->response($result, 200);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    $result = $this->error_response("Insufficient quantity for selected delivery fee !");
                                    $this->response($result, 200);
                                }
                            }
                            else
                            {
                                $result = $this->error_response($this->upload->display_errors());
                                $this->response($result, 200);
                            }
                        }else{
                            $result = $this->error_response("Empty Attachment !");
                            $this->response($result, 200);
                        }
                    }else{
                        $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, country_id, company_id", array('id' => $referral_id, 'active' => 1));
                        $country_id = isset($referral_info['id']) ? $referral_info['country_id'] : 0;
                        $company_id = isset($referral_info['id']) ? $referral_info['company_id'] : 0;

                        $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $country_id, 'active' => 1));
                        $country_name = isset($country_info['id']) ? $country_info['name'] : "";

                        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
                        $company_type = isset($company_info['id']) ? $company_info['type'] : "";
                        $is_delivery_fee = isset($company_info['id']) ? $company_info['is_delivery_fee'] : 0;
                        $is_minimum_purchase = isset($company_info['id']) ? $company_info['is_minimum_purchase'] : 0;

                        $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity, SUM(subtotal) as total_price", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
                        $total_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
                        $total_price = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_price'] : 0;

                        // $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'name' => $promo_code), "id", "DESC", 1);
                        $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'company_id' => $company_id));
                        $promotion_id = isset($promotion_info['id']) ? $promotion_info['id'] : 0;
                        $unit_price = isset($promotion_info['id']) ? $promotion_info['unit_price'] : "0.00";
                        $promotion_price = isset($promotion_info['id']) ? $promotion_info['promotion_price'] : "0.00";
                        $purchase_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                        $free_quantity = isset($promotion_info['id']) ? $promotion_info['free_quantity'] : 0;

                        $total_purchase_quantity = $purchase_quantity + $free_quantity;
                        $new_quantity = $purchase_quantity - $free_quantity;

                        $is_promotion = 0;
                        $promotion_log_info = $this->Api_Model->get_info_sql(TBL_PROMOTION_LOG, "id", "WHERE promotion_id = '$promotion_id' AND active = '1' AND phone_no LIKE '%".$phone_no."%'");
                        $is_phone_no_exist = isset($promotion_log_info['id']) ? 1 : 0;
                        if($is_phone_no_exist == 1){
                            $is_promotion = 0;
                        }else{
                            $is_promotion = 1;
                        }

                        if($is_promotion != 0 && $total_quantity >= ($purchase_quantity + $free_quantity)){
                            $grand_free_quantity = $total_quantity / $purchase_quantity;
                            $total_sum_quantity = $purchase_quantity + $free_quantity;
                            if($grand_free_quantity >= 1 && $total_quantity >= $total_sum_quantity){
                                $grand_free_quantity = 1;
            
                                $promotion_balance_quantity = $total_quantity - $free_quantity;
                                $balance_quantity_price = $promotion_balance_quantity * $unit_price;
                                $total_price = $balance_quantity_price;
                                $is_promotion = 1;
                            }
                        }

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
                        $actual_price = $total_price + $shipping_fee;

                        $is_continue = false;
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

                        if($is_continue){
                            $processing_fee = ($total_price + $shipping_fee) * 0.02;
                            $actual_price = $total_price + $shipping_fee + $processing_fee;
                            $actual_price = number_format($actual_price, 2);

                            if($total_quantity < 10 && $is_minimum_purchase != 0){
                                $result = $this->error_response("Minimum order quantity is " . $is_minimum_purchase . " !");
                                $this->response($result, 200);
                            }else{
                                if($is_payment_check == 1){
                                    $this->payment_post($actual_price, $phone_no, $name, "", $company_id, $is_sandbox);
                                }else{
                                    $stock_balance = $this->check_stock_balance_post($referral_id);
                                    $point_balance = $this->check_point_balance_post($referral_id);

                                    if($total_quantity > $purchase_quantity && $total_quantity < $total_purchase_quantity){
                                        $check_quantity = $new_quantity;
                                    }else if($total_quantity == $total_purchase_quantity){
                                        $check_quantity = $purchase_quantity;
                                    }else{
                                        $check_quantity = $total_quantity;
                                    }

                                    if(($check_quantity > $stock_balance) && $company_type == "FIXED"){
                                        $result = $this->error_response("Insufficient Stock !");
                                        $this->response($result, 200);
                                    }else if(($actual_price > $point_balance) && $company_type == "FLAT"){
                                        $result = $this->error_response("Insufficient Point !");
                                        $this->response($result, 200);
                                    }else{
                                        if($check_quantity == $stock_balance || $actual_price == $point_balance){
                                            $this->proceed_order_post($referral_id, $tmp_user_id, $name, $phone_no, $address, $city, $state, $postcode, $country_name, $remark, $total_quantity, $total_price, $company_id, $shipping_id, $tracking_password, "", $promotion_id, $is_promotion, $payment_type);
                                        }else{
                                            if(($actual_price < $point_balance  && $company_type == "FLAT") || ($total_quantity < $stock_balance  && $company_type == "FIXED")){
                                                $this->proceed_order_post($referral_id, $tmp_user_id, $name, $phone_no, $address, $city, $state, $postcode, $country_name, $remark, $total_quantity, $total_price, $company_id, $shipping_id, $tracking_password, "", $promotion_id, $is_promotion, $payment_type);
                                            }else{
                                                $result = $this->error_response("Invalid Process !");
                                                $this->response($result, 200);
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            $result = $this->error_response("Insufficient quantity for selected delivery fee !");
                            $this->response($result, 200);
                        }
                    }
                }
            }
        }else{
            $result = $this->error_response("Empty Cart !");
            $this->response($result, 200);
        }
    }

    public function proceed_order_post($referral_id, $tmp_user_id, $name, $phone_no, $address, $city, $state, $postcode, $country_name, $remark, $total_quantity, $total_price, $company_id, $shipping_id, $tracking_password, $payment_image = "", $promotion_id, $is_promotion = 0, $payment_type = ""){
        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1));
        if(!empty($cart_list)){
            $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('active' => 1));
            if(isset($order_info['id']) && $order_info['id'] > 0){
                $order_id = $order_info['id'] + 1;
            }else{
                $order_id = 1;
            }

            $shipping_info = $this->Api_Model->get_rows_info(TBL_DELIVERY_FEE, "id, price, is_multiply, start, end, type", array('id' => $shipping_id, 'active' => 1));
            $is_multiply = isset($shipping_info['id']) ? $shipping_info['is_multiply'] : 0;
            $start_amount = isset($shipping_info['id']) ? $shipping_info['start'] : "0.00";
            $end_amount = isset($shipping_info['id']) ? $shipping_info['end'] : "0.00";
            $shipping_type = isset($shipping_info['id']) ? $shipping_info['type'] : "";
            if($is_multiply == 1){
                $selected_shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
                $shipping_fee = $selected_shipping_fee * $total_quantity;
            }else{
                $shipping_fee = isset($shipping_info['id']) ? $shipping_info['price'] : "0.00";
            }
            if($company_id == 12 && $total_price < 160){
                $shipping_fee = 0.00;
                $order_subtotal = $total_price + $shipping_fee;
            }else{
                $order_subtotal = $total_price + $shipping_fee;
            }

            if($company_id == 12 && $order_subtotal < 160){
                $shipping_fee = 6.50;
            }

            $data_order = array(
                'type' => "restock",
                'company_id' => $company_id,
                'order_id' => $order_id,
                'referral_id' => $referral_id,
                'tmp_user_id' => $tmp_user_id,
                'shipping_id' => $shipping_id,
                's_name' => $name,
                's_contact' => $phone_no,
                's_address' => $address,
                's_city' => $city,
                's_postcode' => $postcode,
                's_state' => $state,
                's_country' => $country_name,
                's_remark' => $remark,
                'total_quantity' => $total_quantity,
                'delivery_fee' => $shipping_fee,
                'total_price' => $order_subtotal,
                'tracking_password' => $tracking_password
            );
            if($payment_image != ""){
                $data_order['payment_status'] = "PAID";
                $data_order['payment_receipt'] = $payment_image;
            }
            if($payment_type == "PAYMENT_GATEWAY"){
                $processing_fee = ($total_price + $shipping_fee) * 0.02;
                $actual_price = $total_price + $shipping_fee + $processing_fee;
                $actual_price = number_format($actual_price, 2);
                $order_grand_total = $order_subtotal + $processing_fee;
                $data_order['total_price'] = number_format($order_grand_total, 2);
                $data_order['extra_charge'] = number_format($processing_fee, 2);
                $data_order['status'] = "APPROVE";
                $data_order['payment_status'] = "PAID";
            }
            if($promotion_id != 0 && $is_promotion == 1){
                $data_order['promotion_id'] = $promotion_id;
            }
            $primary_order_id = $this->Api_Model->insert_data(TBL_ORDER, $data_order);

            if($payment_type == "PAYMENT_GATEWAY"){
                if($promotion_id != 0 && $is_promotion == 1){
                    $promotion_info = $this->Api_Model->get_rows_info(TBL_PROMOTION, "*", array('active' => 1, 'id' => $promotion_id));
                    $total_quantity = isset($promotion_info['id']) ? $promotion_info['purchase_quantity'] : 0;
                }
                // deduct referral stock
                $total_balance = $this->check_stock_balance_post($referral_id);
                $new_balance = $total_balance - $total_quantity;

                $data_stock = array(
                    'company_id' => $company_id,
                    'user_id' => $referral_id,
                    'order_id' => $primary_order_id,
                    'description' => "Retail Order",
                    'debit' => $total_quantity,
                    'balance' => $new_balance
                );
                $this->Api_Model->insert_data(TBL_STOCK, $data_stock);

                // update total quantity to agent acc
                $data_user_update = array(
                    'total_stock' => $new_balance
                );
                $this->Api_Model->update_data(TBL_USER, array('id' => $referral_id, 'active' => 1), $data_user_update);
            }

            $upline_price = 0;
            $referral_upline_price = 0;
            foreach($cart_list as $row_cart){
                if($company_id == 12){
                    $cart_quantity = $row_cart['quantity'];

                    $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, referral_id", array('id' => $referral_id));
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
                    'tmp_user_id' => $row_cart['tmp_user_id'],
                    'product_id' => $row_cart['product_id'],
                    'product_price' => $row_cart['price'],
                    'pv_price' => $row_cart['pv_price'],
                    'quantity' => $row_cart['quantity'],
                    'subtotal' => $row_cart['subtotal'],
                    'pv_subtotal' => $row_cart['pv_subtotal'],
                );
                if($payment_type == "PAYMENT_GATEWAY"){
                    $data_order_detail['is_approve'] = 1;
                }
                $this->Api_Model->insert_data(TBL_ORDER_DETAIL, $data_order_detail);
            }

            if($company_id == 12){
                $this->Api_Model->update_data(TBL_ORDER, array('id' => $primary_order_id), array('upline_price' => $upline_price, 'referral_upline_price' => $referral_upline_price));
            }

            if($promotion_id != 0 && $is_promotion == 1){
                $data_promotion_log = array(
                    'promotion_id' => $promotion_id,
                    'phone_no' => $phone_no
                );
                $this->Api_Model->insert_data(TBL_PROMOTION_LOG, $data_promotion_log);
            }

            // clear all cart
            $this->Api_Model->update_multiple_data(TBL_CART, array('tmp_user_id' => $tmp_user_id, 'active' => 1), array('active' => 0, 'is_clear' => 1));

            $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, phone_no, fullname", array('id' => $referral_id, 'active' => 1));
            $referral_phone = isset($referral_info['id']) ? $referral_info['phone_no'] : "";
            $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, is_retail_sms", array('id' => $company_id, 'active' => 1));
            $is_retail_sms = isset($company_info['id']) ? $company_info['is_retail_sms'] : 0;

            if($is_retail_sms == 1){
                $this->send_otp_post("6", $phone_no, $tracking_password, $primary_order_id, $referral_phone, $referral_fullname);
            }

            $data_response = array(
                'order_id' => $primary_order_id,
                'tracking_password' => $tracking_password,
                'referral_phone' => $referral_phone
            );

            $result = $this->success_response($data_response);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Empty Cart !");
            $this->response($result, 200);
        }
    }

    public function get_shipment_order_detail_post(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;
        $tracking_password = isset($this->request_data['tracking_password']) ? $this->request_data['tracking_password'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('id' => $order_id, 'active' => 1));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            if($tracking_password != $order_info['tracking_password']){
                $result = $this->error_response("Incorrect Tracking Password !");
                $this->response($result, 200);
            }else{
                $referral_id = $order_info['referral_id'];
                $tmp_user_id = $order_info['tmp_user_id'];
                $order_detail_list = $this->Api_Model->get_rows(TBL_ORDER_DETAIL, "*", array('tmp_user_id' => $tmp_user_id, 'active' => 1, 'order_id' => $order_id));
                if(!empty($order_detail_list)){
                    $payment_receipt = isset($order_info['id']) ? $order_info['payment_receipt'] : "";
                    foreach($order_detail_list as $odkey => $odval){
                        $product_info = $this->Api_Model->get_rows_info(TBL_PRODUCT, "*", array('id' => $odval['product_id'], 'active' => 1));
                        $product_name = isset($product_info['id']) ? $product_info['name'] : "";
                        $product_image = isset($product_info['id']) ? DISPLAY_PATH . "img/product/" . $product_info['image'] : "";
                        $order_detail_list[$odkey]['product_name'] = $product_name;
                        $order_detail_list[$odkey]['product_image'] = $product_image;
                    }
                }else{
                    $order_detail_list = array();
                    $payment_receipt = "";
                }

                $order_payment_receipt = DISPLAY_PATH . "img/order_receipt/" . $payment_receipt;

                $referral_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, phone_no, email", array('id' => $referral_id, 'active' => 1));
                $referral_username = isset($referral_info['id']) ? $referral_info['username'] : "";
                $referral_fullname = isset($referral_info['id']) ? $referral_info['fullname'] : "";
                $referral_phone_no = isset($referral_info['id']) ? $referral_info['phone_no'] : "";
                $referral_email = isset($referral_info['id']) ? $referral_info['email'] : "";

                if($referral_fullname == ""){
                    $referral_name = $referral_username;
                }else{
                    $referral_name = $referral_fullname . " (" . $referral_username . ")";
                }

                $data = array(
                    'referral_name' => $referral_name,
                    'referral_phone_no' => $referral_phone_no,
                    'referral_email' => $referral_email,
                    'order_id' => "#000" . $order_info['id'],
                    'delivery_company' => $order_info['delivery_company'],
                    'tracking_no' => $order_info['tracking_no'],
                    'tracking_url' => $order_info['tracking_url'],
                    'order_status' => $order_info['status'],
                    'payment_status' => $order_info['payment_status'],
                    'payment_receipt' => $order_payment_receipt,
                    'referral_id' => $referral_id,
                    'order_list' => $order_detail_list
                );

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    public function submit_order_receipt_post(){
        $order_id = isset($this->request_data['order_id']) ? $this->request_data['order_id'] : 0;

        $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('active' => 1, 'id' => $order_id));
        if(isset($order_info['id']) && $order_info['id'] > 0){
            if (!empty($_FILES['Image']['name']))
            {
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

            $data_order = array(
                'payment_receipt' => $image,
                'payment_status' => "PAID"
            );
            $this->Api_Model->update_data(TBL_ORDER, array('active' => 1, 'id' => $order_id), $data_order);

            $result = $this->success_response($data_order);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Order !");
            $this->response($result, 200);
        }
    }

    // all is withdraw stock function

    public function get_withdraw_stock_product_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_type = "normal";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, country_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $country_id = $user_info['country_id'];
            $package_id = $user_info['package_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $product_list = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('company_id' => $company_id, 'active' => 1, 'is_active' => 1));
            if(!empty($product_list)){
                foreach($product_list as $plkey => $plval){
                    $product_list[$plkey]['image'] = DISPLAY_PATH . "img/product/" . $plval['image'];
                    if($company_type != "FIXED"){
                        $product_price_info = $this->Api_Model->get_rows_info(TBL_PRODUCT_PRICE, "*", array('company_id' => $company_id, 'product_id' => $plval['id'], 'package_id' => $package_id, 'active' => 1));
                        $product_price = isset($product_price_info['id']) ? $product_price_info['price'] : "0.00";
                        $product_list[$plkey]['price'] = $product_price;
                    }else{
                        $product_list[$plkey]['price'] = "0.00";
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

    public function select_withdraw_stock_product_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $product_id = isset($this->request_data['product_id']) ? $this->request_data['product_id'] : 0;
        $order_type = "normal";
        $quantity = 1;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id, package_id, referral_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $package_id = $user_info['package_id'];
            $referral_id = $user_info['referral_id'];
            $company_id = $user_info['company_id'];
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $is_able_to_continue = false;
            if($order_type == "normal" || $order_type == "restock"){
                if($company_type == "FIXED"){
                    $account_balance = $this->check_stock_balance_post($user_id);
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
                    $account_balance = $this->check_stock_balance_post($user_id);
                    $cart_balance = $this->check_cart_quantity_balance_post($user_id);

                    $error_message = "Insufficient Quantity !";
                    if($account_balance == 0 || $account_balance == "0.00"){
                        $is_able_to_continue = false;
                    }else if($account_balance == $quantity){
                        $is_able_to_continue = true;
                    }else if($account_balance < $quantity){
                        $is_able_to_continue = false;
                    }else{
                        $is_able_to_continue = true;
                    }
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
                    if($company_type != "FIXED"){
                        if($order_type == "normal"){
                            $product_price = "0.00";
                        }else{
                            $product_price = $product_info['price'];
                        }
                    }else{
                        $product_price = "0.00";
                    }
                    $product_info['price'] = $product_price;

                    // check available product in cart
                    $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'product_id' => $product_info['id'], 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
                    if(isset($cart_info['id']) && $cart_info['id'] > 0){
                        $cart_id = $cart_info['id'];
                        $data_cart_update = array(
                            'active' => 0,
                            'price' => $product_price,
                            'quantity' => 1
                        );
                        $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);
                    }else{
                        $exist_cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'product_id' => $product_info['id'], 'active' => 0, 'is_clear' => 0, 'type' => $order_type));
                        if(isset($exist_cart_info['id']) && $exist_cart_info['id'] > 0){
                            $cart_id = $exist_cart_info['id'];
                            $data_cart_update = array(
                                'price' => $product_price,
                                'quantity' => 1,
                                'active' => 1
                            );
                            $data_cart_update['subtotal'] = "0.00";
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
                                'image' => $product_info['image']
                            );
                            if($company_type != "FIXED"){
                                $data_cart['subtotal'] = $product_price;
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

                    $data = array(
                        'id' => $cart_id,
                        'product_id' => $product_id,
                        'is_add_to_cart' => $is_add_to_cart,
                        'cart' => $cart_list
                    );

                    $result = $this->success_response($data);
                    $this->response($result, REST_Controller::HTTP_OK);
                }else{
                    $result = $this->error_response("Invalid Product !");
                    $this->response($result, 200);
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function get_withdraw_stock_cart_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_type = "normal";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
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
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function update_withdraw_stock_cart_quantity_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $cart_id = isset($this->request_data['cart_id']) ? $this->request_data['cart_id'] : 0;
        $quantity = isset($this->request_data['quantity']) ? $this->request_data['quantity'] : 0;
        $order_type = "normal";

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
                $is_able_to_continue = true;

                $stock_balance = $this->check_stock_balance_post($user_id);
                $point_balance = $this->check_point_balance_post($user_id);

                if($company_type == "FIXED"){
                    if($quantity > $stock_balance){
                        $result = $this->error_response("Insufficient Stock !");
                        $this->response($result, 200);
                    }else{
                        $this->proceed_withdraw_stock_update_cart_post($user_id, $cart_id, $quantity, $company_type, $order_type);
                    }
                }else{
                    $this->proceed_withdraw_stock_update_cart_post($user_id, $cart_id, $quantity, $company_type, $order_type);
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function proceed_withdraw_stock_update_cart_post($user_id, $cart_id, $quantity, $company_type = "", $order_type = ""){
        $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type));
        if(isset($cart_info['id']) && $cart_info['id'] > 0){
            $order_type = $cart_info['type'];
            $product_price = $cart_info['price'];
            $subtotal = $product_price * $quantity;

            $data_cart_update = array(
                'quantity' => $quantity,
            );

            $data_cart_update['subtotal'] = "0.00";

            $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);

            $account_balance = $this->check_stock_balance_post($user_id);
            $cart_balance = $this->check_cart_quantity_balance_post($user_id);

            if($cart_balance > $account_balance){
                $deduct_quantity = $quantity - 1;
                $data_cart_update = array(
                    'quantity' => $deduct_quantity,
                );
                $this->Api_Model->update_data(TBL_CART, array('user_id' => $user_id, 'id' => $cart_id, 'active' => 1, 'is_clear' => 0, 'type' => $order_type), $data_cart_update);
                $result = $this->error_response_with_message("Insufficient Quantity !", array('quantity' => $cart_info['quantity']));
                $this->response($result, 200);
            }else{
                // get cart item
                $cart_info = $this->Api_Model->get_rows_info(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'id' => $cart_id, 'type' => $order_type));
                
                $data = array(
                    'id' => $cart_id,
                    'subtotal' => $cart_info['subtotal'],
                    'quantity' => $cart_info['quantity']
                );

                $result = $this->success_response($data);
                $this->response($result, REST_Controller::HTTP_OK);
            }
        }else{
            $result = $this->error_response("Invalid Cart !");
            $this->response($result, 200);
        }
    }

    public function delete_withdraw_stock_cart_post(){
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

            $data = array(
                'product_id' => $product_id,
                'cart_id' => $cart_id,
                'is_add_to_cart' => 0,
                'cart' => $cart_list
            );

            $result = $this->success_response($data);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid Cart !");
            $this->response($result, 200);
        }
    }

    public function withdraw_stock_post(){
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;
        $order_type = "normal";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, email, country_id, company_id, pincode, referral_id", array('id' => $user_id, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $user_pincode = isset($user_info['id']) ? $user_info['pincode'] : "";
            $s_email = isset($user_info['id']) ? $user_info['email'] : "";
            $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
            $referral_id = isset($user_info['id']) ? $user_info['referral_id'] : 0;

            $country_info = $this->Api_Model->get_rows_info(TBL_CURRENCY, "*", array('id' => $user_info['country_id'], 'active' => 1));
            $country_name = isset($country_info['id']) ? $country_info['name'] : "";

            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $company_id, 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";

            $cart_quantity_info = $this->Api_Model->get_rows_info(TBL_CART, "id, SUM(quantity) as total_quantity, SUM(subtotal) as total_price", array('user_id' => $user_id, 'active' => 1, 'type' => "normal"));
            $total_quantity = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_quantity'] : 0;
            $total_price = isset($cart_quantity_info['id']) ? $cart_quantity_info['total_price'] : 0;

            $actual_price = $total_price;

            if($order_type == "normal"){
                $stock_balance = $this->check_stock_balance_post($user_id);
                $point_balance = $this->check_point_balance_post($user_id);
            }else{
                if($order_type == "restock"){
                    $stock_balance = $this->check_stock_balance_post($referral_id);
                    $point_balance = $this->check_point_balance_post($user_id);
                }
            }

            if(($total_quantity > $stock_balance) && $company_type == "FIXED"){
                $result = $this->error_response("Insufficient Stock !");
                $this->response($result, 200);
            }else if(($actual_price > $point_balance) && $company_type == "FLAT"){
                $result = $this->error_response("Insufficient Point !");
                $this->response($result, 200);
            }else{
                if($total_quantity == $stock_balance || $actual_price == $point_balance){
                    $this->proceed_withdraw_stock_post($user_id, $total_quantity, $total_price, $company_id, $order_type, $referral_id);
                }else{
                    if(($actual_price < $point_balance  && $company_type == "FLAT") || ($total_quantity < $stock_balance  && $company_type == "FIXED")){
                        $this->proceed_withdraw_stock_post($user_id, $total_quantity, $total_price, $company_id, $order_type, $referral_id);
                    }else{
                        $result = $this->error_response("Invalid Process !");
                        $this->response($result, 200);
                    }
                }
            }
        }else{
            $result = $this->error_response("Invalid Account !");
            $this->response($result, 200);
        }
    }

    public function proceed_withdraw_stock_post($user_id, $total_quantity, $total_price, $company_id, $order_type, $referral_id = 0){
        $cart_list = $this->Api_Model->get_rows(TBL_CART, "*", array('user_id' => $user_id, 'active' => 1, 'type' => $order_type));
        if(!empty($cart_list)){
            $order_info = $this->Api_Model->get_rows_info(TBL_ORDER, "*", array('active' => 1));
            if(isset($order_info['id']) && $order_info['id'] > 0){
                $order_id = $order_info['id'] + 1;
            }else{
                $order_id = 1;
            }

            $order_subtotal = $total_price;

            // get agent package
            $original_price = 0;
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, package_id, company_id, fullname, phone_no", array('id' => $user_id, 'active' => 1));
            $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "*", array('id' => $user_info['company_id'], 'active' => 1));
            $company_type = isset($company_info['id']) ? $company_info['type'] : "";
            $pickup_fullname = isset($user_info['id']) ? $user_info['fullname'] : "";
            $pickup_phone_no = isset($user_info['id']) ? $user_info['phone_no'] : "";
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

            $data_order = array(
                'type' => $order_type,
                'company_id' => $company_id,
                'order_id' => $order_id,
                'referral_id' => $referral_id,
                'user_id' => $user_id,
                's_name' => $pickup_fullname,
                's_contact' => $pickup_phone_no,
                'total_quantity' => $total_quantity,
                'total_price' => $order_subtotal,
                'original_price' => $original_price,
                'status' => 2,
                'payment_status' => 1
            );
            $primary_order_id = $this->Api_Model->insert_data(TBL_ORDER, $data_order);

            // deduct referral stock
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

            foreach($cart_list as $row_cart){
                $data_order_detail = array(
                    'order_id' => $primary_order_id,
                    'user_id' => $row_cart['user_id'],
                    'product_id' => $row_cart['product_id'],
                    'product_price' => $row_cart['price'],
                    'quantity' => $row_cart['quantity'],
                    'subtotal' => $row_cart['subtotal'],
                    'is_restock' => $row_cart['is_restock'],
                    'is_approve' => 1
                );
                $this->Api_Model->insert_data(TBL_ORDER_DETAIL, $data_order_detail);
            }

            // clear all cart
            $this->Api_Model->update_multiple_data(TBL_CART, array('user_id' => $user_id, 'active' => 1), array('active' => 0, 'is_clear' => 1));

            $result = $this->success_response($data_order);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Empty Cart !");
            $this->response($result, 200);
        }
    }

    public function get_user_information_post(){
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", array('username' => $username, 'active' => 1));
        if(isset($user_info['id']) && $user_info['id'] > 0){
            $result = $this->success_response($user_info['id']);
            $this->response($result, REST_Controller::HTTP_OK);
        }else{
            $result = $this->error_response("Invalid User !");
            $this->response($result, 200);
        }
    }

    public function check_cart_quantity_balance_post($user_id){
        $cart_balance = $this->Api_Model->get_rows_info(TBL_CART, 'id, SUM(quantity) as total_quantity', array('active' => 1, 'user_id' => $user_id));
        $total_balance = isset($cart_balance['id']) ? $cart_balance['total_quantity'] : 0;
        return $total_balance;
    }

    public function check_cart_subtotal_balance_post($referral_id){
        $cart_balance = $this->Api_Model->get_rows_info(TBL_CART, 'id, SUM(subtotal) as total_price', array('active' => 1, 'referral_id' => $referral_id));
        $total_balance = isset($cart_balance['id']) ? $cart_balance['total_price'] : 0;
        return $total_balance;
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

    public function send_otp_post($phone_code = "6", $phone_no = "", $tracking_password, $order_id, $referral_phone, $referral_fullname)
    {
        // initialize data
        $otp_email = $this->_sms_api_email;
        $otp_api_key = $this->_sms_api_key;
        $otp_receipent = $phone_code . $phone_no;
        $otp_unencode_message =  "[Sangrila] Your order is placed ! Your order ID is " . $order_id . " and tracking PIN is " . $tracking_password . " for order tracking. For more details contact " . $referral_phone . " " . $referral_fullname . "";
        $otp_message = urlencode($otp_unencode_message);

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
        $output = json_decode($json,true);
        
        if($output['statusCode'] == "1606"){
            // success
            $status = 1;

            $data_otp_log = array(
                'otp_reference' => $output['sms']['items']['referenceID']
            );
        }else{
            // failed
            $status = 2;
        }

        $current_time = date('Y-m-d H:i:s');
        $valid_time = date('Y-m-d H:i:s', strtotime('+90 seconds',strtotime($current_time)));
        $expiry_time = date('Y-m-d H:i:s', strtotime('+300 seconds',strtotime($current_time)));

        $data_otp_log['phone_no'] = $phone_no;
        $data_otp_log['sms_message'] = $otp_unencode_message;
        $data_otp_log['status'] = $status;
        $data_otp_log['status_code'] = $output['statusCode'];
        $data_otp_log['status_msg'] = $output['statusMsg'];
        $this->Api_Model->insert_data(TBL_OTP_LOGS, $data_otp_log);
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

    /*function get_center_text_position($img_width, $font_size, $font_file, $string) {
        //Grab the width of the text box
        $bounding_box_size = imagettfbbox($font_size, 0, $font_file, $string);
        $text_width = $bounding_box_size[2] - $bounding_box_size[0];

        //Return the position the text should start
        return ceil(($img_width - $text_width) / 2);
    }    

    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);
    
        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    
        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    
        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }*/
}
