<?php
/* Provide application paths */
if (!defined("INTERNAL_INCLUSION")) require_once "./core/config.php";

abstract class APPL_ROUTES
{
    public const VIEW_SENSOR = "view-sensor";
}

if( !isset($ROUTER) )
    return;

#    _______  _______ _____ ____  _   _
#   | ____\ \/ /_   _| ____|  _ \| \ | |
#   |  _|  \  /  | | |  _| | |_) |  \| |
#   | |___ /  \  | | | |___|  _ <| |\  |
#   |_____/_/\_\ |_| |_____|_| \_\_| \_|
#
$ROUTER->get(APPL_ROUTES::VIEW_SENSOR, static function() { exit(require DOC_ROOT . "/view-hive.php"); });