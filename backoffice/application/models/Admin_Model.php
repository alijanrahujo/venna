<?php
class Admin_Model extends CI_Model {
    private $tblGroup = "group";
    private $tblPermission = "permission";
    private $tblDelmode = "active";
    private $tblID = "id";

    public function __construct()
    {
        parent::__construct();
    }

    public function get_group_list($where = ''){
        $sql = "SELECT *  FROM ".$this->db->dbprefix($this->tblGroup)." ".$where;
        
        $rs = $this->db->query($sql);

        return $rs->result_array();
    }

    public function count_group_list($where = ''){
        $sql = "SELECT count(*) AS TOTAL FROM ".$this->db->dbprefix($this->tblGroup)." ".$where;
        
        $rs = $this->db->query($sql);

        $output =  $rs->row_array();
        return $output['TOTAL'];
    }

    public function get_group_data($where = "")
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix($this->tblGroup) . " " . $where;
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function insert_group($data){
        $this->db->insert($this->db->dbprefix($this->tblGroup), $data);
        return $this->db->insert_id();
    }

    public function insert_group_privellges_category($data)
    {
        $this->db->insert($this->db->dbprefix($this->tblPermission), $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function get_group_privellges_info($where = "")
    {
        $sql = "SELECT * FROM " . $this->db->dbprefix($this->tblPermission) . " " . $where;
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function insert_group_privellges($menu_data, $group_id){
        foreach($menu_data as $mli){
            $insert_data = array(
                'group_id' => $group_id,
                'menu_id' =>  $mli,
                'active' =>  1,
            );
            $this->db->insert($this->db->dbprefix($this->tblPermission), $insert_data);
        }
    }

    public function get_group_info($id){
        $sql = "SELECT *  FROM ".$this->db->dbprefix($this->tblGroup)." WHERE id='".$id."'";
        $rs = $this->db->query($sql);
        return $rs->row_array();
    }

    public function get_group_privellges($id){
        $sql = "SELECT *  FROM ".$this->db->dbprefix($this->tblPermission)." WHERE group_id='".$id."' AND active=1";
        $rs = $this->db->query($sql);
        return count($rs->result_array()) > 0?$rs->result_array():array();
    }

    public function update_group($data, $id)
    {
        $this->db->where("id", $id);
        $this->db->limit(1);
        $this->db->update($this->db->dbprefix($this->tblGroup), $data);
    }

    public function update_group_privellges($menu_data, $group_id){
        $clear_data = array(
            'active' => 0
        );
        $this->db->where('group_id', $group_id);
        $this->db->where('menu_id !=', 1);
        $this->db->update($this->db->dbprefix($this->tblPermission), $clear_data);

        foreach($menu_data as $mli){
            $active_menu = array();
            $active_data = array(
                'active' => 1
            );
            $active_menu = $this->get_parent_menu($mli);
            foreach($active_menu as $ali){
                if($this->check_menu_active($ali, $group_id)){
                    $this->db->where('group_id', $group_id);
                    $this->db->where('menu_id', $ali);
                    $this->db->update($this->db->dbprefix($this->tblPermission), $active_data);
                }
                else{
                    $insert_data = array(
                        'group_id' => $group_id,
                        'menu_id' =>  $ali,
                        'active' =>  1,
                    );
                    $this->db->insert($this->db->dbprefix($this->tblPermission), $insert_data);
                }
            }
        }
    }

    public function get_parent_menu($menu_id){
        $sql = "SELECT ref FROM vny_menu WHERE id='$menu_id'";

        $row = $this->db->query($sql)->row_array();
        $output = array();
        if($row['ref'] > 0){
            $output[] = $menu_id;
            $output =array_merge($output, $this->get_parent_menu($row['ref']));
            return $output;
        }
        else{
            $output[] = $menu_id;
            return $output;
        }
    }

    public function check_menu_active($menu_id, $group_id){
        $sql = "SELECT count(*) as TOTAL FROM vny_permission WHERE menu_id='$menu_id' AND group_id='$group_id'";

        $row = $this->db->query($sql)->row_array();

        if($row['TOTAL'] > 0){
            return true;
        }
        else{
            return false;
        }
    }
}
?>