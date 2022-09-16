<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'vendor/autoload.php';

class Login extends Base_Controller {
    public $_api_code = '0000';
	public function __construct() {
        parent::__construct();
    }

    public function index(){
        $this->load->view("login");
    }

    public function login_post(){
        $username = isset($this->request_data['username']) ? $this->request_data['username'] : "";
        $password = isset($this->request_data['password']) ? $this->request_data['password'] : "";

        if($username == ""){
            $this->session->set_flashdata('error', "Username is empty !");
            redirect(base_url() . "Login", "refresh");
        }else if($password == ""){
            $this->session->set_flashdata('error', "Password is empty !");
            redirect(base_url() . "Login", "refresh");
        }else{
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('username' => $username, 'active' => 1));

            if(isset($user_info['id']) && $user_info['id'] > 0){
                $user_password = $user_info['password'];
                $user_group = $user_info['group_id'];

                $group_info = $this->Api_Model->get_rows_info("vny_group", "id, is_shipment", array('id' => $user_group, 'active' => 1));
                $is_shipment = isset($group_info['id']) ? $group_info['is_shipment'] : 0;

                if($user_info['user_type'] == "ADMIN"){
                    if($this->verify_password($username, $password, $user_password) === true){
                        if($is_shipment == 1){
                            redirect(base_url() . "Order/shipment", "refresh");
                        }else if($group_info['id'] == 7){
                            redirect(base_url() . "Product", "refresh");
                        }else{
                            redirect(base_url() . "Member", "refresh");
                        }
                    }else{
                        $this->session->set_flashdata('error', "Invalid Password !");
                        redirect(base_url() . "Login", "refresh");
                    }
                }else{
                    $this->session->set_flashdata('error', "Invalid Brand Account !");
                    redirect(base_url() . "Login", "refresh");
                }
            }else{
                $this->session->set_flashdata('error', "Invalid Username !");
                redirect(base_url() . "Login", "refresh");
            }
        }
    }

    public function verify_password($username, $password, $user_password){
        $is_correct = false;

        if(password_verify($password, $user_password)){
            $access_token = md5($username.date('YmdHis'));
    
            $data = array(
                'access_token' => $access_token
            );
            $this->Api_Model->update_data(TBL_USER, array('username' => $username), $data);

            $user_data = $this->Api_Model->get_rows_info(TBL_USER, "*", array('username' => $username, 'active' => 1));

            $this->session->set_userdata(
                array(
                    'user_id' => $user_data['id'],
                    'email' => $user_data['email'],                          
                    'is_user_login' => true,
                    'group_id' => $user_data['group_id'],
                    'access_token' => $user_data['access_token'],
                    'user_type' => $user_data['user_type']
                )
            );

            $is_correct = true;
        }else{
            $is_correct = false;
        }

        return $is_correct;
    }

    public function logout()
	{
        $array_items = array('user_id', 'email', 'is_user_login', 'group_id', 'access_token', 'user_type');
		$this->session->unset_userdata($array_items);
		redirect(base_url() . "Login", "refresh");
	}

    public function generate_daily_summary_report()
    {
		$this->ali();
		
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $today_date = date("Y-m-d");
        // $today_date = date("2021-10-29");
        $summary_date = date("dmY");
        // $summary_date = date("29102021");
        $this->page_data['summary_shipment_list'] = $this->Api_Model->get_all_sql(TBL_ORDER, "*", "WHERE DATE(shipped_at) = '$today_date' AND active = '1' AND status = 'APPROVE' AND order_status = 'SHIPPED' AND company_id = '2'");
        $this->page_data['summary_order_list'] = $this->Api_Model->get_all_sql(TBL_ORDER, "*", "WHERE DATE(insert_time) = '$today_date' AND active = '1' AND status = 'APPROVE' AND company_id = '2'");
        $html = $this->load->view('main/daily_summary_report',$this->page_data,true);
        $mpdf->WriteHTML($html);
        // $mpdf->Output();
        $mpdf->Output("../img/dato_report/summary" . $summary_date . '.pdf','F');
        $this->send_daily_summary_report($summary_date);
		
		//echo $summary_date;
        redirect(base_url() . "Login", "refresh");
    }
	
	public function ali()
	{
		$mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $today_date = date("Y-m-d");
        // $today_date = date("2021-10-29");
        $summary_date = date("dmY").'_';
        // $summary_date = date("29102021");
        $this->page_data['summary_shipment_list'] = $this->Api_Model->get_all_sql(TBL_ORDER, "*", "WHERE DATE(shipped_at) = '$today_date' AND active = '1' AND status = 'APPROVE' AND order_status = 'SHIPPED' AND company_id = '12'");
        $this->page_data['summary_order_list'] = $this->Api_Model->get_all_sql(TBL_ORDER, "*", "WHERE DATE(insert_time) = '$today_date' AND active = '1' AND status = 'APPROVE' AND company_id = '12'");
        $html = $this->load->view('main/daily_summary_report2',$this->page_data,true);
        $mpdf->WriteHTML($html);
        // $mpdf->Output();
        $mpdf->Output("../img/dato_report/summary" . $summary_date . '.pdf','F');
        $this->send_daily_summary_report($summary_date);
		
		//echo $summary_date;
	}

	public function gmmsr()
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $today_date = date("Y-m-d");
        // $today_date = date("2021-10-29");
        $summary_date = date("dmY");
        // $summary_date = date("29102021");
        $user_list =  $this->page_data['summary_shipment_list'] = $this->Api_Model->get_datatables_list(TBL_ORDER, "month(insert_time) as Month, sum(total_quantity)", array('company_id' => 12),"","month(insert_time)");
        foreach ($user_list as $row) {

            //$ausername = $row['user_id'];
           // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
            $username = $row['Month'];
            $phone_no = $row['sum(total_quantity)'];

            $result['data'][] = $row;
            
        }
        
      //echo $result['data'];
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
        
        $html = $this->load->view('main/daily_summary_report',$this->page_data,true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
        $mpdf->Output("../img/dato_report/summary" . $summary_date . '.pdf','F');
       // $this->send_daily_summary_report($summary_date);
       // redirect(base_url() . "Login", "refresh");
    }
	
    public function send_daily_summary_report($date){
        $this->load->config('email');
        $this->load->library('email');

        $attachment_path = "";
        $attachment_path = DISPLAY_PATH . "img/dato_report/summary" . $date . '.pdf';

        // $this->email->clear(TRUE);
        $this->email->from('no-reply@ainra.co', "Ainra");
        $email_list = array("billionlai7@gmail.com","alex.kong2@gmail.com","alijan.rahujo143@gmail.com");
        // $email_list = array("billionlai7@gmail.com", "danielgoh649@gmail.com", "junghao2017@gmail.com");
        $this->email->to($email_list);
        $this->email->subject("Daily Summary Report");
        $this->email->message("Summary Report of " . $date);
        $this->email->attach($attachment_path);
        $this->email->send();
    }
	
	 public function send_daily_summary_report2($date){
        $this->load->config('email');
        $this->load->library('email');

        $attachment_path = "";
        $attachment_path = DISPLAY_PATH . "img/dato_report/summary" . $date . '.pdf';

        // $this->email->clear(TRUE);
        $this->email->from('no-reply@ainra.co', "Ainra");
        $email_list = array("tankikan@gmail.com");
        // $email_list = array("billionlai7@gmail.com", "danielgoh649@gmail.com", "junghao2017@gmail.com");
        $this->email->to($email_list);
        $this->email->subject("Daily Summary Report");
        $this->email->message("Summary Report of " . $date);
        $this->email->attach($attachment_path);
        $this->email->send();
    }

    public function generate_daily_order_report()
    {
        $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp', 'mode' => '+aCJK', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $this->page_data['product_list'] = $this->Api_Model->get_rows(TBL_PRODUCT, "*", array('active' => 1, 'is_active' => 1, 'company_id' => 2));
        $html = $this->load->view('main/daily_order_report',$this->page_data,true);
        $mpdf->WriteHTML($html);
        // $mpdf->Output();
        $mpdf->Output("../img/dato_report/order" . date("dmY") . '.pdf','F');
        $this->send_daily_order_report(date("dmY"));
        redirect(base_url() . "Login", "refresh");
    }

    public function send_daily_order_report($date){
        $this->load->config('email');
        print_r($this->load->library('email'));
		
		
		
		exit;
		
        $attachment_path = "";
        $attachment_path = DISPLAY_PATH . "img/dato_report/order" . $date . '.pdf';

        // $this->email->clear(TRUE);
        $this->email->from('no-reply@ainra.co', "Ainra");
        $email_list = array("billionlai7@gmail.com","alex.kong2@gmail.com");
        $this->email->to($email_list);
        $this->email->subject("Daily Order Report");
        $this->email->message("Order Report of " . $date);
        $this->email->attach($attachment_path);
        $this->email->send();
    }

    // public function update_stock_record(){
    //     $stock_list = $this->Api_Model->get_rows(TBL_STOCK,"*",array('company_id' => 0, 'active' => 1));
    //     foreach($stock_list as $row_stock){
    //         $user_id = $row_stock['user_id'];
    //         $stock_id = $row_stock['id'];

    //         $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
    //         $user_company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

    //         $data_update = array(
    //             'company_id' => $user_company_id
    //         );
    //         $this->Api_Model->update_data(TBL_STOCK, array('id' => $stock_id), $data_update);
    //     }
    // }

    public function msvenus_drb_cron(){
        $this->calculate_drb_bonus(1);

        $this->load->view("output/success_response");
    }

    public function calculate_drb_bonus($company_id){
        $is_record_exist = 0;

        $company_info = $this->Api_Model->get_rows_info(TBL_COMPANY, "id, drb_bonus", array('id' => $company_id));
        $drb_bonus = isset($company_info['id']) ? $company_info['drb_bonus'] : 0;

        if($drb_bonus != 0){
            $package_id_arr = $this->get_conditions_package_id_array($company_id, "drb");
            $max_package_id = implode("','", $package_id_arr);
            $member_list = $this->Api_Model->get_all_sql(TBL_USER, "id, is_voucher, voucher_id, package_id, company_id, country_id", "WHERE active = '1' AND company_id = '$company_id' AND package_id IN ('" . $max_package_id . "')");

            if(!empty($member_list)){
                foreach($member_list as $row_member){
                    // $total_quantity = $this->get_team_break_away_total_box($row_member['is_voucher'], $row_member['voucher_id'], $row_member['package_id'], $row_member['company_id'], $row_member['country_id']);
                    $total_price = $this->get_selected_package_price($row_member['is_voucher'], $row_member['voucher_id'], $row_member['package_id'], $row_member['company_id'], $row_member['country_id']);
                    $grand_total = $total_price;
                    // echo $grand_total; die;
                    $bonus = $grand_total * 0.001;

                    $drb_info = $this->Api_Model->get_rows_info(TBL_DRB_REPORT, "id", array('day' => date("d"), 'month' => date("m"), 'year' => date("Y"), 'user_id' => $row_member['id'], 'active' => 1));
                    $is_record_exist = isset($drb_info['id']) ? 1 : 0;

                    $data_drb = array(
                        'day' => date("d"),
                        'month' => date("m"),
                        'year' => date("Y"),
                        'company_id' => $company_id,
                        'user_id' => $row_member['id'],
                        'price' => $total_price,
                        'description' => "Daily Rebate of " . date("d") . "-" . date("m") . "-" . date("Y"),
                        'bonus' => $bonus
                    );
                    if($is_record_exist == 0 && $row_member['id'] == 8){
                        $this->Api_Model->insert_data(TBL_DRB_REPORT, $data_drb);

                        $drb_remark = "Daily Rebate Bonus";
                        $this->give_cash_wallet_comm($row_member['id'], $drb_remark, $bonus, "drb");
                    }
                }
            }
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

    public function get_selected_package_price($is_voucher, $voucher_id, $package_id, $company_id, $country_id, $debug = false){
        $total_box = 0;
        if($is_voucher == 1){
            $voucher_info = $this->Api_Model->get_rows_info(TBL_BIG_PRESENT, "*", array('id' => $voucher_id));
            $total_price = isset($voucher_info['id']) ? $voucher_info['price'] : 0;
        }else{
            $package_info = $this->Api_Model->get_rows_info(TBL_PACKAGE, "id, grand_total", array('id' => $package_id, 'company_id' => $company_id, 'country_id' => $country_id, 'active' => 1));
            $total_price = isset($package_info['id']) ? $package_info['grand_total'] : 0;
        }

        return $total_price;
    }

    public function give_cash_wallet_comm($user_id, $description, $amount, $type){
        $company_id = $this->get_company_id($user_id);
        $total_balance = $this->check_wallet_balance_post($type, $user_id, 1);
        $new_balance = $total_balance + $amount;

        $data_wallet_insert = array(
            'type' => $type,
            'company_id' => $company_id,
            'to_user_id' => $user_id,
            'description' => $description,
            'credit' => $amount,
            'balance' => $new_balance,
        );

        $wallet_info = $this->Api_Model->get_rows_info(TBL_WALLET, "id", array('description' => "Daily Rebate Bonus", 'to_user_id' => $user_id, 'type' => "drb"));
        $exist_drb_wallet_id = isset($wallet_info['id']) ? $wallet_info['id'] : 0;
        $is_exist_drb_data = isset($wallet_info['id']) ? 1 : 0;
        
        if($is_exist_drb_data == 1){
            $this->Api_Model->update_data(TBL_WALLET, array('id' => $exist_drb_wallet_id), array('credit' => $new_balance, 'balance' => $new_balance));
        }else{
            $this->Api_Model->insert_data(TBL_WALLET, $data_wallet_insert);
        }
    }

    public function get_company_id($user_id){
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;
        return $company_id;
    }

    public function check_wallet_balance_post($type, $user_id, $is_drb = 0){
        if($is_drb == 1){
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type' => "drb"));
        }else{
            $wallet_balance = $this->Api_Model->get_rows_info(TBL_WALLET, 'SUM(credit) as total_credit, SUM(debit) as total_debit', array('active' => 1, 'to_user_id' => $user_id, 'type !=' => "drb"));
        }
        $total_credit = isset($wallet_balance['total_credit']) ? $wallet_balance['total_credit'] : 0;
        $total_debit = isset($wallet_balance['total_debit']) ? $wallet_balance['total_debit'] : 0;
        $total_balance = $total_credit - $total_debit;
        return $total_balance;
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
	
	 public function update_promotions(){

        $company_id = 1; //isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        
        //  $aphone_no = "";isset($this->request_data['aphone_no']) ? $this->request_data['aphone_no'] : "";
          $today = date("Y-m-d");
          $stock_quantity = 2;
          $start = 0;
          $count = 10;
          $where_query = array('id' > 0, 'active' => 1, 'DATE(end_date) <' => $today);
          $where_group_like_query = "";
          $where_group_or_like_query = "";
          $order_query = "";
  
          $promotions_list[] = array();
          $promotions_list = $this->Api_Model->get_datatables_list("vny_promotions", "*",$where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
          
          
          foreach ($promotions_list as $row) {
  
              //$ausername = $row['user_id'];
              // $uid = $this->Api_Model->get_rows_info(TBL_USER, "username", array('id' => $row['user_id'], 'active' => 1));
              $package_id = $row['package_id'];
              $cp = $row['current_price'];
         
             // $username = "";$this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
             
             
              $data_user_update = array(
                      'grand_total' => $cp
                  );
              $this->Api_Model->update_data(TBL_PACKAGE, array('id' => $package_id, 'active' => 1), $data_user_update);             
          }
          $this->load->view("output/success_response");
       
    }
	
}
