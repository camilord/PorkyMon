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
    public function is_online($domain, $port = 80){
        try {
            error_reporting(0);
            $starttime = microtime(true);
            $file = fsockopen(trim($domain), $port, $errno, $errstr, 10);
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
        $this->db->where('deleted','n');
        $this->db->order_by('hostname', 'asc');
        $sql = $this->db->get("servers");
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
        $this->db->where('deleted','n');
        $this->db->order_by('hostname', 'asc');
        $sql = $this->db->get("servers");
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        } else {
            return null;
        }
    }

    public function stats_data($where, $limit = null) {
        if (is_array($where)) {
            $this->db->where($where);
        }
        if (!is_null($limit)) {
            if (is_array($limit)) {
                if (isset($limit['start']) && isset($limit['limit'])) {
                    $this->db->limit($limit['start'], $limit['limit']);
                } else {
                    $this->db->limit($limit[0], $limit[1]);
                }
            } else {
                $this->db->limit($limit);
            }
        } else {
            $this->db->limit(100);
        }
        $this->db->order_by('id', 'desc');
        $sql = $this->db->get("server_data");
        if ($sql->num_rows() > 0) {
            return $sql->result_array();
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

    public function get_name($server_id) {
        $this->db->where('id',$server_id);
        $sql = $this->db->get("servers");
        if ($sql->num_rows() > 0) {
            $tmp = $sql->row_array();
            return $tmp['hostname'];
        } else {
            return 'Unknown Server';
        }
    }
} 