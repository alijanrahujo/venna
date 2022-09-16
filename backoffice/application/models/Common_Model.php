<?php
class Common_Model extends CI_Model {
    private $tblUser = "user";
    private $tblMenu = "menu";
    private $tblID = "id";
    private $tblDelmode = "active";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_menu_info($where = "")
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix($this->tblMenu) . " " . $where;
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_menu_list($menu_id = "", $user_category_id = "")
    {
        if(!$menu_id){
            //category
            $sql = "SELECT vny_menu.* FROM vny_menu LEFT JOIN vny_permission ON vny_permission.menu_id = vny_menu.id WHERE vny_permission.group_id = '$user_category_id' AND vny_menu.ref = '0' AND vny_menu.status = '1' AND vny_permission.active = '1' ORDER BY vny_menu.seq ASC";
        }else{
            //sub category
            $sql = "SELECT vny_menu.* FROM vny_menu LEFT JOIN vny_permission ON vny_permission.menu_id = vny_menu.id WHERE vny_permission.group_id = '$user_category_id' AND vny_menu.ref = '$menu_id' AND vny_menu.status = '1' AND vny_permission.active = '1' ORDER BY vny_menu.seq ASC";
        }

        $query = $this->db->query($sql);

        $output = array();
        $variable = $query->result_array();
        foreach($variable as $t){
            $menu_list = new Common_Model();
            $sub_menu = $menu_list->get_menu_list($t['id'], $user_category_id);
            $t['sub_menu'] =  $sub_menu;
            $output[] = $t;
            unset($sub_menu);
        }
        return $output;
    }

    public function get_all_menu_list($menu_id = ''){
        $output = '';
        if(!$menu_id){
            $this->db->select('*');
            $this->db->from('vny_menu');
            $this->db->where('ref',0);
            $this->db->where('status',1);
            $this->db->order_by('seq', 'ASC');
            $query = $this->db->get();
            $query->result();
            $output = array();
            foreach ($query->result_array() as $t) {
                $sub_menu = $this->get_all_menu_list($t['id']);
                $t['sub_menu'] =  $sub_menu;
                $output[] = $t;
                unset($sub_menu);
            }
        }
        else{
            $this->db->select('*');
            $this->db->from('vny_menu');
            $this->db->where('ref',$menu_id);
            $this->db->where('status',1);
            $this->db->order_by('seq', 'ASC');
            $query = $this->db->get();
            $query->result();
            $output = array();
            foreach ($query->result_array() as $t) {
                $sub_menu = $this->get_all_menu_list($t['id']);
                $t['sub_menu'] =  $sub_menu;
                $output[] = $t;
                unset($sub_menu);
            }
        }
        return $output;
    }

    public function get_all_sub_menu(){
        $sub_sql = "SELECT m.name FROM vny_menu m WHERE m.id=sm.ref ";
        $sql = "SELECT sm.*, (".$sub_sql.") AS parent_name FROM vny_menu sm WHERE sm.status = '1' AND sm.ref != '0' ";

        $rs = $this->db->query($sql);

        return $rs->result_array();
    }
}
?>