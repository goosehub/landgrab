<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Debug
define('ALLOW_TERRAIN_UPDATE', TRUE);

// Polling
define('MAP_UPDATE_INTERVAL_MS', 5 * 1000);
define('ACCOUNT_UPDATE_INTERVAL_MS', 1 * 1000);
define('LEADERBOARD_UPDATE_INTERVAL_M', 10);

// Combat
DEFINE('COMBAT_ACCURACY', 10);
DEFINE('BARREN_OFFENSIVE_BONUS', 1);
DEFINE('TUNDRA_DEFENSIVE_BONUS', 1);
DEFINE('MOUNTAIN_DEFENSIVE_BONUS', 1);
DEFINE('TOWN_DEFENSIVE_BONUS', 1);
DEFINE('CITY_DEFENSIVE_BONUS', 2);
DEFINE('METRO_DEFENSIVE_BONUS', 3);
// Township Costs
DEFINE('TOWN_FOOD_COST', 2);
DEFINE('CITY_FOOD_COST', 5);
DEFINE('METRO_FOOD_COST', 20);
DEFINE('TOWN_CASH_CROPS_COST', 0);
DEFINE('CITY_CASH_CROPS_COST', 2);
DEFINE('METRO_CASH_CROPS_COST', 5);
DEFINE('TOWN_ENERGY_COST', 2);
DEFINE('CITY_ENERGY_COST', 5);
DEFINE('METRO_ENERGY_COST', 20);
DEFINE('TOWN_MERCHANDISE_COST', 0);
DEFINE('CITY_MERCHANDISE_COST', 2);
DEFINE('METRO_MERCHANDISE_COST', 5);
DEFINE('TOWN_STEEL_COST', 0);
DEFINE('CITY_STEEL_COST', 0);
DEFINE('METRO_STEEL_COST', 2);
DEFINE('TOWN_PHARMACEUTICALS_COST', 0);
DEFINE('CITY_PHARMACEUTICALS_COST', 0);
DEFINE('METRO_PHARMACEUTICALS_COST', 2);
DEFINE('FEDERAL_CASH_COST', 10);
DEFINE('BASE_CASH_COST', 10);
DEFINE('EDUCATION_CASH_COST', 10);
DEFINE('PHARMACEUTICALS_CASH_COST', 10);
// Population
DEFINE('TOWN_POPULATION_INCREMENT', 50);
DEFINE('CITY_POPULATION_INCREMENT', 50);
DEFINE('METRO_POPULATION_INCREMENT', 50);
// Support Bonus
DEFINE('BASE_SUPPORT_BONUS', 2);
// Gdp bonus
DEFINE('PORT_BONUS', 10);
DEFINE('MACHINERY_BONUS', 10);
DEFINE('AUTOMOTIVE_BONUS', 10);
DEFINE('AEROSPACE_BONUS', 10);
DEFINE('ENTERTAINMENT_BONUS', 30);
DEFINE('FINANCIAL_BONUS', 30);
// Laws
define('DEMOCRACY_SUPPORT_REGEN', 1);
define('OLIGARCHY_SUPPORT_REGEN', 3);
define('AUTOCRACY_SUPPORT_REGEN', 4);
define('MAX_TAX_RATE', 50);
define('DEMOCRACY_SOCIALISM_MAX_SUPPORT', 200);
define('OLIGARCHY_SOCIALISM_MAX_SUPPORT', 150);
define('AUTOCRACY_SOCIALISM_MAX_SUPPORT', 100);
DEFINE('TILES_PER_CORRUPTION_PERCENT', 5);
// Support use
DEFINE('SUPPORT_COST_MOVE_UNIT', 1);
DEFINE('SUPPORT_COST_CAPTURE_LAND', 5);
DEFINE('SUPPORT_COST_CAPTURE_TOWN', 10);
DEFINE('SUPPORT_COST_CAPTURE_CITY', 20);
DEFINE('SUPPORT_COST_CAPTURE_METRO', 40);
DEFINE('SUPPORT_COST_DECLARE_WAR', 50);

// World generation
DEFINE('LOWEST_LAT_RESOURCE_GEN', -60);
DEFINE('TILE_SIZE', 2);
DEFINE('BARREN_BIAS', 4);
DEFINE('TUNDRA_BIAS', 6);

// Terrain Colors
DEFINE('FERTILE_COLOR', '#758E4F');
DEFINE('BARREN_COLOR', '#EA526F');
DEFINE('MOUNTAIN_COLOR', '#F26419');
DEFINE('TUNDRA_COLOR', '#FFFFFF');
DEFINE('COASTAL_COLOR', '#A5FFD6');
DEFINE('OCEAN_COLOR', '#33658A');
DEFINE('FERTILE_TEXT_COLOR', '#758E4F');
DEFINE('BARREN_TEXT_COLOR', '#BF1836');
DEFINE('MOUNTAIN_TEXT_COLOR', '#F26419');
DEFINE('TUNDRA_TEXT_COLOR', '#000000');
DEFINE('COASTAL_TEXT_COLOR', '#5FB6B8');

