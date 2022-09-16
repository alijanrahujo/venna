<?php
class Errors extends Base_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index(){
        $this->load->view("error404");
    }
}
?>