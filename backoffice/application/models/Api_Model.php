<?php
class Api_Model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_all_sql($table_name, $column, $where, $debug = false){
        $sql = "SELECT " . $column . " FROM " . $table_name . " " . $where;
        $query = $this->db->query($sql);
        if($debug){
            echo $sql;
        }else{
            return $query->result_array();
        }
    }

    public function get_info_sql($table_name, $column, $where, $debug = false){
        $sql = "SELECT " . $column . " FROM " . $table_name . " " . $where;
        $query = $this->db->query($sql);
        if($debug){
            echo $sql;
        }else{
            return $query->row_array();
        }
    }

    public function get_downline($table_name, $column, $where, $debug = false){
        $data = array();
        $sql = "SELECT " . $column . " FROM " . $table_name . " " . $where;
        $query = $this->db->query($sql);
        if($debug){
            echo $sql;
        }else{
            foreach ($query->result() as $row)
            {
                $data[] = $row->id;
            }
            return $data;
        }
    }

    public function insert_data($table_name, $data)
    {
        $this->db->insert($table_name, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function update_data($table_name, $where, $data)
    {
        $this->db->where($where);
        $this->db->limit(1);
        $this->db->update($table_name, $data);
    }

    public function update_data_order_by($table_name, $where, $data, $order_field, $order_by)
    {
        $this->db->where($where);
        $this->db->order_by($order_field, $order_by);
        $this->db->limit(1);
        $this->db->update($table_name, $data);
    }

    public function update_multiple_data($table_name, $where, $data)
    {
        $this->db->where($where);
        $this->db->update($table_name, $data);
    }

    public function get_rows($table_name, $column, $where = array(), $start = "", $count = "", $order_field = "", $order_by = "", $limit = "", $or_where = "", $like_array = array(), $or_like_array = array(), $not_like_array = array(), $debug = false) {
        $this->db->select($column);
        $this->db->from($table_name);
        $this->db->where($where);

        if($or_where != ""){
            $this->db->or_where($or_where);
        }

        if (is_array($like_array) && count($like_array) > 0) {
            $this->db->like($like_array);
        }
        if (is_array($or_like_array) && count($or_like_array) > 0) {
            //or_like
            foreach ($or_like_array as $key => $value) {
                $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $this->db->where($like_string);
        }
        if (is_array($not_like_array) && count($not_like_array) > 0) {
            //not_like
            foreach ($not_like_array as $key => $value) {
                $not_like_statements[] = " " . $key . " NOT LIKE '%" . $value . "%'";
            }
            $not_like_string = "(" . implode(' AND ', $not_like_statements) . ")";
            $this->db->where($not_like_string);
        }

        if($order_field != "" && $order_by != ""){
            $this->db->order_by($order_field, $order_by);
        }

        if($start != "" && $count != ""){
            $this->db->limit($count, $start);
        }

        if($limit != ""){
            $this->db->limit($limit);
        }

		$query= $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            if($query->num_rows() > 0) {
                return $query->result_array();
            }else {
                return array();
            }
        }
    }
    
    public function get_group_by_rows($table_name, $column, $where = array(), $group_by = "", $start = "", $count = "", $order_field = "" , $order_by = "", $debug = false) {
        $this->db->select($column);
        $this->db->from($table_name);
        $this->db->where($where);
        if($group_by != '') {
            $this->db->group_by($group_by);
        }
        if($order_field != "" && $order_by != ""){
            $this->db->order_by($order_field, $order_by);
        }
        if($start != "" && $count != ""){
            $this->db->limit($count, $start);
        }
		$query= $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            if($query->num_rows() > 0) {
                return $query->result_array();
            }else {
                return array();
            }
        }
    }

    public function get_rows_info($table_name, $column, $where= array(), $order_field = "" , $order_by = "", $limit = "", $like_array = array(), $or_like_array = array(), $not_like_array = array(), $debug = false) {
		$this->db->select($column);
        $this->db->from($table_name);
        $this->db->where($where);
        if (is_array($like_array) && count($like_array) > 0) {
            $this->db->like($like_array);
        }
        if (is_array($or_like_array) && count($or_like_array) > 0) {
            //or_like
            foreach ($or_like_array as $key => $value) {
                $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $this->db->where($like_string);
        }
        if (is_array($not_like_array) && count($not_like_array) > 0) {
            //not_like
            foreach ($not_like_array as $key => $value) {
                $not_like_statements[] = " " . $key . " NOT LIKE '%" . $value . "%'";
            }
            $not_like_string = "(" . implode(' AND ', $not_like_statements) . ")";
            $this->db->where($not_like_string);
        }
        $this->db->order_by($order_field, $order_by);
        if($limit != ""){
            $this->db->limit($limit);
        }
		$query= $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            return $query->row_array();
        }
    }
    
    public function get_join_rows_info($table_name, $column, $where= array(), $join = array(), $order_field = "" , $order_by = "", $limit = "") {
		$this->db->select($column);
        $this->db->from($table_name);
        if (is_array($join) && count($join) > 0) {
            foreach ($join as $key => $value) {
                if (!empty($join_type) && $join_type) {
                    $this->db->join($key, $value, $join_type);
                } else {
                    $this->db->join($key, $value);
                }
            }
        }
        $this->db->where($where);
        $this->db->order_by($order_field, $order_by);
        if($limit != ""){
            $this->db->limit($limit);
        }
		$query= $this->db->get();
		return $query->row_array();
	}
    
    public function get_all_rows($table_name, $fields, $where = array(), $join = array(), $order_by = array(), $start = "", $count = "", $join_type = "", $like_array = array(), $group_by = "", $or_where = array(), $or_like_array = array(), $having = "", $debug = false) {
		$this->db->select($fields);
        if (is_array($join) && count($join) > 0) {
            foreach ($join as $key => $value) {
                if (!empty($join_type) && $join_type) {
                    $this->db->join($key, $value, $join_type);
                } else {
                    $this->db->join($key, $value);
                }
            }
        }
        if (is_array($where) && count($where) > 0) {
            $this->db->where($where);
        }
        if (count($or_where) > 0) {
            $this->db->or_where($or_where);
        }
        if($having != ""){
            $this->db->having($having);
        }
        if (is_array($order_by) && count($order_by) > 0) {
            foreach ($order_by as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
        if (is_array($like_array) && count($like_array) > 0) {
            $this->db->like($like_array);
        }
        //or like
        if (is_array($or_like_array) && count($or_like_array) > 0) {
            //or_like
            foreach ($or_like_array as $key => $value) {
                $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
            }
            $like_string = "(" . implode(' OR ', $like_statements) . ")";
            $this->db->where($like_string);
        }
        if ($group_by != '') {
            $this->db->group_by($group_by);
        }

        if($start != "" && $count != ""){
            $this->db->limit($count, $start);
        }
        $database_object = $this->db->get($table_name);
        if($debug){
            print $this->db->last_query();
        }
        $table_data = array();
        foreach ($database_object->result_array() as $row) {
            $table_data[] = $row;
        }
        return $table_data;
	}

    public function get_datatables_list($table_name, $fields, $where = array(), $group_where_and = array(), $group_where_or = array(), $order_query = array(), $start = "", $count = "", $or_where = array(), $join_table = "", $join_table_on = "", $like_array = array(), $or_like_array = array(), $is_group_for_like = "", $debug = false)
    {
        $this->db->select($fields);
        $this->db->from($table_name);
        if($join_table != "" && $join_table_on != ""){
            $this->db->join($join_table, $join_table_on);
        }
        $this->db->where($where);

        if (count($or_where) > 0) {
            $this->db->or_where($or_where);
        }

        if (is_array($like_array) && count($like_array) > 0) {
            if($is_group_for_like == 1){
                $this->db->group_start();
            }
            $this->db->like($like_array);
        }

        if (is_array($or_like_array) && count($or_like_array) > 0) {
            $this->db->or_like($or_like_array);
            if($is_group_for_like == 1){
                $this->db->group_end();
            }
        }

        // if (is_array($or_like_array) && count($or_like_array) > 0) {
        //     //or_like
        //     foreach ($or_like_array as $key => $value) {
        //         $like_statements[] = " " . $key . " LIKE '%" . $value . "%'";
        //     }
        //     $like_string = "(" . implode(' OR ', $like_statements) . ")";
        //     $this->db->where($like_string);
        // }

        if($group_where_and != ""){
            $this->db->group_start();
            $this->db->like($group_where_and);
            if($group_where_or != ""){
                $this->db->or_like($group_where_or);
            }
            $this->db->group_end();
        }

        if($order_query != ""){
            $this->db->order_by($order_query);
        }

        if($start != "" && $count != ""){
            $this->db->limit($count, $start);
        }

        $query = $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            return $query->result_array();
        }
    }
	
	public function get_datatables_group_by($table_name, $fields, $where = array(), $group_where_and = array(), $group_where_or = array(), $order_query = array(), $start = "", $count = "", $group_by = "", $debug = false)
    {
        $this->db->select($fields);
        $this->db->from($table_name);
        $this->db->where($where);

        if($group_where_and != ""){
            $this->db->group_start();
            $this->db->like($group_where_and);
            if($group_where_or != ""){
                $this->db->or_like($group_where_or);
            }
            $this->db->group_end();
        }

        if($group_by != ""){
            $this->db->group_by($group_by);
        }

        if($order_query != ""){
            $this->db->order_by($order_query);
        }

        if($start != "" && $count != ""){
            $this->db->limit($count, $start);
        }

        $query = $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            return $query->result_array();
        }
    }

    public function count_datatables_list($table_name, $where = array(), $group_where_and = array(), $group_where_or = array(), $or_where = array(), $join_table = "", $join_table_on = "", $fields = "", $like_array = array(), $or_like_array = array(), $debug = false)
    {
        if($fields == ""){
            $column = "COUNT(*) as total";
        }else{
            $column = $fields;
        }
        $this->db->select($column);
        $this->db->from($table_name);

        if($join_table != "" && $join_table_on != ""){
            $this->db->join($join_table, $join_table_on);
        }

        if($where != ""){
            $this->db->where($where);
        }

        if (count($or_where) > 0) {
            $this->db->or_where($or_where);
        }

        if (is_array($like_array) && count($like_array) > 0) {
            $this->db->like($like_array);
        }

        if (is_array($or_like_array) && count($or_like_array) > 0) {
            $this->db->or_like($or_like_array);
        }

        if($group_where_and != ""){
            $this->db->group_start();
            $this->db->like($group_where_and);
            if($group_where_or != ""){
                $this->db->or_like($group_where_or);
            }
            $this->db->group_end();
        }
        
        $query = $this->db->get();
        if($debug){
            print $this->db->last_query();
        }else{
            $output = $query->row_array();
            return $output['total'];
        }
    }
}
?>