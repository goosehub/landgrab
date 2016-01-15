<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Marketing routes
$route['world/4chan'] = "game/index/3";
$route['world/s4s'] = "game/index/3";
$route['world/b'] = "game/index/3";
$route['world/int'] = "game/index/3";
$route['world/pol'] = "game/index/3";
$route['world/biz'] = "game/index/3";

// World load
$route['world/(:any)'] = "game/index/$1";

// Game functions
$route['get_single_land'] = "game/get_single_land";
$route['land_form'] = "game/land_form";

// Account functions
$route['user/update_color'] = "user/update_color";

// User functions
$route['user/login'] = "user/login";
$route['user/register'] = "user/register";
$route['user/login'] = "user/login";
$route['user/logout'] = "user/logout";


// Token
$route['tax/(:any)'] = "tax/index/$1";

// Not found
$route['404_override'] = 'user/page_not_found';

// Setting
$route['translate_uri_dashes'] = FALSE;