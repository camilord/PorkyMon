<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 2:01 PM
 */

class porky extends CI_Model{

    private $_server_data = null;
    private $_1k_block = true;

    public function __construct() {
        parent::__construct();
    }

    /*
     * Chris Jester-Young
     * http://stackoverflow.com/questions/2510434/format-bytes-to-kilobytes-megabytes-gigabytes
     */
    function bytes2size($bytes, $precision = 2, $ceil = false) {
        if ($this->_1k_block) {
            $bytes = $bytes * 1024;
        }
        $base = log($bytes) / log(1024);
        $suffixes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        if ($ceil) {
            return ceil(round(pow(1024, $base - floor($base)), $precision)) . $suffixes[floor($base)];
        } else {
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }
    }

    // setters ----------------------------------------------------------------
    public function set($data) {
        $this->_server_data = $data;
    }
    public function set_1k_block($is_it) {
        $this->_1k_block = $is_it;
    }

    // getters ----------------------------------------------------------------
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

    public function get_arc() {
        if (preg_match("/x86_64/", $this->_server_data['kernel'])) {
            return '64bit Architecture';
        } else {
            return '32bit Architecture';
        }
    }

    public function get_kernel() {
        $kernel = explode(" ",$this->_server_data['kernel']);
        if (count($kernel) > 1) {
            return $kernel[0].' '.substr($kernel[1],0,strpos($kernel[1], '-'));
        } else {
            return 'Unknown';
        }
    }

    public function get_memory() {
        if (isset($this->_server_data['memory'])) {
            $tmp = explode("\n", $this->_server_data['memory']);
            $tmp = explode(' ', preg_replace('/\s\s+/', ' ', trim($tmp[1])));
            return array(
                'total' => $tmp[1],
                'used' => $tmp[2],
                'free' => $tmp[3],
                'shared' => $tmp[4],
                'buffers' => $tmp[5],
                'cached' => $tmp[6],
                'time' => $this->_server_data['time'],
                'received' => $this->_server_data['received']
            );
        } else {
            return null;
        }
    }

    public function get_hdd() {
        if (isset($this->_server_data['hdd'])) {
            $tmp = explode("\n", $this->_server_data['hdd']);
            $i = 1;
            $hdd_data = null;
            while($i < count($tmp)) {
                $hdd = explode(' ', preg_replace('/\s\s+/', ' ', trim($tmp[$i])));
                if (is_array($hdd) && count($hdd) >= 5) {
                    if (count($hdd) == 5) {
                        array_unshift($hdd, 'disk');
                    }
                    if (is_null($hdd_data)) {
                        $hdd_data = $hdd;
                    } else {
                        if (count($hdd_data) == count($hdd)) {
                            for ($j = 1; $j < count($hdd_data); $j++) {
                                if (is_numeric($hdd[$j])) {
                                    $hdd_data[$j] += $hdd[$j];
                                }
                            }
                        }
                    }
                }
                $i++;
            }
            return array(
                'disk' => $hdd_data[0],
                'total' => $hdd_data[1],
                'used' => $hdd_data[2],
                'free' => $hdd_data[3],
                'used_percent' => $hdd_data[4],
                'mount' => $hdd_data[5],
                'time' => $this->_server_data['time'],
                'received' => $this->_server_data['received']
            );
        } else {
            return null;
        }
    }

    public function get_memory_data($server_data) {
        if (is_array($server_data)) {
            $parsed_data = array();
            foreach ($server_data as $data) {
                $this->set($data);
                $parsed_data[] = $this->get_memory();
            }
            return $parsed_data;
        } else {
            return 'No data to present...';
        }
    }

    public function get_hdd_data($server_data) {
        if (is_array($server_data)) {
            $parsed_data = array();
            foreach ($server_data as $data) {
                $this->set($data);
                $parsed_data[] = $this->get_hdd();
            }
            return $parsed_data;
        } else {
            return 'No data to present...';
        }
    }

} 