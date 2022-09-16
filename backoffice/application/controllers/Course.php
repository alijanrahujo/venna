<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/course_list");
    }

    public function add(){
        $this->load(ADMIN_URL . "/add_course");
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_COURSE, "*", array('active' => 1, 'id' => $id));
        $this->load(ADMIN_URL . "/edit_course", $this->page_data);
    }

    public function cadd(){
        $this->load(ADMIN_URL . "/add_course_details");
    }

    public function cedit(){
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_COURSE_DETAILS, "*", array('active' => 1, 'id' => $this->uri->segment(3)));
        $this->load(ADMIN_URL . "/edit_course_details", $this->page_data);
    }

    public function calist(){
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_COURSE_DETAILS, "*", array('active' => 1, 'id' => $this->uri->segment(3)));
        $this->load(ADMIN_URL . "/course_attachment_list", $this->page_data);
    }

    public function caadd(){
        $this->load(ADMIN_URL . "/add_course_attachment");
    }

    public function caedit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $this->page_data['edit'] = $this->Api_Model->get_rows_info(TBL_COURSE_ATTACHMENT, "*", array('active' => 1, 'id' => $id));
        $this->load(ADMIN_URL . "/edit_course_attachment", $this->page_data);
    }

    public function get_course(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $user_id = isset($this->request_data['user_id']) ? $this->request_data['user_id'] : 0;

        $user_info = $this->Api_Model->get_rows_info(TBL_USER, "id, company_id", array('id' => $user_id, 'active' => 1));
        $company_id = isset($user_info['id']) ? $user_info['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'company_id' => $company_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_COURSE, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_COURSE, $where_query, $where_group_like_query, $where_group_or_like_query);
        } else {
            $result['recordsFiltered'] = $result['recordsTotal'];        	
        }

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }else{
            foreach ($order as $row) {
                if ($row['column'] == 1) {
                    $order_query = $order_query == "" ? " name ".$row['dir'] : $order_query.", name ".$row['dir'];
                }else if ($row['column'] == 2) {
                    $order_query = $order_query == "" ? " publisher ".$row['dir'] : $order_query.", publisher ".$row['dir'];
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $training_list = $this->Api_Model->get_datatables_list(TBL_COURSE, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($training_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $counting++;
            $row['count'] = $counting;

            $course_list = "";
            $course_details_list = $this->Api_Model->get_rows(TBL_COURSE_DETAILS, "*", array('course_id' => $row['id'], 'active' => 1));
            if(!empty($course_details_list)){
                $course_count = 0;
                foreach($course_details_list as $row_course_details){
                    $course_details_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row_course_details['id']));
                    $course_count++;

                    $course_list .= $course_count . ". " . "<a href='" . site_url() . "Course/calist/" . $row_course_details['id'] . "'>" . $row_course_details['name'] . "</a> <a href='" . site_url() . "Course/caadd/" . $id . "/" . $course_details_id . "' class='btn-sm btn-info' style='border:none;'>" . "Add Attachment" . "</a> <a class='btn-sm btn-success' href='" . site_url() . "Course/cedit/" . $row_course_details['id'] . "'>" . "Edit" . "</a> <a href='#' onclick='delete_course_details(" . $row_course_details['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Delete" . "</a><br><br>";
                }
            }else{
                $course_list = "";
            }
            $row['course_details'] = $course_list;
            $course_desc = $row['name'] . "<br><br>" . "<img style='border-radius: 5px;' width='300' src='" . DISPLAY_PATH . "img/course/" . $row['image'] . "'>";
            $row['name'] = $course_desc;

            $btn = '';
            $btn .= "<a href='" . site_url() . "Course/cadd/" . $id . "' class='btn-sm btn-success' style='border:none;'>" . "Add Course" . "</a> <a href='" . site_url() . "Course/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . "Edit" . "</a> <a href='#' onclick='delete_course(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Delete" . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_course(){
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $publisher = isset($this->request_data['publisher']) ? $this->request_data['publisher'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/course';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/course')) {
                @mkdir(IMAGE_PATH . './img/course', 0777, TRUE);
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

        $data_course = array(
            'company_id' => $company_id,
            'name' => $name,
            'publisher' => $publisher,
            'content' => $content,
            'image' => $image
        );
        $this->Api_Model->insert_data(TBL_COURSE, $data_course);

        $json['response_data'] = $data_course;
	    $this->load->view("output/success_response", $json);
    }

    public function update_course(){
        $course_id = isset($this->request_data['course_id']) ? $this->request_data['course_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $publisher = isset($this->request_data['publisher']) ? $this->request_data['publisher'] : "";
        $content = isset($this->request_data['content']) ? $this->request_data['content'] : "";

        if (!empty($_FILES['Image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/course';
            $config['allowed_types'] = 'jpg|png|jpeg';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/course')) {
                @mkdir(IMAGE_PATH . './img/course', 0777, TRUE);
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

        $data_course = array(
            'name' => $name,
            'publisher' => $publisher,
            'content' => $content
        );
        if($image != ""){
            $data_course['image'] = $image;
        }
        $this->Api_Model->update_data(TBL_COURSE, array('id' => $course_id, 'active' => 1), $data_course);

        $json['response_data'] = $data_course;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_course(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_COURSE, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function insert_course_details(){
        $course_id = isset($this->request_data['course_id']) ? $this->request_data['course_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $embed_url = isset($this->request_data['embed_url']) ? $this->request_data['embed_url'] : "";
        $create_url = $this->getYoutubeEmbedUrl($embed_url);

        $data_course = array(
            'course_id' => $course_id,
            'name' => $name,
            'embed_url' => $create_url
        );
        $this->Api_Model->insert_data(TBL_COURSE_DETAILS, $data_course);

        $json['response_data'] = $data_course;
	    $this->load->view("output/success_response", $json);
    }

    public function update_course_details(){
        $course_details_id = isset($this->request_data['course_details_id']) ? $this->request_data['course_details_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $embed_url = isset($this->request_data['embed_url']) ? $this->request_data['embed_url'] : "";
        $create_url = $this->getYoutubeEmbedUrl($embed_url);

        $data_course = array(
            'name' => $name,
            'embed_url' => $create_url
        );
        $this->Api_Model->update_data(TBL_COURSE_DETAILS, array('id' => $course_details_id, 'active' => 1), $data_course);

        $json['response_data'] = $data_course;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_course_details(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_COURSE_DETAILS, array('id' => $id), $data);
        $this->Api_Model->update_multiple_data(TBL_COURSE_ATTACHMENT, array('course_details_id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function get_course_attachment(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $course_details_id = isset($this->request_data['course_details_id']) ? $this->request_data['course_details_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'course_details_id' => $course_details_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_COURSE_ATTACHMENT, $where_query, $where_group_like_query, $where_group_or_like_query);
        $result['recordsFiltered'] = $result['recordsTotal'];

        $result['total_pages'] = ceil($result['recordsFiltered'] / $count);
        $result['current_page'] = floor($start/$count) + 1;
        $result['records_per_page'] = $count;
        
        $order_query = "";

        if(empty($order)){
            $order_query = " id ASC";
        }

        $output_data = array();
        $result['data'] = [];
        $course_attachment_list = $this->Api_Model->get_datatables_list(TBL_COURSE_ATTACHMENT, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($course_attachment_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $btn = '';
            $course_attachment = DISPLAY_PATH . "img/course/" . $row['attachment'];
            $row['attachment'] = "<a target='_blank' href='" . DISPLAY_PATH . "img/course/" . $row['attachment'] . "'>" . $course_attachment . "</a>";
            $btn .= "<a href='" . site_url() . "Course/caedit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . "Edit" . "</a> <a href='#' onclick='delete_course_attachment(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . "Delete" . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_course_attachment(){
        $course_id = isset($this->request_data['course_id']) ? $this->request_data['course_id'] : 0;
        $course_details_id = isset($this->request_data['course_details_id']) ? $this->request_data['course_details_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
            
        if (!empty($_FILES['attachment']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/course';
            $config['allowed_types'] = 'pptx|pdf';  
            $config['max_size'] = '5120'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/course')) {
                @mkdir(IMAGE_PATH . './img/course', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('attachment'))
            {
                $img = $this->upload->data();
                $attachment = $img['file_name'];

                $data_course_attachment = array(
                    'course_id' => $course_id,
                    'course_details_id' => $course_details_id,
                    'name' => $name,
                    'attachment' => $attachment
                );
                $this->Api_Model->insert_data(TBL_COURSE_ATTACHMENT, $data_course_attachment);
        
                $json['response_data'] = $data_course_attachment;
                $this->load->view("output/success_response", $json);
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }else{
            $data['message'] = "Empty Attachment";
			$this->load->view("output/error_response", $data);
        }
    }

    public function update_course_attachment(){
        $course_attachment_id = isset($this->request_data['course_attachment_id']) ? $this->request_data['course_attachment_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        
        $data_course_attachment = array(
            'name' => $name
        );
        $this->Api_Model->update_data(TBL_COURSE_ATTACHMENT, array('id' => $course_attachment_id, 'active' => 1), $data_course_attachment);

        $json['response_data'] = $data_course_attachment;
        $this->load->view("output/success_response", $json);
    }

    public function delete_course_attachment(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_COURSE_ATTACHMENT, array('id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function getYoutubeEmbedUrl($url)
    {
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        return 'https://www.youtube.com/embed/' . $youtube_id ;
    }

    public function optimize_desc(){
        if (!empty($_FILES['image']['name']))
        {
            $config['upload_path'] = IMAGE_PATH . './img/course';
            $config['allowed_types'] = 'jpg|png|jpeg';
            $config['max_size'] = '10000'; //in KB    
            $config['encrypt_name'] = TRUE;               
            // create directory if not exists
            if (!@is_dir(IMAGE_PATH . 'img/course')) {
                @mkdir(IMAGE_PATH . './img/course', 0777, TRUE);
            }
			$this->upload->initialize($config);  
			         
            if ($this->upload->do_upload('image'))
            {
                $img = $this->upload->data();
                $image = $img['file_name']; 

                $json['response_data'] = DISPLAY_PATH . "img/course/" . $image;
		        $this->load->view("output/success_response", $json);
            }
            else
            {
                $data['message'] = $this->upload->display_errors();
				$this->load->view("output/error_response", $data);
            }
        }
    }

    public function resizingImage($file_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/course/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 1080,
                'new_image'     => IMAGE_PATH . 'img/course/' . $file_name
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
