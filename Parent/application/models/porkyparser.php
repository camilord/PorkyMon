<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28/03/14
 * Time: 2:01 PM
 */

class porkyParser extends CI_Model{

    public function __construct() {
        parent::__construct();
    }

    /*
     * http://www.kirupa.com/forum/showthread.php?339124-Check-if-server-is-online
     * author: Swooter
     */
    public function server_online($domain){
        $starttime = microtime(true);
        $file = fsockopen ($domain, 80, $errno, $errstr, 10);
        $stoptime = microtime(true);
        $status = 0;

        if (!$file) {
            $status = -1;
        } else {
            $status = ($stoptime - $starttime) * 1000;
            $status = floor($status);
        }
        fclose($file);
        return $status;
    }

} 