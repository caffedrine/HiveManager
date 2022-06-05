<?php

/** Include global configuration as well */
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/gloabl_cfg.php";

/** Used to indicate whether this was already included by the parent */
define('INTERNAL_INCLUSION',    true);

# Shortcut to document root
define('DOC_ROOT', $_SERVER["DOCUMENT_ROOT"]);

/** Define paths for HTML inclusions */
define('CORE_PATH_LIBS_REL',            '/core/libs');
define('RELATIVE_PATH_CONSOLE',         '/console');
define('RELATIVE_PATH_ADMIN',           '/admin');
define('RELATIVE_PATH_INCLUDE',         '/include');
define('RELATIVE_PATH_CRONS',           '/crons');
define('RELATIVE_PATH_CORE',            '/core');
define('RELATIVE_PATH_APPL',            '/appl');

/** All absolute paths to be used by PHP  */
define('APPL_BASE_PATH',         DOC_ROOT . '/appl');
define('CORE_BASE_PATH',         DOC_ROOT . '/core');
define('MODULES_BASE_PATH',      DOC_ROOT . '/modules');
define('TMP_PATH',               DOC_ROOT . '/tmp');
define('CORE_GLOB_CONFIG_PATH',  DOC_ROOT . '/config');
define('CORE_INCLUDE_PATH',      CORE_BASE_PATH . '/include');
define('CORE_CLASSES_PATH',      CORE_BASE_PATH . '/include/classes');
define('CORE_CRONS_PATH',        CORE_BASE_PATH . RELATIVE_PATH_CRONS);
define('CORE_CONSOLE_PATH',      CORE_BASE_PATH . RELATIVE_PATH_CONSOLE);
define('CORE_ADMIN_PATH',        CORE_BASE_PATH . RELATIVE_PATH_ADMIN);
define('CORE_LIBS_PATH',         CORE_BASE_PATH . '/libs');
define('CORE_DATABASE_PATH',     CORE_BASE_PATH . '/include/database');
define('CORE_SETTINGS_PATH',     CORE_BASE_PATH . '/include/settings');
define('CORE_APIV1_PATH',        CORE_BASE_PATH . "/api/v1");

/** Set active template paths */
define('TPL_PATH_ABS',           DOC_ROOT . "/templates/" . ACTIVE_TEMPLATE_NAME);
define('TPL_PATH_REL',           "/templates/" . ACTIVE_TEMPLATE_NAME);