// Units UI
DEFINE('UNIT_VALID_SQUARE_COLOR', '#00FFFF');
DEFINE('SELECTED_SQUARE_COLOR', '#00FFFF');

// Map
DEFINE('DEFAULT_MAP', 'paper_map');
DEFINE('DEFAULT_BORDER_TOGGLE', TRUE);
DEFINE('DEFAULT_RESOURCE_TOGGLE', TRUE);
DEFINE('DEFAULT_SETTLEMENT_TOGGLE', FALSE);
DEFINE('DEFAULT_TOWNSHIP_AND_INDUSTRY_TOGGLE', 1);
DEFINE('DEFAULT_UNIT_TOGGLE', TRUE);
DEFINE('DEFAULT_GRID_TOGGLE', TRUE);
DEFINE('TILE_OPACITY', 0.5);
DEFINE('STROKE_WEIGHT', 0.1);
DEFINE('STROKE_COLOR', '#222222');
DEFINE('MAP_ICON_SIZE', 25);
DEFINE('FOCUS_ZOOM', 6);
DEFINE('DEFAULT_ZOOM', 3);
DEFINE('MAX_ZOOM', 2);

// Account Defaults
define('DEFAULT_POWER_STRUCTURE', 2);
define('DEFAULT_TAX_RATE', 25);
define('DEFAULT_IDEOLOGY', 1);

// Marketing
define('ENABLE_FACEBOOK', false);

// Use for emergencies
define('MAINTENANCE', false);

/*
|--------------------------------------------------------------------------
| Keys
|--------------------------------------------------------------------------
*/

// Settlements
define('UNCLAIMED_KEY', 1);
define('UNINHABITED_KEY', 2);
define('TOWN_KEY', 3);
define('CITY_KEY', 4);
define('METRO_KEY', 5);
// Power Structure
define('DEMOCRACY_KEY', 1);
define('OLIGARCHY_KEY', 2);
define('AUTOCRACY_KEY', 3);
// Ideology
define('FREE_MARKET_KEY', 1);
define('SOCIALISM_KEY', 2);
// Terrain
DEFINE('FERTILE_KEY', 1);
DEFINE('BARREN_KEY', 2);
DEFINE('MOUNTAIN_KEY', 3);
DEFINE('TUNDRA_KEY', 4);
DEFINE('COASTAL_KEY', 5);
DEFINE('OCEAN_KEY', 6);
// Agreement
DEFINE('WAR_KEY', 1);
DEFINE('PEACE_KEY', 2);
DEFINE('PASSAGE_KEY', 3);
// Core Supply
define('CASH_KEY', 1);
define('SUPPORT_KEY', 2);
DEFINE('POPULATION_KEY', 3);
DEFINE('TILES_KEY', 4);
// Supply category key
DEFINE('FOOD_KEY', 69420); // Symbolic key
DEFINE('CASH_CROPS_KEY', 777); // Symbolic key
DEFINE('FOOD_CATEGORY_ID', 2);
DEFINE('CASH_CROPS_CATEGORY_ID', 3);
DEFINE('ENERGY_CATEGORY_ID', 4);
DEFINE('METALS_CATEGORY_ID', 5);
DEFINE('RICHES_CATEGORY_ID', 11);
// Food Supply
DEFINE('GRAIN_KEY', 8);
DEFINE('FRUIT_KEY', 9);
DEFINE('VEGETABLES_KEY', 10);
DEFINE('LIVESTOCK_KEY', 11);
DEFINE('FISH_KEY', 12);
// Cash Crops Supply
DEFINE('COFFEE_KEY', 23);
DEFINE('TEA_KEY', 24);
DEFINE('CANNABIS_KEY', 25);
DEFINE('ALCOHOLS_KEY', 26);
DEFINE('TOBACCO_KEY', 27);
// Other Township Input Supply
DEFINE('ENERGY_KEY', 13);
DEFINE('MERCHANDISE_KEY', 37);
DEFINE('STEEL_KEY', 39);
DEFINE('PHARMACEUTICALS_KEY', 35);
// GDP Bonus Supply keys
DEFINE('PORT_KEY', 41);
DEFINE('MACHINERY_KEY', 42);
DEFINE('AUTOMOTIVE_KEY', 43);
DEFINE('AEROSPACE_KEY', 44);
DEFINE('ENTERTAINMENT_KEY', 45);
DEFINE('FINANCIAL_KEY', 46);
// Industry
DEFINE('CAPITOL_INDUSTRY_KEY', 1);
DEFINE('FEDERAL_INDUSTRY_KEY', 2);
DEFINE('BASE_INDUSTRY_KEY', 3);
DEFINE('EDUCATION_INDUSTRY_KEY', 20);
DEFINE('HEALTHCARE_INDUSTRY_KEY', 22);
// Unit types
DEFINE('INFANTRY_KEY', 1);
DEFINE('TANKS_KEY', 2);
DEFINE('AIRFORCE_KEY', 3);
DEFINE('NAVY_KEY', 4);

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
