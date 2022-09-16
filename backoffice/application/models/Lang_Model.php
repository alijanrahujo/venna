<?php
class Lang_Model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        if(!empty($this->session->userdata("site_lang")) || isset($_REQUEST['language'])){
            $this->site_language = isset($_REQUEST['language']) ? $_REQUEST['language'] : "english";
        }else{
            $this->site_language = "english";
        }
    }

    public function replaceLang($key, $replace_language = []){
        $this->lang->load('information',$this->site_language);
        $string = $this->lang->line($key);
        if(count($replace_language) > 0){

            foreach($replace_language as $rkey => $value){
                $string = str_replace($rkey, $value, $string);
            }
        }
        return $string;
    }
}
?>