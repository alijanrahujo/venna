<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
header('Content-Type: application/json');

require APPPATH . '/libraries/CreatorJwt.php';

//include restapi libraries
require(APPPATH.'/libraries/REST_Controller.php');

class Base_Controller extends REST_Controller {
    protected static $_exclude_token_api = array(
		'1068', '2068', '3068'
    );

    public function __construct()
	{
        parent::__construct();
        $this->objOfJwt = new CreatorJwt();

        $this->load->database();
        $this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('base');
        $this->load->library('session');
        $this->load->library('encryption');
        $this->load->library('upload');
        $this->request_data = $_REQUEST;
		date_default_timezone_set("Asia/Kuala_Lumpur");
        $this->update_time = date('Y-m-d H:i:s');

        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $language = $this->session->userdata('site_lang');
        $this->lang->load('information',$language);


        // check api that no need token checking
        if((!isset($this->_api_code) || !in_array($this->_api_code, static::$_exclude_token_api))){
            $access_token = isset($this->request_data['access_token']) ? $this->request_data['access_token'] : "";
            $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

            $where_access_token = array(
                'access_token' => $access_token,
                'id' => $user_id,
                'active' => 1
            );
            $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id", $where_access_token);
            if(isset($user_info['id']) && $user_info['id'] > 0){
                $this->verify_token();
            }else{
                http_response_code('200');
                echo json_encode(array( "status" => "Failed", "message" => "Invalid Token"));exit;
            }
        }
    }

    /*************Ganerate token this function use**************/

    public function generate_client_token($client_id)
    {
        // $tokenData['uniqueId'] = '11';
        // $tokenData['role'] = 'alamgir';
        $tokenData['timeStamp'] = Date('Y-m-d h:i:s');
        $jwtToken = $this->objOfJwt->GenerateToken($tokenData);
        $data_oauth_client_token = array(
            'user_id' => $client_id,
            'token' => $jwtToken
        );
        $this->Api_Model->insert_data(TBL_OAUTH_TOKEN, $data_oauth_client_token);
        return $jwtToken;
    }
     
    /*************Use for token then fetch the data**************/
         
    public function verify_token()
    {
        $received_token = $this->input->request_headers('Authorization');
        $header_authorization = $received_token['Authorization'];
        if($header_authorization != ""){
            $oauth_token_info = $this->Api_Model->get_rows_info(TBL_OAUTH_TOKEN, 'id, token', array('token' => $received_token['Authorization']));
            if(isset($oauth_token_info['id']) && $oauth_token_info['id'] > 0){
                try
                {
                    $jwtData = $this->objOfJwt->DecodeToken($received_token['Authorization']);
                }
                catch (Exception $e)
                    {
                    http_response_code('200');
                    echo json_encode(array( "status" => "Failed", "message" => $e->getMessage()));exit;
                }
            }else{
                http_response_code('200');
                echo json_encode(array( "status" => "Failed", "message" => "Invalid Auth Token"));exit;
            }
        }else{
            http_response_code('200');
            echo json_encode(array( "status" => "Failed", "message" => "Unauthorized"));exit;
        }
    }

    public function success_response($data){
        $response = array('status' => "Success");
        if (isset($data)) {
            $response['data'] = $data;
        }
        return $response;
        exit;
    }

    public function success_response_with_message($data, $message = ""){
        $response = array('status' => "Success", 'message' => $message);
        if (isset($data)) {
            $response['data'] = $data;
        }
        return $response;
        exit;
    }

    public function error_response($message){
        $response = array('status' => "Failed");
        if (isset($message)) {
            $response['message'] = $message;
        }
        return $response;
        exit;
    }
	
	public function error_response_with_message($message, $extra_param){
        $response = array('status' => "Failed");
        if (isset($message)) {
            $response['message'] = $message;
            $response['extra_param'] = $extra_param;
        }
        return $response;
        exit;
    }
}