<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Game functions
$route['world/(:any)'] = "game/index/$1";
$route['get_single_land'] = "game/get_single_land";
$route['land_form'] = "game/land_form";

// Account functions
$route['account/update_color'] = "account/update_color";

// User functions
$route['user/login'] = "user/login";
$route['user/register'] = "user/register";
$route['user/login'] = "user/login";
$route['user/logout'] = "user/logout";


// Token
$route['cron/(:any)'] = "cron/index/$1";

// Not found
$route['404_override'] = 'user/page_not_found';

// Setting
$route['translate_uri_dashes'] = FALSE;