<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 2:01 PM
 */

class porky extends CI_Model{

    private $_server_data = null;

    public function __construct() {
        parent::__construct();
    }

    public function set($data) {
        $this->_server_data = $data;
    }

    public function get_os() {
        if (isset($this->_server_data['lsb'])) {
            if (preg_match("/debian/", strtolower($this->_server_data['lsb']))) {
                //preg_match_all("~([\"''])([^\"'']+)\1~", $this->_server_data['lsb'], $rtn_data);
                preg_match_all('/(\w+)\s*=\s*(["\'])((?:(?!\2).)*)\2/', $this->_server_data['lsb'], $result, PREG_SET_ORDER);
                if (isset($result[0][3])) {
                    return $result[0][3];
                } else {
                    return 'Debian';
                }
            } else if (preg_match("/centos/", strtolower($this->_server_data['lsb']))) {
                $tmp = explode("\n",$this->_server_data['lsb']);
                return trim(str_replace('release','',$tmp[0]));
            } else {
                // future linux release data...
                return 'Unknown OS';
            }
        } else {
            return 'Unknown OS';
        }
    }

} 