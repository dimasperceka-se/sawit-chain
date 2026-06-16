<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| Database connection for the /api app (Docker-aware)
| -------------------------------------------------------------------
| Values come from the container environment (see docker/.env). When run
| outside Docker the fallbacks after ?: are used.
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = getenv('DB_HOST')     ?: '127.0.0.1';
$db['default']['port']     = getenv('DB_PORT')     ?: 3306;
$db['default']['username'] = getenv('DB_USER')     ?: 'palmoiltrace';
// NOTE: this app's DB driver hex-decodes the password (pack("H*", ...) in
// api/system/database/DB_driver.php), so the stored value must be bin2hex'd.
$db['default']['password'] = bin2hex(getenv('DB_PASSWORD') ?: 'palmoiltrace');
$db['default']['database'] = getenv('DB_NAME')     ?: 'palmoiltrace_demo';
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = (getenv('APP_ENV') === 'production') ? FALSE : TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8mb4';
$db['default']['dbcollat'] = 'utf8mb4_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./api/application/config/database.php */
