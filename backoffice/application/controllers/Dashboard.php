<?php
class Dashboard extends Base_Controller {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/dashboard");
    }
}
?>