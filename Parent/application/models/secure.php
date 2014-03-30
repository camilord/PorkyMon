<?php
/**
 * Created by PhpStorm.
 * User: camilord
 * Date: 3/30/14
 * Time: 6:11 AM
 */

class Secure extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function clean($val, $opt = '') {
        switch($opt) {
            case 'numeric':
                return preg_replace( '/[^0-9]/', '', $val);
                break;
            case 'decimal':
                return preg_replace( '/[^0-9.]/', '', $val);
                break;
            case 'email':
                return preg_replace( '/[^a-zA-Z0-9._\-@]/', '', $val);
                break;
            case 'text':
                return preg_replace( '/[^a-zA-Z0-9._\-: ]/', '', $val);
                break;
            case 'text_strict':
                return preg_replace( '/[^a-zA-Z0-9]/', '', $val);
                break;
            case 'login':
                return preg_replace( '/[^a-zA-Z0-9._]/', '', $val);
                break;
            case 'slugger':
                return preg_replace( '/[^a-zA-Z0-9_\- ]/', '', $val);
                break;
            default:
                return $val;
                break;
        }
    }

    public function generate_code($maxChars = 8) {
        $possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        $i = 0;

        while ($i < $maxChars) {
            $code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
            $i++;
        }

        return $code;
    }

    public function encrypt($val) {
        $porkey = trim($this->config->item('porkey'));
        return (strlen($porkey) > 0) ? sha1(md5($val.$porkey)) : sha1(md5($val));
    }

} 