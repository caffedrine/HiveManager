<?php

# Enable debug mode
define('DEBUG', true);
define('TPL_DEBUG', false);
define('SYS_LOG_PRINT', false);

# Enable global message when entire website is down for maintenance
define('GLOBAL_DOWN_FOR_MAINTENANCE', false);

# Require PHP > 7.3
if (PHP_VERSION_ID < 70300)
{
    die("Required PHP >= 7.3");
}

if (defined('DEBUG') && (DEBUG === true) )
{
    /** Temporary while debugging */
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    # For xdebug to display multiple data
    //    ini_set('xdebug.var_display_max_depth', '10');
    //    ini_set('xdebug.var_display_max_children', '256');
    //    ini_set('xdebug.var_display_max_data', '1024');

    ini_set("xdebug.var_display_max_children", '-1');
    ini_set("xdebug.var_display_max_data", '-1');
    ini_set("xdebug.var_display_max_depth", '-1');
}

# Define current template
define('ACTIVE_TEMPLATE_NAME', "default");

# Enable database queries debugging - do not enable this in production!!!
define('DB_QUERY_STATS', false);

if( defined('GLOBAL_DOWN_FOR_MAINTENANCE') && GLOBAL_DOWN_FOR_MAINTENANCE )
{
    die("Down for maintenance. Please come back later");
}