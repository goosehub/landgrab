<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 
// Cron Token, for verifying cron actions
// 

// Keys
// Settlements
define('UNCLAIMED_KEY', 1);
define('TOWN_KEY', 3);
define('CITY_KEY', 4);
define('METRO_KEY', 5);
// Governments
define('DEMOCRACY_KEY', 1);
define('OLIGARCHY_KEY', 2);
define('AUTOCRACY_KEY', 3);
define('ANARCHY_KEY', 4);
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
// Supply
define('SUPPORT_KEY', 2);
DEFINE('POPULATION_KEY', 3);
DEFINE('TILES_KEY', 4);
// Industry
DEFINE('CAPITOL_INDUSTRY_KEY', 1);
// Unit types
DEFINE('INFANTRY_KEY', 1);
DEFINE('GUERRILLA_KEY', 2);
DEFINE('COMMANDOS_KEY', 3);

// World generation
DEFINE('LOWEST_LAT_RESOURCE_GEN', -60);

// Terrain Colors
DEFINE('FERTILE_COLOR', '#758E4F');
DEFINE('BARREN_COLOR', '#EA526F');
DEFINE('MOUNTAIN_COLOR', '#F26419');
DEFINE('TUNDRA_COLOR', '#FFFFFF');
DEFINE('COASTAL_COLOR', '#A5FFD6');
DEFINE('OCEAN_COLOR', '#33658A');

// Units UI
DEFINE('UNIT_VALID_SQUARE_COLOR', '#00FFFF');

// Map
DEFINE('DEFAULT_MAP', 'paper_map');
DEFINE('USE_BORDERS', TRUE);
DEFINE('DEFAULT_UNIT_TOGGLE', TRUE);
DEFINE('DEFAULT_GRID_TOGGLE', FALSE);
DEFINE('TILE_OPACITY', 0.5);
DEFINE('STROKE_WEIGHT', 0.1);
DEFINE('STROKE_COLOR', '#222222');

// Account Defaults
define('DEFAULT_GOVERNMENT', 2);
define('DEFAULT_TAX_RATE', 15);
define('DEFAULT_IDEOLOGY', 1);

// Polling
define('MAP_UPDATE_INTERVAL_MS', 30 * 1000);
define('ACCOUNT_UPDATE_INTERVAL_MS', 60 * 1000);
define('LEADERBOARD_UPDATE_INTERVAL_M', 10);

// Marketing
define('ENABLE_FACEBOOK', false);

// Use for emergencies
define('MAINTENANCE', false);

// This variable to be changed for live version
define('CRON_TOKEN', '1234');

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
