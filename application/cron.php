<?php

// Local base URL
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    $base_url = 'http://localhost/landgrab/';
}
else {
    $base_url = 'http://landgrab.xyz/';
}

// Token
// This variable to be changed in live version
$cron_token = '1234';

// Taxes
$route = 'cron/';
echo file_get_contents($base_url . $route . $cron_token);