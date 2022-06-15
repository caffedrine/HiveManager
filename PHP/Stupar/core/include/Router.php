<?php

/* Provide application paths */
if (!defined("INTERNAL_INCLUSION")) require_once $_SERVER['DOCUMENT_ROOT'] . "/core/config.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Route.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/RouteDataProviderInterface.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/RouteParser.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/RouteCollector.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Exception/HttpException.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Exception/HttpRouteNotFoundException.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Exception/BadRouteException.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Exception/HttpMethodNotAllowedException.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/HandlerResolverInterface.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/HandlerResolver.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/RouteDataInterface.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/RouteDataArray.php";
require_once CORE_LIBS_PATH . "/PHP/phroute/src/Phroute/Dispatcher.php";
