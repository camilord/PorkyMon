<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 3:34 PM
 */

class Servers extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('default', true);
    }

    /*
     * http://www.kirupa.com/forum/showthread.php?339124-Check-if-server-is-online
     * author: Swooter
     * updated: camilo3rd
     */
    public function is_online($domain){
        try {
            error_reporting(0);
            $starttime = microtime(true);
            $file = fsockopen(trim($domain), 80, $errno, $errstr, 10);
            $stoptime = microtime(true);
            //$status = 0;
            $status = -1;
            if ($file && !preg_match("/failed/", $errstr)) {
                $status = ($stoptime - $starttime) * 1000;
                $status = floor($status);
                @fclose($file);
            }
            return $status;
        } catch(Exception $e) {
            return -1;
        }

    }

    public function load_servers() {
        //$this->db->where('deleted','n');
        $this->db->order_by('hostname', 'asc');
        $sql = $this->db->query("SELECT * FROM servers");
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
        } else {
            return null;
        }
    }

    public function load_server($where) {
        if (is_array($where)) {
            $this->db->where($where);
        }
        //$this->db->where('deleted','n');
        $this->db->order_by('hostname', 'asc');
        $sql = $this->db->query("SELECT * FROM servers");
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        } else {
            return null;
        }
    }

    public function server_last_log($where) {
        if (is_array($where)) {
            $this->db->where($where);
        }
        $this->db->order_by('id', 'desc');
        $sql = $this->db->get("server_data");
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        } else {
            return null;
        }
    }
} 