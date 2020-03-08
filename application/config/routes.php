<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

// Default to game map
$route['default_controller'] = "game";

// Marketing routes
$route['world/test'] = "game/index/1/test";

// World load
$route['world/(:any)'] = "game/index/$1";

// Cron
$route['cron/(:any)'] = "cron/index/$1";

// Not found
$route['404_override'] = 'user/page_not_found';

// Setting
$route['translate_uri_dashes'] = FALSE;