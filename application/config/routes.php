<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Game functions
$route['get_single_grid'] = "game/get_single_grid";
$route['claim_land'] = "game/claim_land";

// User functions
$route['user/login'] = "user/login";
$route['user/register'] = "user/register";
$route['user/login'] = "user/login";
$route['user/logout'] = "user/logout";

// Not found
$route['404_override'] = 'user/page_not_found';