<?php
/**
 * Created by PhpStorm.
 * User: camilo
 * Date: 2/05/14
 * Time: 3:35 PM
 */

class Updates extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function get_recent() {
        $this->db->where('resolved','n');
        $this->db->order_by('id', 'desc');
        $sql = $this->db->get("server_updates");
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return null;
        }
    }

    public function get_sms() {
        $this->db->where('sms','n');
        $this->db->order_by('id', 'desc');
        $sql = $this->db->get("server_updates");
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return null;
        }
    }


    public function get_all() {
        $this->db->order_by('id', 'desc');
        $sql = $this->db->get("server_updates");
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return null;
        }
    }

} 