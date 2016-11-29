<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// CodeIgniter Functions
$autoload['packages'] = array();
$autoload['libraries'] = array('session', 'form_validation');
$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'form');
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array();

// Random color function for generating primary color
function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}
function random_hex_color() {
    return '#' . random_color_part() . random_color_part() . random_color_part();
}

// Time Attack Safe string comparison
function _createHashEquals() {
    if(!function_exists('hash_equals')) {
        function hash_equals($str1, $str2) {
            if(strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                return !$ret;
            }
        }
    }
}

// Get sql connection, used for escaping strings
function get_mysqli() { 
    $db = (array)get_instance()->db;
    return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);
}