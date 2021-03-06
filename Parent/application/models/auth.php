<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 8:54 AM
 */

class Auth extends  CI_Model{

    private $current_data = null;

    public function __construct() {
        //parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function get() {
        return $this->current_data;
    }

    public function verify($username, $password) {
        $this->db->where(array(
            'username' => $username,
            'passcode' => md5($password)
        ));
        $sql = $this->db->get("users");
        if ($sql->num_rows() > 0) {
            $this->current_data = $sql->row_array();
            return true;
        } else {
            return false;
        }
        echo $this->db->last_query();
    }

    public function identify($hostname, $secret_key) {
        $this->db->where(array(
            'hostname' => $hostname,
            'secret_key' => $secret_key
        ));
        $sql = $this->db->get("servers");
        if ($sql->num_rows() > 0) {
            $this->current_data = $sql->row_array();
            return true;
        } else {
            return false;
        }
    }
} 