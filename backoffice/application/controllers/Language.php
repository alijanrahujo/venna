<?php
class Language extends Base_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index(){
        redirect("Errors", "refresh");
    }

    public function translate_language($language = ""){
        $language = ($language != "") ? $language : "english";
        $this->session->set_userdata('site_lang', $language);
        redirect(site_url() . "Company", "refresh");
    }
}
?>