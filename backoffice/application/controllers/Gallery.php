<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends Base_Controller {
	public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index(){
        $this->load(ADMIN_URL . "/gallery_list");
    }

    public function add(){
        $this->load(ADMIN_URL . "/add_gallery");
    }

    public function edit(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_GALLERY, "*", array('active' => 1, 'id' => $id));
        $gallery_attachment = $this->Api_Model->get_rows(TBL_GALLERY_ATTACHMENT, "*", array('active' => 1, 'gallery_id' => $id), "", "", "sequence", "ASC");
        $this->page_data['edit'] = $edit_info;
        $this->page_data['attachment'] = $gallery_attachment;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_gallery", "Gallery");
    }
    
    public function attachment(){
        $id = $this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(3)));
        $edit_info = $this->Api_Model->get_rows_info(TBL_GALLERY_ATTACHMENT, "*", array('active' => 1, 'id' => $id));
        $this->page_data['edit'] = $edit_info;
        $this->check_is_fake_data($edit_info, $this->page_data, "edit_gallery_detail", "Gallery");
    }

    public function get_gallery(){
        $draw = isset($this->request_data['draw']) ? $this->request_data['draw'] : 0;
        $start = isset($this->request_data['start']) ? $this->request_data['start'] : 0;
        $count = isset($this->request_data['length']) ? $this->request_data['length'] : 10;
        $order = isset($this->request_data['order']) ? $this->request_data['order'] : [];
        $search = isset($this->request_data['search']['value']) ? $this->request_data['search']['value'] : "";
        $language = isset($this->request_data['language']) ? $this->request_data['language'] : "english";
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;

        $result = array();
        $result['draw'] = $draw;

        $where_query = array('active' => 1, 'company_id' => $company_id);
        $where_group_like_query = "";
        $where_group_or_like_query = "";

        $result['recordsTotal'] = $this->Api_Model->count_datatables_list(TBL_GALLERY, $where_query, $where_group_like_query, $where_group_or_like_query);

        if ($search != "") {
            $where_group_like_query = array('name' => $search);
            $result['recordsFiltered'] = $this->Api_Model->count_datatables_list(TBL_GALLERY, $where_query, $where_group_like_query, $where_group_or_like_query);
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
                }
            }
        }

        $output_data = array();
        $result['data'] = [];
        $gallery_list = $this->Api_Model->get_datatables_list(TBL_GALLERY, "*", $where_query, $where_group_like_query, $where_group_or_like_query, $order_query, $start, $count);
        $counting = 0;

        foreach ($gallery_list as $row) {
            $id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($row['id']));
            $btn = '';
            $btn .= "<a href='" . site_url() . "Gallery/edit/" . $id . "' class='btn-sm btn-info' style='border:none;'>" . $this->Lang_Model->replaceLang("edit") . "</a> <a href='#' onclick='delete_gallery(" . $row['id'] . ")' class='btn-sm btn-danger' style='border:none;'>" . $this->Lang_Model->replaceLang("delete") . "</a>";
            $row['action'] = $btn;
            $result['data'][] = $row;
        }
        
        $json['response_data'] = $result;
        $this->load->view("output/success_response", $json);
    }

    public function insert_gallery(){
        $company_id = isset($this->request_data['company_id']) ? $this->request_data['company_id'] : 0;
        $type = isset($this->request_data['type']) ? $this->request_data['type'] : "";
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";

        if($name == ""){
            $data['message'] = "Name is empty !";
            $this->load->view("output/error_response", $data);
        }else{
            $data = array(
                'type' => $type,
                'company_id' => $company_id,
                'name' => $name
            );
            $this->Api_Model->insert_data(TBL_GALLERY, $data);

            $json['response_data'] = $data;
            $this->load->view("output/success_response", $json);
        }
    }

    public function update_gallery(){
        $gallery_id = isset($this->request_data['gallery_id']) ? $this->request_data['gallery_id'] : 0;
        $name = isset($this->request_data['name']) ? $this->request_data['name'] : "";
        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($gallery_id));

        $data = array(
            'name' => $name
        );
        $this->Api_Model->update_data(TBL_GALLERY, array('id' => $gallery_id, 'active' => 1), $data);
        $data['encrypt_id'] = $encrypt_id;
        $json['response_data'] = $data;
	    $this->load->view("output/success_response", $json);
    }

    public function delete_gallery(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_data(TBL_GALLERY, array('id' => $id), $data);
        $this->Api_Model->update_multiple_data(TBL_GALLERY_ATTACHMENT, array('gallery_id' => $id), $data);

        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function delete_attachment(){
        $id = isset($this->request_data['id']) ? $this->request_data['id'] : 0;
        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($id));

        $data = array(
            'active' => 0
        );
        $this->Api_Model->update_multiple_data(TBL_GALLERY_ATTACHMENT, array('id' => $id), $data);
        $data['encrypt_id'] = $encrypt_id;
        $json['response_data'] = $data;
        $this->load->view("output/success_response", $json);
    }

    public function upload(){
        $title = isset($this->request_data['title']) ? $this->request_data['title'] : "";
        $gallery_id = isset($this->request_data['gallery_id']) ? $this->request_data['gallery_id'] : 0;
        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($gallery_id));

        $gallery_info = $this->Api_Model->get_rows_info(TBL_GALLERY, "*", array('id' => $gallery_id, 'active' => 1));
        $gallery_type = isset($gallery_info['id']) ? $gallery_info['type'] : "";

        if (!empty($_FILES['file']['name']))
        {
            $file_ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
            if($gallery_type == "image"){
                if($file_ext != "jpg" && $file_ext != "png" && $file_ext != "jpeg"){
                    $data['message'] = "Only allow JPG, PNG, JPEG Format !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->proceed_attachment_upload($gallery_id, $encrypt_id, $file_ext, $title);
                }
            }else if($gallery_type == "video"){
                if($file_ext != "mp4" && $file_ext != "mov"){
                    $data['message'] = "Only allow MP4, MOV Format !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->proceed_attachment_upload($gallery_id, $encrypt_id, $file_ext, $title);
                }
            }else if($gallery_type == "pdf"){
                if($file_ext != "pdf"){
                    $data['message'] = "Only allow PDF Format !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->proceed_attachment_upload($gallery_id, $encrypt_id, $file_ext, $title);
                }
            }else if($gallery_type == "ppt"){
                if($file_ext != "pptx"){
                    $data['message'] = "Only allow PPTX Format !";
                    $this->load->view("output/error_response", $data);
                }else{
                    $this->proceed_attachment_upload($gallery_id, $encrypt_id, $file_ext, $title);
                }
            }
        }else{
            $data['message'] = "Empty Attachment !";
            $this->load->view("output/error_response", $data);
        }
    }

    public function proceed_attachment_upload($gallery_id, $encrypt_id, $file_ext, $title){
        $config['upload_path'] = IMAGE_PATH . './img/gallery/' . $gallery_id;
        $config['allowed_types'] = '*'; 
        if($file_ext == "mp4"){
            $config['max_size'] = '50000';
        }else if($file_ext == "pptx" || $file_ext == "pdf"){
            $config['max_size'] = '10000';
        }else{
            $config['max_size'] = '5000';
        }
        $config['encrypt_name'] = TRUE;               
        // create directory if not exists
        if (!@is_dir(IMAGE_PATH . 'img/gallery/' . $gallery_id)) {
            @mkdir(IMAGE_PATH . './img/gallery/' . $gallery_id, 0777, TRUE);
        }
        $this->upload->initialize($config);
                
        if ($this->upload->do_upload('file'))
        {
            $img = $this->upload->data();
            if($file_ext == "jpg" || $file_ext == "png" || $file_ext == "jpeg"){
                $this->resizingImage($img['file_name']);
            }
            $image = $img['file_name'];

            $gallery_attachment_info = $this->Api_Model->get_rows_info(TBL_GALLERY_ATTACHMENT, "*", array('id' => $gallery_id, 'active' => 1));
            if(isset($gallery_attachment_info['id']) && $gallery_attachment_info['id'] > 0){
                $sequence_id = $gallery_attachment_info['sequence'];
                $new_sequence_id = $sequence_id + 1;
            }else{
                $new_sequence_id = 1;
            }

            $data_attachment = array(
                'gallery_id' => $gallery_id,
                'attachment_type' => $file_ext,
                'title' => $title,
                'name' => $image,
                'sequence' => $new_sequence_id,
                'ori_sequence' => $new_sequence_id,
            );
            $this->Api_Model->insert_data(TBL_GALLERY_ATTACHMENT, $data_attachment);

            $data_attachment['encrypt_id'] = $encrypt_id;

            $json['response_data'] = $data_attachment;
            $this->load->view("output/success_response", $json);
        }
        else
        {
            $data['message'] = $this->upload->display_errors();
            $this->load->view("output/error_response", $data);
        }
    }

    public function exchange_sequence(){
        $gallery_id = isset($this->request_data['gallery_id']) ? $this->request_data['gallery_id'] : 0;
        $from_sequence = isset($this->request_data['from_sequence']) ? $this->request_data['from_sequence'] : 0;
        $to_sequence = isset($this->request_data['to_sequence']) ? $this->request_data['to_sequence'] : 0;
        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($gallery_id));

        $from_sequence_info = $this->Api_Model->get_info_sql(TBL_GALLERY_ATTACHMENT, "*", "WHERE active = '1' AND gallery_id = '$gallery_id' AND ori_sequence = '$from_sequence'");
        if(isset($from_sequence_info['id']) && $from_sequence_info['id'] > 0){
            $from_category_id = $from_sequence_info['id'];
            $data_update_from = array(
                'sequence' => $to_sequence
            );
            $this->Api_Model->update_data(TBL_GALLERY_ATTACHMENT, array('active' => 1, 'gallery_id' => $gallery_id, 'id' => $from_category_id), $data_update_from);
        }

        $to_sequence_info = $this->Api_Model->get_info_sql(TBL_GALLERY_ATTACHMENT, "*", "WHERE active = '1' AND gallery_id = '$gallery_id' AND ori_sequence = '$to_sequence'");
        if(isset($to_sequence_info['id']) && $to_sequence_info['id'] > 0){
            $to_category_id = $to_sequence_info['id'];
            $data_update_to = array(
                'sequence' => $from_sequence
            );
            $this->Api_Model->update_data(TBL_GALLERY_ATTACHMENT, array('active' => 1, 'gallery_id' => $gallery_id, 'id' => $to_category_id), $data_update_to);
        }

        $rearrange_gallery_list = $this->Api_Model->get_all_sql(TBL_GALLERY_ATTACHMENT, "*", "WHERE active = '1' AND gallery_id = '$gallery_id'");
        foreach($rearrange_gallery_list as $row_gallery_list){
            $sequence = $row_gallery_list['sequence'];
            $id = $row_gallery_list['id'];

            $data = array(
                'ori_sequence' => $sequence
            );
            $this->Api_Model->update_data(TBL_GALLERY_ATTACHMENT, array('active' => 1, 'gallery_id' => $gallery_id, 'id' => $id), $data);
        }

        $data['response_data'] = $encrypt_id;
        $this->load->view("output/success_response", $data);
    }

    public function update_gallery_detail(){
        $gallery_detail_id = isset($this->request_data['gallery_detail_id']) ? $this->request_data['gallery_detail_id'] : 0;
        $title = isset($this->request_data['title']) ? $this->request_data['title'] : "";

        $gallery_attachment_info = $this->Api_Model->get_rows_info(TBL_GALLERY_ATTACHMENT, "id, gallery_id", array('id' => $gallery_detail_id, 'active' => 1));
        $gallery_id = isset($gallery_attachment_info['id']) ? $gallery_attachment_info['gallery_id'] : 0;

        $data = array(
            'title' => $title
        );
        $this->Api_Model->update_data(TBL_GALLERY_ATTACHMENT, array('id' => $gallery_detail_id), $data);

        $encrypt_id = str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($gallery_id));

        $data['response_data'] = $encrypt_id;
        $this->load->view("output/success_response", $data);
    }

    public function resizingImage($file_name)
    {
        $config = array(
            // Large Image
            array(
                'image_library' => 'GD2',
                'source_image'  => IMAGE_PATH . 'img/gallery/' . $file_name,
                'maintain_ratio' => TRUE,
                'height'        => 400,
                'new_image'     => IMAGE_PATH . 'img/gallery/' . $file_name
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
