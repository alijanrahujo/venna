<?php
class Common extends Base_Controller {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function get_menu_list(){
        $user_id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;
        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "*", array('id' => $user_id));
        $user_category_id = $user_info['group_id'];
        // if($user_category_id == 1){
        //     $menu_list = $this->Common_Model->get_all_menu_list();
        // }else{
            $menu_list = $this->Common_Model->get_menu_list("", $user_category_id);
        // }

        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";

        $this->lang->load('information',$language);

        foreach($menu_list as $mkey => $mlist){
            $menu_list[$mkey]['name'] = $this->lang->line($mlist['name']);

            foreach($menu_list[$mkey]['sub_menu'] as $smkey => $smlist){
                $menu_list[$mkey]['sub_menu'][$smkey]['name'] = $this->lang->line($smlist['name']);
            }
        }

        $data = array(
            'menu_list' => $menu_list
        );

        $data['response_data'] = $data;
        $this->load->view("output/success_response", $data);
    }
}
?>