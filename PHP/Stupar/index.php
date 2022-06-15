<?php

/* Get current time to calculate page loading time */
define('START_PAGE_LOADING_TIMESTAMP_US', microtime(true));

require_once "./core/config.php";
require_once "./appl/config.php";
require_once CORE_INCLUDE_PATH . '/Router.php';

# Indicator that ROOT router is included
define('ROUTER_INCLUDED', true);

# Read path
define('CURR_PATH', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/"));

# Route API request to receiver
$ROUTER = new Phroute\Phroute\RouteCollector();

# Include core routes
include_once CORE_BASE_PATH . "/_core_routes.php";

# Include application specific routes
include_once APPL_BASE_PATH . "/_appl_routes.php";

$ROUTER->get("/test", static function()
{
    exit(require "test.php");
});

try
{
    exit((new Phroute\Phroute\Dispatcher($ROUTER->getData()))->dispatch($_SERVER['REQUEST_METHOD'], CURR_PATH));
}
catch (Phroute\Phroute\Exception\HttpRouteNotFoundException $e)
{
    http_response_code(404);
    die("Not found");
}
catch (Exception $e)
{
    http_response_code(500);
    die("Not found");
}

