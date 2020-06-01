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

// Temp Banned IPs
$ip = $_SERVER['REMOTE_ADDR'];
// Don't allow IPs in source control
$temp_banned_ips = array();
if (in_array($ip, $temp_banned_ips)) {
    echo "You are temp banned, most likely for using multiple accounts in an unfair way. Please email me a handdrawn picture of a dinosaur to goosepostbox@gmail.com to reactivate your account. After your account is reactivated I will monitor your behavior and will permaban on continued abuse.";
    die();
}
$perm_banned_ips = array();
if (in_array($ip, $perm_banned_ips)) {
    echo "You are perm banned.";
    die();
}

function dd($value = '') {
    var_dump($value);
    die();
}

// Return if this is dev
function is_dev() {
    if (isset($_SERVER['SERVER_ADDR']) && ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1') ) {
        return true;
    }
    return false;
}

function slugify($str){
    return strtolower(str_replace(' ', '_', $str));
}

// Get JSON POST
function get_json_post($required) {
    $raw_post = file_get_contents('php://input');
    if ($required && !$raw_post) {
        echo api_error_response('no_post', 'POST received was empty.');
        exit();
    }
    $json_post = json_decode($raw_post);
    if ($required && !$json_post) {
        echo api_error_response('post_is_not_json', 'POST must be formatted as JSON.');
        exit();
    }
    return $json_post;
}

// API Error JSON Response
function api_error_response($error_code, $error_message) {
    log_message('error', $error_code . ' - ' . $error_message);
    $data['error'] = true;
    $data['error_code'] = $error_code;
    $data['error_message'] = $error_message;
    echo json_encode($data);
    exit();
}

// API Data JSON Response
function api_response($data = array()) {
    $data['error'] = false;
    $data['success'] = true;
    // Encode and send data
    function filter(&$value) {
        if (is_string($value)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $value = nl2br($value);
        }
    }
    array_walk_recursive($data, "filter");
    echo json_encode($data);
    exit();
}

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

function force_ssl() {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") {
        $url = "https://". $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        redirect($url);
        exit;
    }
}

// Get sql connection, used for escaping strings
function get_mysqli() { 
    $db = (array)get_instance()->db;
    return mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);
}

