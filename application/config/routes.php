<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "game";

$route['ajax'] = "game/ajax";
$route['ajax/(:any)'] = "game/ajax";

$route['404_override'] = 'game';