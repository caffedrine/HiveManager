<?php

/** Include global configuration as well */
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/config.php";

define('APPL_PROVIDERS_PATH',     APPL_BASE_PATH . '/providers');
define('APPL_DATABASE_PATH',      APPL_BASE_PATH . '/include/database');
define('APPL_INCLUDE_PATH',       APPL_BASE_PATH . '/include');
define('APPL_CONSOLE_PATH',       APPL_BASE_PATH . RELATIVE_PATH_CONSOLE);
define('APPL_ADMIN_PATH',         APPL_BASE_PATH . RELATIVE_PATH_ADMIN);
define('APPL_SETTINGS_PATH',      APPL_BASE_PATH . '/include/settings');
define('APPL_CRONS_PATH',         APPL_BASE_PATH . RELATIVE_PATH_CRONS);