function escape_quotes(&$value) {
    $value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

// https://www.php.net/manual/en/function.shuffle.php#94697
function shuffle_assoc(&$array) { 
    $keys = array_keys($array);
    shuffle($keys);
    foreach($keys as $key) {
        $new[$key] = $array[$key];
    }
    $array = $new;
    return true;
}

function sanitize_html($html) {
    // Content input allow only gmail whitelisted tags
    $whitelisted_tags = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
    $html = strip_tags($html, $whitelisted_tags);
    // Disallow these character combination to prevent potential javascript injection
    $disallowed_strings = ['onerror', 'onload', 'onclick', 'ondblclick', 'onkeydown', 'onkeypress', 'onkeyup', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup'];
    $html = str_replace($disallowed_strings, '', $html);
    // Close open tags
    preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
    $openedtags = $result[1];
    preg_match_all('#</([a-z]+)>#iU', $html, $result);
    $closedtags = $result[1];
    $len_opened = count($openedtags);
    if (count($closedtags) == $len_opened) {
        return $html;
    }
    $openedtags = array_reverse($openedtags);
    for ($i=0; $i < $len_opened; $i++) {
        if (!in_array($openedtags[$i], $closedtags)) {
            $html .= '</'.$openedtags[$i].'>';
        } 
        else {
            unset($closedtags[array_search($openedtags[$i], $closedtags)]);
        }
    }
    // Replace new lines with break tags
    $html = preg_replace("/\r\n|\r|\n/",'<br/>',$html);
    // Return result
    return $html;
}

function get_percent_of($number, $percentage) {
    return ceil( $number * ($percentage / 100) );
}

function generate_popover($title, $content, $placement, $classes = '') {
    return '
        <button type="button" class="popover_tip btn btn-xs btn-default ' . $classes . '" tabindex="0" data-toggle="popover" data-html="true" data-container="body" data-placement="' . $placement . '" data-trigger="focus" title="' . $title . '" 
        data-content="' . $content . '">
            <span class="fa fa-question" title="More Info"></span>
        </button>
    ';
}

// For human readable spans of time
// http://stackoverflow.com/questions/2915864/php-how-to-find-the-time-elapsed-since-a-date-time
function get_time_ago($time_stamp) {
    $time_difference = strtotime('now') - $time_stamp;
    if ($time_difference >= 60 * 60 * 24 * 365.242199) {
        return get_time_ago_string($time_stamp, 60 * 60 * 24 * 365.242199, 'year');
    }
    else if ($time_difference >= 60 * 60 * 24 * 30.4368499) {
        return get_time_ago_string($time_stamp, 60 * 60 * 24 * 30.4368499, 'month');
    }
    else if ($time_difference >= 60 * 60 * 24 * 7) {
        return get_time_ago_string($time_stamp, 60 * 60 * 24 * 7, 'week');
    }
    else if ($time_difference >= 60 * 60 * 24) {
        return get_time_ago_string($time_stamp, 60 * 60 * 24, 'day');
    }
    else if ($time_difference >= 60 * 60) {
        return get_time_ago_string($time_stamp, 60 * 60, 'hour');
    }
    else {
        return get_time_ago_string($time_stamp, 60, 'minute');
    }
}
function get_time_ago_string($time_stamp, $divisor, $time_unit) {
    $time_difference = strtotime("now") - $time_stamp;
    $time_units      = floor($time_difference / $divisor);
    settype($time_units, 'string');
    if ($time_units === '0') {
        return 'less than 1 ' . $time_unit . ' ago';
    }
    else if ($time_units === '1') {
        return '1 ' . $time_unit . ' ago';
    }
    else {
        return $time_units . ' ' . $time_unit . 's ago';
    }
}

// http://stackoverflow.com/a/5727346/3774582
// Parse CRON frequency
// Break it down like James Brown
function parse_crontab($time, $crontab) {
    // Get current minute, hour, day, month, weekday
    $time = explode(' ', date('i G j n w', strtotime($time)));
    // Split crontab by space
    $crontab = explode(' ', $crontab);
    // Foreach part of crontab
    foreach ($crontab as $k => &$v) {
        // Remove leading zeros to prevent octal comparison, but not if number is already 1 digit
        $time[$k] = preg_replace('/^0+(?=\d)/', '', $time[$k]);
        // 5,10,15 each treated as seperate parts
        $v = explode(',', $v);
        // Foreach part we now have
        foreach ($v as &$v1) {
            // Do preg_replace with regular expression to create evaluations from crontab
            $v1 = preg_replace(
                // Regex
                array(
                    // *
                    '/^\*$/',
                    // 5
                    '/^\d+$/',
                    // 5-10
                    '/^(\d+)\-(\d+)$/',
                    // */5
                    '/^\*\/(\d+)$/'
                ),
                // Evaluations
                // trim leading 0 to prevent octal comparison
                array(
                    // * is always true
                    'true',
                    // Check if it is currently that time, 
                    $time[$k] . '===\0',
                    // Find if more than or equal lowest and lower or equal than highest
                    '(\1<=' . $time[$k] . ' and ' . $time[$k] . '<=\2)',
                    // Use modulus to find if true
                    $time[$k] . '%\1===0'
                ),
                // Subject we are working with
                $v1
            );
        }
        // Join 5,10,15 with `or` conditional
        $v = '(' . implode(' or ', $v) . ')';
    }
    // Require each part is true with `and` conditional
    $crontab = implode(' and ', $crontab);
    // Evaluate total condition to find if true
    return eval('return ' . $crontab . ';');
}