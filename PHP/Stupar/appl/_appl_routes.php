<?php
/* Provide application paths */
if (!defined("INTERNAL_INCLUSION")) require_once "./core/config.php";

abstract class APPL_ROUTES
{
}

if( !isset($ROUTER) )
    return;

#    _______  _______ _____ ____  _   _
#   | ____\ \/ /_   _| ____|  _ \| \ | |
#   |  _|  \  /  | | |  _| | |_) |  \| |
#   | |___ /  \  | | | |___|  _ <| |\  |
#   |_____/_/\_\ |_| |_____|_| \_\_| \_|
#
/** @route /careers */
//$ROUTER->get(APPL_ROUTES::CAREERS, static function() { exit(require APPL_BASE_PATH . "/careers.php"); });