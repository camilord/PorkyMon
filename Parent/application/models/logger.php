<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 27/03/14
 * Time: 4:38 PM
 */

class Logger extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    public function log($msg) {
        if (is_array($msg)) {
            $msg = json_encode($msg);
        }
        $data = array(
            'ip' => $_SERVER['REMOTE_ADDR'],
            'data' => $msg,
            'created' => date('Y-m-d H:i:s')
        );
        $sql = $this->db->insert('logs', $data);
        return $this->db->insert_id();
    }

} 