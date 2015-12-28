<?php

// Local base URL
$base_url = 'http://localhost/landgrab/';

// Route
$route = 'cron/';

// Token
// This variable to be changed in live version
$cron_token = '1234';


echo file_get_contents($base_url . $route . $cron_token);