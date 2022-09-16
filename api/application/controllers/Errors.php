<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends Base_Controller
{
    public $_api_code = '1068';

    public function __construct()
	{
        parent::__construct();
    }

    public function index_get(){
        $output = array(
            'status' => $this->lang->line("text_rest_invalid_credentials")
        );
        $this->response($output, REST_Controller::HTTP_OK);
    }
}