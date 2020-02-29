<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Marketing routes
$route['world/test'] = "game/index/1/test";

// World load
$route['world/(:any)'] = "game/index/$1";
$route['world/leaderboards/(:any)'] = "game/leaderboards/$1";

// Game functions
$route['get_single_tile'] = "game/get_single_tile";
$route['tile_form'] = "game/tile_form";
$route['tile_upgrade_form'] = "game/tile_upgrade_form";
$route['tile_capitol_form'] = "game/tile_capitol_form";
$route['budget_form'] = "game/budget_form";

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