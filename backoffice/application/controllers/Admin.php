<?php
class Admin extends Base_Controller {
    public function __construct() {
        parent::__construct(__CLASS__);
        $this->load->model("Admin_Model");
    }

    public function index(){
		$this->load(ADMIN_URL . '/group_list');
    }

    public function withdraw(){
		$this->load(ADMIN_URL . '/withdraw_list');
    }
    
    public function awithdraw(){
		$this->load(ADMIN_URL . '/withdrawal_approved_list');
    }

    public function ticket(){
		$this->load(ADMIN_URL . '/ticket_list');
    }
    
    public function reply(){
        //reply
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_TICKET, "*", array('active' => 1, 'id' => $id));
        // $gallery_attachment = $this->Api_Model->get_rows(TBL_GALLERY_ATTACHMENT, "*", array('active' => 1, 'gallery_id' => $id), "", "", "sequence", "ASC");
        $this->page_data['edit'] = $edit_info;
        // $this->page_data['attachment'] = $gallery_attachment;
        $this->check_is_fake_data($edit_info, $this->page_data, "reply_ticket", "Admin/ticket");
    }

    public function get_group(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
		$start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
		$count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
		$search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : '';
		$order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
		$language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";

        $result = array();
        $result['draw'] = $draw;
        $result['recordsTotal'] = $this->Admin_Model->count_group_list();
        $search_query = "";
        if ($search != "") {
        	$search_query = "WHERE id != '1' AND active = '1' AND (name LIKE '%".$search."%')";
        	$result['recordsFiltered'] = $this->Admin_Model->count_group_list($search_query);
        } else {
			$result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
			$order_query = " ORDER BY id ASC";
		}else{
            foreach ($order as $row) {
                if ($row['column'] == 0) {
                    $order_query = $order_query == "" ? " ORDER BY name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }
            }
        }

        $search_query = "WHERE id != '1' AND active = '1' AND (name LIKE '%".$search."%')";
        $group_list = $this->Admin_Model->get_group_list(" $search_query $order_query LIMIT $start, $count");
		$output_data = array();
		$result['data'] = [];
        foreach ($group_list as $row) {
            $row['insert_time'] = date('d M Y H:i:s', strtotime($row['insert_time']));
            $btn = '';
            $btn .= "<a href='" . site_url() . "Admin/edit/" . $row['id'] . "' class='btn-sm btn-primary' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
		$json['response_data'] = $result;
		$this->load->view("output/success_response", $json);
    }

    public function add(){
		$this->load(ADMIN_URL . '/add_group');
    }

    public function get_group_permission_list(){
        $language = isset($this->request_data['language']) ?$this->request_data['language']: 'english';

        $menu_list = $this->Common_Model->get_all_sub_menu();
        
        $grp_pri = array();
        $count = 1;
        foreach($menu_list as $mky => $mli){
            $check_box = "<input type='checkbox' id='admin_group_checkbox_" .$mli['id']. "' name='menu_id[]' value='" .$mli['id']. "'>";

            $grp_pri[] = array(
                $check_box,
                $this->Lang_Model->replaceLang($mli['parent_name']),
                $this->Lang_Model->replaceLang($mli['name'])
            );
        }
        $data['response_data']['permission'] = $grp_pri;
        $this->load->view("output/success_response", $data);
    }

    public function insert_group(){
        $name = isset($this->request_data['name']) ?$this->request_data['name']: '';
        $menu_id = isset($this->request_data['menu_id']) ?$this->request_data['menu_id']: array();

        $data = array(
            'name' => $name
        );

        $check_group_name = $this->Admin_Model->get_group_data("WHERE name = '$name'");

        if(!empty($check_group_name)){
            $data['message'] = $this->lang->line("data_exist");
            $this->load->view("output/error_response", $data);
        }else{
            $group_id = $this->Admin_Model->insert_group($data);

            $data_dashboard = array(
                'group_id' => $group_id,
                'menu_id' => 1
            );

            $this->Admin_Model->insert_group_privellges_category($data_dashboard);
  
            for($i = 0; $i < count($this->request_data['menu_id']); $i++)
            {
                if($this->request_data['menu_id'][$i] != '')
                {
                    $user_category = $this->Common_Model->get_menu_info(" WHERE id = '".$this->request_data['menu_id'][$i]."'", "");
                    $user_category_id = $user_category['ref'];

                    $check_user_category = $this->Admin_Model->get_group_privellges_info(" WHERE active = '1' AND group_id = '$group_id' AND menu_id = '".$user_category_id."'");

                    if($check_user_category == NULL){
                        $data_category = array(
                            'group_id' => $group_id,
                            'menu_id' => $user_category_id
                        );

                        $this->Admin_Model->insert_group_privellges_category($data_category);
                    }
                }
            }

            $this->Admin_Model->insert_group_privellges($menu_id, $group_id);

            $data['response_data'] = $data;
            $this->load->view("output/success_response", $data);
        }
    }

    public function edit(){
		$this->load(ADMIN_URL . '/edit_group');
    }

    public function get_group_info(){
        $group_id = isset($this->request_data['group_id']) ?$this->request_data['group_id']: '';
        $language = isset($this->request_data['language']) ?$this->request_data['language']: 'english';

        $group_data = $this->Admin_Model->get_group_info($group_id);
        
        $group_prevellge = $this->Admin_Model->get_group_privellges($group_id);
        $menu_list = $this->Common_Model->get_all_sub_menu();
        
        $grp_pri = array();
        $count = 1;
        foreach($menu_list as $mky => $mli){
            if(in_array($mli['id'], array_column($group_prevellge, 'menu_id'))){
                $check_box = "<input type='checkbox' id='admin_group_checkbox_" .$mli['id']. "' name='menu_id[]' value='" .$mli['id']. "' CHECKED>";
            }
            else{
                $check_box = "<input type='checkbox' id='admin_group_checkbox_" .$mli['id']. "' name='menu_id[]' value='" .$mli['id']. "'>";
            }

            $grp_pri[] = array(
                $check_box,
                $this->Lang_Model->replaceLang($mli['parent_name']),
                $this->Lang_Model->replaceLang($mli['name'])
            );
        }

        $data['response_data']['info'] = $group_data;
        $data['response_data']['permission'] = $grp_pri;
        $this->load->view("output/success_response", $data);
    }

    public function update_group(){
        $group_name = isset($this->request_data['name']) ?$this->request_data['name']: '';
        $group_id = isset($this->request_data['group_id']) ?$this->request_data['group_id']: '';
        $menu_id = isset($this->request_data['menu_id']) ?$this->request_data['menu_id']: array();
        $language = isset($this->request_data['language']) ?$this->request_data['language']: 'english';

        $data = array(
            'name' => $group_name
        );

        $check_dashboard_exist = $this->Admin_Model->get_group_privellges_info(" WHERE active = '1' AND group_id = '$group_id' AND menu_id = '1'");

        if($check_dashboard_exist == NULL){
            $data_dashboard = array(
                'group_id' => $group_id,
                'menu_id' => 1
            );

            $this->Admin_Model->insert_group_privellges_category($data_dashboard);
        }
        
        $this->Admin_Model->update_group($data, $group_id);


        $this->Admin_Model->update_group_privellges($menu_id, $group_id);
        $data['response_data'] = $data;
        $this->load->view("output/success_response", $data);
    }

    public function get_withdrawal_list(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $status = isset($this->request_data['status']) ? $this->request_data['status'] : "PENDING";

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'company_id' => $company_id, 'status' => $status);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_WITHDRAW, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('description' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_WITHDRAW, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }

        $output_data = array();
        $result['data'] = [];
        $withdraw_list = $this->Api_Model->get_datatables_list(TBL_WITHDRAW, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);

        foreach ($withdraw_list as $row) {
            $agent_info = $this->Api_Model->get_rows_info(TBL_USER, "id, username, fullname, bank_name, account_name, account_no", array('id' => $row['user_id'], 'active' => 1));
            if(isset($agent_info['id']) && $agent_info['id'] > 0){
                $username = $agent_info['username'];
                $fullname = $agent_info['fullname'];
                $bank_name = $agent_info['bank_name'];
                $account_name = $agent_info['account_name'];
                $account_no = $agent_info['account_no'];
            }else{
                $username = "";
                $fullname = "";
                $bank_name = "";
                $account_name = "";
                $account_no = "";
            }

            if($username == "" || $fullname == ""){
                $row['agent_name'] = $fullname . "<br>" . $bank_name . "<br>" . $account_name . "<br>" . $account_no;
            }else{
                $row['agent_name'] = $fullname . " (" . $username . ")" . "<br>" . $bank_name . "<br>" . $account_name . "<br>" . $account_no;
            }

            $btn = '';
            if($row['status'] == "PENDING"){
                $btn .= "<a href='#' onclick='approve_withdraw(" . $row['id'] . "); return false;' class='btn-sm btn-success' style='border:none;'>" . "Approve" . "</a> <a href='#' onclick='reject_withdraw(" . $row['id'] . "); return false;' class='btn-sm btn-danger' style='border:none;'>" . "Reject" . "</a>";
            }
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function approve_withdraw(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array('status' => "APPROVE");
        $this->Api_Model->update_data(TBL_WITHDRAW, array('id' => $id), $data);

        $this->load->view("output/success_response");
    }

    public function reject_withdraw(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array('status' => "REJECT");
        $this->Api_Model->update_data(TBL_WITHDRAW, array('id' => $id), $data);

        $this->load->view("output/success_response");
    }

    public function get_ticket(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_TICKET, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_TICKET, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id DESC";
        }

        $output_data = array();
        $result['data'] = [];
        $ticket_list = $this->Api_Model->get_datatables_list(TBL_TICKET, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($ticket_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $btn = '';
            $btn .= "<a href='" . site_url() . "Admin/reply/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . "Reply" . "</a> ";
            if($row['status'] == 1){
                $btn .= "<a href='#' onclick='close_ticket(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Closed" . "</a>";
            }
            if($row['status'] == 1){
                $row['ticket_status'] = "Open";
            }else{
                $row['ticket_status'] = "Close";
            }
            $row['ticket_id'] = "#000" . $row['id'];
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function reply_ticket(){
        $ticket_id = isset($this->request_data['ticket_id']) ? $this->request_data['ticket_id'] : 0;
        $message = isset($this->request_data['message']) ? $this->request_data['message'] : "";

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/ticket';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/ticket')) {
                @mkdir(IMAGE_PATH . './img/ticket', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('Image'))
            {
                $img = $this->upload->data();
                $this->resizingImage($img['file_name']);
                $image = $img['file_name'];
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }else{
            $image = "";
        }

        if($image == "" && $message == ""){
            $data['message'] = "Please enter either message or attachment !";
            $this->load->view("output/error_response", $data);
        }else{
            $data_ticket = array(
                'ticket_id' => $ticket_id,
                'message' => $message,
                'attachment' => $image
            );
            $this->Api_Model->insert_data(TBL_TICKET_REPLY, $data_ticket);

            $encrypt_ticket_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($ticket_id));
            $json_response = array(
                'ticket_id' => $encrypt_ticket_id
            );

            $data['response_data'] = $json_response;
            $this->load->view("output/success_response", $data);
        }
    }

    public function close_ticket(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'status' => 0
        );
        $this->Api_Model->update_data(TBL_TICKET, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function resizingImage($file_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/ticket/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 1080,
                'new_image'     => IMAGE_PATH . 'img/ticket/' . $file_name
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
}
?>