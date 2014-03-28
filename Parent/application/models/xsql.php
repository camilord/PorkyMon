<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 8:20 AM
 */

class xSQL extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function query($q, $single = false) {
        $sql = $this->db->query($q);
        if ($sql->num_rows() > 0) {
            if ($single) {
                return $sql->row_array();
            } else {
                return $sql->result_array();
            }
        } else {
            return null;
        }
    }

    public function insert($table, $data, $rtn_id = true) {
        $this->db->insert($table, $data);
        if ($rtn_id) {
            return $this->db->insert_id();
        } else {
            return $this->db->affected_rows();
        }
    }

    public function update($table, $data, $where, $rtn_affected = true) {
        $this->db->where($where);
        $this->db->update($table, $data);
        if ($rtn_affected) {
            return $this->db->affected_rows();
        } else {
            return true;
        }
    }
} 