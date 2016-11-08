<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Marketing routes
$route['world/4chan'] = "game/index/5/4chan";
$route['world/s4s'] = "game/index/5/s4s";
$route['world/b'] = "game/index/5/b";
$route['world/v'] = "game/index/5/v";
$route['world/int'] = "game/index/5/int";
$route['world/pol'] = "game/index/5/pol";
$route['world/biz'] = "game/index/5/biz";
$route['world/webgames'] = "game/index/5/webgames";

// World load
$route['world/(:any)'] = "game/index/$1";

// Game functions
$route['get_single_land'] = "game/get_single_land";
$route['land_form'] = "game/land_form";
$route['land_upgrade_form'] = "game/land_upgrade_form";

// Update functions
$route['get_army_update'] = "game/get_army_update";

// Account functions
$route['user/update_account_info'] = "user/update_account_info";

// User functions
$route['user/login'] = "user/login";
$route['user/register'] = "user/register";
$route['user/login'] = "user/login";
$route['user/logout'] = "user/logout";

// Chat functions
$route['chat/load'] = "chat/load";
$route['chat/new_chat'] = "chat/new_chat";

// Cron
$route['cron/(:any)'] = "cron/index/$1";

// Not found
$route['404_override'] = 'user/page_not_found';

// Setting
$route['translate_uri_dashes'] = FALSE;